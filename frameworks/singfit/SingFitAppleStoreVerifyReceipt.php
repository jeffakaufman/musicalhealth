<?php
# -*- coding: utf-8 -*-

//
//  Copyright (C) 2011 Sonoma Wire Works. All rights reserved.
//

require_once dirname(__FILE__).'/SingFitDateTimeUtilities.php';

$__store_errno = 0;

function SingFitAppleStoreGetErrno() {
	global $__store_errno;
	return $__store_errno;
}

function SingFitAppleStoreSetErrno($errnum = 0) {
	global $__store_errno;
	$__store_errno = $errnum;
}

function SingFitAppleStoreFieldDescription($field = 0) {
	$desc = null;
	switch($field) {
		case "quantity":
			$desc = "The number of items purchased. This value corresponds to the quantity property";
			$desc .= " of the SKPayment object stored in the transaction's payment property, this is a integer length unknown.";
		break;
		case "product_id":
			$desc = "The product identifier of the item that was purchased. This value corresponds to the productIdentifier";
			$desc .= " property of the SKPayment object stored in the transaction's payment property, indeed this is a string";
		break;
		case "transaction_id":
			$desc = "The transaction identifier of the item that was purchased. This value corresponds";
			$desc .= " to the transaction's transactionIdentifier property, this is a integer length unknown.";
		break;
		case "purchase_date":
			$desc = "The date and time this transaction occurred. This value corresponds to the transaction's transactionDate"; 
			$desc .= " property, an non ISO standard date 2011-07-01 22:31:57 Etc/GMT needs to be converted.";
		break;
		case "original_transaction_id":
			$desc = "For a transaction that restores a previous transaction, this holds the original transaction identifier, this is a integer length unknown.";
		break;
		case "original_purchase_date":
			$desc = "For a transaction that restores a previous transaction, this holds the original purchase date.";
		break;
		case "app_item_id":
			$desc = "A string that the App Store uses to uniquely identify the iOS application that created the payment transaction.";
			$desc .=" If your server supports multiple iOS applications, you can use this value to differentiate between them. Applications that";
			$desc .=" are executing in the sandbox do not yet have an app-item-id assigned to them, so this key is missing from receipts created by the sandbox.";
		break;
		case "version_external_identifier":
			$desc = "An arbitrary number that uniquely identifies a revision of your application. This key is missing in receipts created by the sandbox.";
		break;
		case "bid":
			$desc = "The bundle identifier for the iOS application, indeed this is a string";
		break;
		case "bvrs":
			$desc = "A version number for the iOS application. indeed this is a string (1.0).";
		break;
		case "item_id": // discovery channel not in the doc
			$desc = "Not in the doc unknown, this is a integer length unknown.";
		break;
		case "expires_date": // discovery channel not in the doc
			$desc = "this is indeed an Unix Timestamp in milliseconds...";
		break;
		case "expires_date_formatted": // discovery channel not in the doc
			$desc = "an non ISO standard date 2011-07-01 22:31:57 Etc/GMT needs to be converted.";
		break;
		default:
		break;
	}
	return $desc;
}

function SingFitAppleStoreErrorString($code = 0) {
	$error = null;
	if ($code == 0) {
		return null;
	}
	switch($code) {
		case 21000:
			$error = "The App Store could not read the JSON object you provided.";
		break;
		case 21002:
			$error = "The data in the receipt-data property was malformed.";
		break;
		case 21003:
			$error = "The receipt could not be authenticated.";
		break;
		case 21004:
			$error = "The shared secret you provided does not match the shared secret on file for your account.";
		break;
		case 21005:
			$error = "The receipt server is not currently available.";
		break;
		case 21006:
			$error = "This receipt is valid but the subscription has expired.";
		break;
		default:
			$error = "Not documented error, base64 the receipt ios side before sending it.";
		break;
	}
	return $error;
}

function __SingFitAppleStoreReceiptResponse($data) {
	SingFitAppleStoreSetErrno(0);
	if (!isset($data['receipt'])) {
		SingFitAppleStoreSetErrno(100006);
		return null;
	}
	$receipt = $data['receipt'];
	if (!is_array($receipt)) {
		SingFitAppleStoreSetErrno(100007);
		return null;
	}	
	$quantity = isset($receipt['quantity']) ? $receipt['quantity'] : 1;
	$product_id = isset($receipt['product_id']) ? $receipt['product_id'] : null;
	$transaction_id = isset($receipt['transaction_id']) ? $receipt['transaction_id'] : null;
	$purchase_date = isset($receipt['purchase_date']) ? SingFitDateTimeUtilitiesNonIsoGMTDateToDateTime($receipt['purchase_date']) : null;
	$expires_date = isset($receipt['expires_date']) ? SingFitDateTimeUtilitiesUnixTimeStampToDateTime($receipt['expires_date'] / 1000) : null;
	$latest_receipt = isset($data['latest_receipt']) ? $data['latest_receipt'] : null;
	$original_transaction_id =  isset($receipt['original_transaction_id']) ? $receipt['original_transaction_id'] : null;
	$original_purchase_date = isset($receipt['original_purchase_date']) ? SingFitDateTimeUtilitiesNonIsoGMTDateToDateTime($receipt['original_purchase_date']) : null;
	$app_item_id = isset($receipt['app_item_id']) ? $receipt['app_item_id'] : null;
	$version_external_identifier = isset($receipt['version_external_identifier']) ? $receipt['version_external_identifier'] : null;
	$bid = isset($receipt['bid']) ? $receipt['bid'] : null;
	$bvrs = isset($receipt['bvrs']) ? $receipt['bvrs'] : null;
	return array(
		'receipt_data' => $data['receipt_data'],
		'receipt' => json_encode($receipt),
		'quantity' => $quantity,
		'product_id' => $product_id,
		'transaction_id' => $transaction_id,
		'purchase_date' => $purchase_date,
		'expires_date' => $expires_date,
		'latest_receipt' => $latest_receipt,
		'original_transaction_id' => $original_transaction_id,
		'original_purchase_date' => $original_purchase_date,
		'app_item_id' => $app_item_id,
		'version_external_identifier' => $version_external_identifier,
		'bid' => $bid,
		'bvrs' => $bvrs
	);
}

function __SingFitAppleStoreFakeVerifyReceipt($receipt, $secret = null) {
	return array(
		'receipt_data' => '',
		'receipt' => '{}',
		'quantity' => 1,
		'product_id' => 1,
		'transaction_id' => 1,
		'purchase_date' => '0000-00-00 00:00:00',
		'expires_date' => '0000-00-00 00:00:00',
		'latest_receipt' => '',
		'original_transaction_id' => 1,
		'original_purchase_date' => '0000-00-00 00:00:00',
		'app_item_id' => '',
		'version_external_identifier' => '',
		'bid' => '',
		'bvrs' => ''
	);
}

function SingFitAppleStoreVerifyReceipt($receipt, $secret = null, $useAppleSandbox = false) {
	SingFitAppleStoreSetErrno(0);
	if ($useAppleSandbox == true) {
		$applehost = 'https://sandbox.itunes.apple.com/verifyReceipt';
	} else {
		$applehost = 'https://buy.itunes.apple.com/verifyReceipt';
	}
	$receipt_data = array('receipt-data' => $receipt);
	if ($secret != null) {
		$receipt_data['password'] =  $secret;
	}
	$post_receipt = json_encode($receipt_data);
	$request = curl_init($applehost);
	curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($request, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($request, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($request, CURLOPT_POST, true);
	curl_setopt($request, CURLOPT_POSTFIELDS, $post_receipt);
	$response = curl_exec($request);
	$errno = curl_errno($request);
	$errmsg = curl_error($request);
	curl_close($request);
	if ($errno != 0) {
		$__store_errno = $errno;
		__SingFitErrorLog(__FUNCTION__, $response);
		return null;
	}
	$data = json_decode($response, true);
	if (!is_array($data)) {
		SingFitAppleStoreSetErrno(100003);
		__SingFitErrorLog(__FUNCTION__, SingFitAppleStoreGetErrno());
		__SingFitErrorLog(__FUNCTION__, $response);
		return null;
	}
	if (!isset($data['status'])) {
		SingFitAppleStoreSetErrno(100004);
		__SingFitErrorLog(__FUNCTION__, SingFitAppleStoreGetErrno());
		__SingFitErrorLog(__FUNCTION__, $response);
		return null;
	}
	if ($data['status'] != 0) {
		SingFitAppleStoreSetErrno($data['status']);
		__SingFitErrorLog(__FUNCTION__, SingFitAppleStoreGetErrno());
		__SingFitErrorLog(__FUNCTION__, $response);
		return null;
	}
	if (!is_array($data['receipt'])) {
		SingFitAppleStoreSetErrno(100005);
		__SingFitErrorLog(__FUNCTION__, SingFitAppleStoreGetErrno());
		__SingFitErrorLog(__FUNCTION__, $response);
		return null;
	}
	$data['receipt_data'] = $receipt;
	return __SingFitAppleStoreReceiptResponse($data);
}

/* EOF */ ?>