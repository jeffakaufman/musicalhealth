<?php
# -*- coding: utf-8 -*-

//
//  Copyright (C) 2011 Sonoma Wire Works. All rights reserved.
//

require_once dirname(__FILE__).'/SingFitDatabaseConnection.php';
require_once dirname(__FILE__).'/SingFitAppleStoreVerifyReceipt.php';

function SingFitStoreUserProductOwned($apple_product_id = null, $userid = 0, $link = false) {
	$product = null;
	_SingFitDataBaseGetCurrentConnection($link);
	if (false !== $link && $apple_product_id != null) {
		// one to one: has product
		$sql = "
			SELECT store_product.id as product_id, store_product.apple_product_secret
			FROM store_user_to_product, store_product
			WHERE store_user_to_product.user_id=".$userid." 
			AND store_product.id = store_user_to_product.product_id 
			AND store_product.apple_product_id='".$apple_product_id."'
		";
		if (false !== ($res = mysql_query($sql, $link))) {
			if (mysql_num_rows($res) == 1) {
				while ($row = mysql_fetch_assoc($res)) {
					$product = $row;
				}
			}
			mysql_free_result($res);
		}
	}
	return $product;
}

function SingFitStoreUserLastTransactionId($productid = 0, $userid = 0, $link = false) {
	$transactionid = 0;
	_SingFitDataBaseGetCurrentConnection($link);
	// one to many: last transaction for product
	// this design is wanted, few query should hit the complete history
	// that is the main reason we have a proxy history table linked with numbers.
	if (false !== $link && $productid > 0) {
		$sql = "
			SELECT MAX(store_user_product_to_apple_transaction.transaction_id) as transaction_id
			FROM store_user_product_to_apple_transaction
			WHERE store_user_product_to_apple_transaction.user_id=".$userid." 
			AND store_user_product_to_apple_transaction.product_id=".$productid."
		";
		if (false !== ($res = mysql_query($sql, $link))) {
			if (mysql_num_rows($res) == 1) {
				while ($row = mysql_fetch_assoc($res)) {
					$transactionid = $row['transaction_id'];
				}
			}
			mysql_free_result($res);
		}
	}
	return $transactionid;
}

function SingFitStoreUserIsSubscriber($request, $link = false, &$expires = 0, &$corporate = false) {
	$bundleid = _kSingFitStoreProductRoot;
	if (isset($request['POST']['clientappid'])) {
		$bundleid = $request['POST']['clientappid'];
	}
	$appinfo = SingFitStoreGetAppInfo($bundleid);
	if (($corporate = $appinfo['corporate'])) {
		$now = time();
		$expires = $appinfo['agreement_time'];
		if ($now > $expires) {
			return false;
		}
		return true;
	}
	_SingFitDataBaseGetCurrentConnection($link);
	$apple_udid = null;
	if (isset($request['POST']['clientidentifier'])) {
		$apple_udid = $request['POST']['clientidentifier'];
	}
	$useAppleSandbox = false;
	if (_kSingFitAppleStoreIsSandBox == false) {
		if (isset($request['POST']['storesandbox'])) {
			$useAppleSandbox = $request['POST']['storesandbox'] == 1 ? true : false;
		}
	}
	if (false !== $link && $apple_udid != null) {
		$userid = SingFitStoreUserIDWithAppleUDID($apple_udid, $link);
		if ($userid > 0) {
			$product = SingFitStoreUserProductOwned(_kSingFitStoreProductMonthlySubscription, $userid, $link);
			if (null != $product) {
				$productid = $product['product_id'];
				$secret = $product['apple_product_secret'];
				if (null != $secret && $productid > 0) {
					$transactionid = SingFitStoreUserLastTransactionId($productid, $userid);
					if ($transactionid > 0) {
						$transaction = SingFitStoreGetTransaction($transactionid);
						$response = SingFitAppleStoreVerifyReceipt($transaction['receipt_data'], $secret, $useAppleSandbox);
						if (null != $response) {
							$now = time();
							$expires = $transaction['expires_time'];
							if ($now > $expires) {
								return false;
							}
							return true;
						}
						//__SingFitErrorLog(
						//	__FUNCTION__,
						//	SingFitAppleStoreGetErrno()
						//);
					}
				}
			}
		}
	}
	return false;
}

function SingFitStoreUserSetProductAndTransactionHistory($productid = 0, $userid = 0, $apple_transaction = null, $link = false, $useAppleSandbox = false) {
	_SingFitDataBaseGetCurrentConnection($link);
	if ($link != false && $productid > 0  && $userid > 0 && $apple_transaction != null) {
		$cannotlog = false;
		$res = false;
		$sql = "
			SELECT user_id  
			FROM store_user_to_product
			WHERE user_id=".$userid."
			AND product_id='".$productid."'
		";
		if (false !== ($res = mysql_query($sql, $link))) {
			$numrows = mysql_num_rows($res);
			mysql_free_result($res);
			if ($numrows == 0) {
				$sql = "INSERT INTO store_user_to_product (id, user_id, product_id) VALUES (NULL, '".$userid."', '".$productid."')";
				if (false === mysql_query($sql, $link)) {
					$cannotlog = true;
				}
			}
		} else {
			$cannotlog = true;
		}
		if (!$cannotlog) {
			$transactionid = SingFitStoreLogAppleTransaction($apple_transaction, $useAppleSandbox);
			if ($transactionid > 0) {
				$sql = "INSERT INTO store_user_product_to_apple_transaction (id, user_id, transaction_id, product_id) VALUES (NULL, '".$userid."', '".$transactionid."', '".$productid."')";
				if (false === mysql_query($sql, $link)) {
					$cannotlog = true;
				}
			}
		}
		return !$cannotlog;
	}
	return false;
}

function SingFitStoreUserIDWithAppleUDID($apple_udid = null, $link = false) {
	$userid = 0;
	$res = false;
	$row = null;
	_SingFitDataBaseGetCurrentConnection($link);
	if (false !== $link && $apple_udid != null) {
		$sql = "
			SELECT store_user.id 
			FROM store_user, store_user_to_apple_udid 
			WHERE store_user.id = store_user_to_apple_udid.user_id 
			AND store_user_to_apple_udid.apple_udid='".$apple_udid."'
		";
		if (false !== ($res = mysql_query($sql, $link))) {
			if (mysql_num_rows($res) != 0) {	
				while ($row = mysql_fetch_assoc($res)) {
					$userid = $row['id'];
				}
			}
			mysql_free_result($res);
		}
	}
	return $userid;
}

function SingFitStoreSetUserWithAppleUDID($apple_udid = null, $apple_original_transaction_id = null, $link = false) {
	$userid = 0;
	$res = false;
	$row = null;
	_SingFitDataBaseGetCurrentConnection($link);
	if (false !== $link && $apple_udid != null) {
		$userid = SingFitStoreUserIDWithAppleUDID($apple_udid, $link);
		if ($userid > 0) {
			return $userid;
		}
		$sql = "INSERT INTO store_user (id, creation) VALUES (NULL, NOW())";
		if (false !== mysql_query($sql, $link)) {
			$userid = mysql_insert_id($link);
			$sql = "INSERT INTO store_user_to_apple_udid (id, user_id, apple_udid) VALUES (NULL , '".$userid."', '".$apple_udid."')";
			if (false !== mysql_query($sql, $link)) {
				return $userid;
			} else {
				$sql = "DELETE FROM store_user_to_apple_udid WHERE apple_udid=".$apple_udid;
				mysql_query($sql, $link);
				$sql = "DELETE FROM store_user WHERE id=".$userid;
				mysql_query($sql, $link);
				return 0;
			}
		}
	}
	return $userid;
}

function SingFitStoreAppleTransactionParseField($data) {
	if (null === $data) {
		return "NULL";
	}
	if (is_int($data) || is_float($data)) {
		return $data;
	}
	$data = mysql_escape_string($data);
	if (
		strtolower(rtrim($data)) == "null" || 
		strtolower(rtrim($data)) == "nil" || 
		strtolower(rtrim($data)) == "none"
	) {
		return "NULL";
	} else {
		return "'".$data."'";
	}
	return "NULL";
}

function SingFitStoreLogAppleTransaction($apple_transaction, $useAppleSandbox = false) {
	$link = false;
	 // restored transaction
	if (is_null($apple_transaction['expires_date']) && $apple_transaction['product_id'] == _kSingFitStoreProductMonthlySubscription) {
		if (!is_null($apple_transaction['latest_receipt'])) {
			$transaction = SingFitStoreGetTransactionWithReceiptData($apple_transaction['latest_receipt']);
			if ($useAppleSandbox == false && $transaction != null) { // overwrite missing expires_date
				$apple_transaction['expires_date'] = $transaction['expires_date'];
			}
		}
	}
	$sql = "
		INSERT INTO store_apple_transaction 
		(
			id, 
			transaction_date, 
			receipt_data, 
			receipt, 
			quantity, 
			product_id, 
			transaction_id, 
			purchase_date, 
			expires_date, 
			latest_receipt, 
			original_transaction_id, 
			original_purchase_date, 
			app_item_id, 
			version_external_identifier, 
			bid, 
			bvrs
		) 
		VALUES (
			NULL, 
			NOW(), 
			".SingFitStoreAppleTransactionParseField($apple_transaction['receipt_data']).", 
			".SingFitStoreAppleTransactionParseField($apple_transaction['receipt']).",  
			".SingFitStoreAppleTransactionParseField($apple_transaction['quantity']).",  
			".SingFitStoreAppleTransactionParseField($apple_transaction['product_id']).",  
			".SingFitStoreAppleTransactionParseField($apple_transaction['transaction_id']).",  
			".SingFitStoreAppleTransactionParseField($apple_transaction['purchase_date']).",  
			".SingFitStoreAppleTransactionParseField($apple_transaction['expires_date']).",  
			".SingFitStoreAppleTransactionParseField($apple_transaction['latest_receipt']).", 
			".SingFitStoreAppleTransactionParseField($apple_transaction['original_transaction_id']).",  
			".SingFitStoreAppleTransactionParseField($apple_transaction['original_purchase_date']).",  
			".SingFitStoreAppleTransactionParseField($apple_transaction['app_item_id']).",  
			".SingFitStoreAppleTransactionParseField($apple_transaction['version_external_identifier']).",  
			".SingFitStoreAppleTransactionParseField($apple_transaction['bid']).", 
			".SingFitStoreAppleTransactionParseField($apple_transaction['bvrs'])."
		)
	";
	if (false !== ($link = SingFitDataBaseConnect())) {
		if (false !== mysql_query($sql, $link)) {
			return mysql_insert_id($link);
		}
		SingFitDataBaseClose($link);
	}
	return 0;
}

function SingFitStoreLogTransactionRequest($request) {
	$link = false;
	$sql = "
		INSERT INTO store_transaction_request 
		(
			id, 
			request_date, 
			apple_product_id,
			apple_udid,
			app_bundle_id,
			forfree,
			language,
			country
		) 
		VALUES (
			NULL, 
			NOW(), 
			".SingFitStoreAppleTransactionParseField($request['POST']['storeproductid']).", 
			".SingFitStoreAppleTransactionParseField($request['POST']['clientidentifier']).", 
			".SingFitStoreAppleTransactionParseField($request['POST']['clientappid']).", 
			".SingFitStoreAppleTransactionParseField($request['POST']['storeaction'] == "forfree" ? 1 : 0).", 
			".SingFitStoreAppleTransactionParseField($request['POST']['clientlanguage']).", 
			".SingFitStoreAppleTransactionParseField($request['POST']['clientcountry'])."
		)
	";
	if (false !== ($link = SingFitDataBaseConnect())) {
		if (false !== mysql_query($sql, $link)) {
			return mysql_insert_id($link);
		}
		SingFitDataBaseClose($link);
	}
	return 0;
}

function SingFitStoreGetTransactionWithReceiptData($data = null) {
	$link = false;
	$transaction = null;
	if (false !== ($link = SingFitDataBaseConnect())) {
		$row = null;
		$res = false;
		$sql = "SELECT *, UNIX_TIMESTAMP(expires_date) AS expires_time FROM store_apple_transaction WHERE store_apple_transaction.receipt_data='".$data."'";
		if (false !== ($res = mysql_query($sql, $link))) {
			if (mysql_num_rows($res) == 1) {
				while ($row = mysql_fetch_assoc($res)) {
					$transaction = $row;
				}
			}
			mysql_free_result($res);
		}
		SingFitDataBaseClose($link);
	}
	return $transaction;
}

function SingFitStoreGetAppInfo($appbundleid = null) {
	$link = false;
	$appinfo = null;
	if (false !== ($link = SingFitDataBaseConnect())) {
		$row = null;
		$res = false;
		$sql = "SELECT *, UNIX_TIMESTAMP(agreement) AS agreement_time FROM store_app WHERE store_app.app_bundle_id='".$appbundleid."'";
		if (false !== ($res = mysql_query($sql, $link))) {
			if (mysql_num_rows($res) == 1) {
				while ($row = mysql_fetch_assoc($res)) {
					$appinfo = $row;
				}
			}
			mysql_free_result($res);
		}
		SingFitDataBaseClose($link);
	}
	return $appinfo;
}

function SingFitStoreGetTransaction($transactionid = 0) {
	$link = false;
	$transaction = null;
	if (false !== ($link = SingFitDataBaseConnect())) {
		$row = null;
		$res = false;
		$sql = "SELECT *, UNIX_TIMESTAMP(expires_date) AS expires_time FROM store_apple_transaction WHERE store_apple_transaction.id=".$transactionid;
		if (false !== ($res = mysql_query($sql, $link))) {
			if (mysql_num_rows($res) == 1) {
				while ($row = mysql_fetch_assoc($res)) {
					$transaction = $row;
				}
			}
			mysql_free_result($res);
		}
		SingFitDataBaseClose($link);
	}
	return $transaction;
}

function SingFitStoreManagerGetProductContent($product_id) {
	
}

function SingFitStoreManagerGetProduct($apple_product_id, $link = false) {
	$link = false;
	$product = null;
	if (false !== ($link = SingFitDataBaseConnect())) {
		$row = null;
		$res = false;
		$sql = "SELECT * FROM store_product WHERE store_product.apple_product_id = '".$apple_product_id."' AND store_product.visible=1";
		if (false !== ($res = mysql_query($sql, $link))) {
			if (mysql_num_rows($res) == 1) {
				$product = array();
				while ($row = mysql_fetch_assoc($res)) {
					$product['product_id'] = $row['id'];
					$product['product_identifier'] = $row['apple_product_id'];
					$product['product_secret'] = $row['apple_product_secret'];
				}
			}
			mysql_free_result($res);
		}
		SingFitDataBaseClose($link);
	}
	return $product;
}

function SingFitStoreManagerProductExists($apple_product_id, $link = false) {
	return (null != SingFitStoreManagerGetProduct($apple_product_id)) ? true : false;
}

function SingFitStoreManagerBuyProduct($request) {	
	$response = array();
	$response['errno'] = 1;
	$response['identifier'] = null;
	$userid = 0;
	$link = false;
	$useAppleSandbox = false;
	if (_kSingFitAppleStoreIsSandBox == false) {
		if (isset($request['POST']['storesandbox'])) {
			$useAppleSandbox = $request['POST']['storesandbox'] == 1 ? true : false;
		}
	}
	$apple_transaction = null;
	if (false !== ($link = SingFitDataBaseConnect())) {
		if (isset($request['POST']['storeproductid'])) {
			$response['errno'] = 300;
			$response['identifier'] = $request['POST']['storeproductid'];
			if (isset($request['POST']['storeaction'])) {
				$response['errno'] = 400;
				if ($request['POST']['storeaction'] == "forfree") {
					$response['errno'] = 500;
					if (false != SingFitStoreManagerProductExists($request['POST']['storeproductid'])) {
						$userid = SingFitStoreSetUserWithAppleUDID($request['POST']['clientidentifier'], null, $link);
						if ($request['POST']['storeproductid'] == _kSingFitStoreProductMonthlySubscription) {
							$response['errno'] = 2;
						} else {
							$response['errno'] = 0;
						}
					}
				} else if ($request['POST']['storeaction'] == "purchased") {
					$response['errno'] = 600;
					if (isset($request['POST']['storereceipt'])) {
						$product = SingFitStoreManagerGetProduct($request['POST']['storeproductid']);
						if (null != $product) {
							$secret = null;
							if ($request['POST']['storeproductid'] == _kSingFitStoreProductMonthlySubscription) {
								$secret = $product['product_secret'];
							}
							$apple_transaction = SingFitAppleStoreVerifyReceipt($request['POST']['storereceipt'], $secret, $useAppleSandbox);
							if ($apple_transaction != null) {
								$userid = SingFitStoreSetUserWithAppleUDID($request['POST']['clientidentifier'], $apple_transaction['original_transaction_id'], $link);
								if ($userid > 0) {
									if (SingFitStoreUserSetProductAndTransactionHistory($product['product_id'], $userid, $apple_transaction, $link, $useAppleSandbox)) {
										$response['errno'] = 0;
									}  else {
										$response['errno'] = 3;
									}
								} else {
									$response['errno'] = 4;
								}
							} else {
								$response['errno'] = 5;
							}
						} else {
							$response['errno'] = 6;
						}
					} else {
						$response['errno'] = 7;
					}
				}
			}
		}
		if ($response['errno'] != 0) {
			__SingFitErrorLog(__FUNCTION__, $apple_transaction);
		} else {
			if (!$useAppleSandbox) {
				SingFitStoreLogTransactionRequest($request);
			}
		}
		SingFitDataBaseClose($link);
	}
	//__SingFitErrorLog(__FUNCTION__, $response);
	return SingFitServicesResponse($response);
}

/* EOF */ ?>