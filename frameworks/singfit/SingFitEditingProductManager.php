<?php
# -*- coding: utf-8 -*-

//
//  Copyright (C) 2011 Sonoma Wire Works. All rights reserved.
//

require_once dirname(__FILE__).'/SingFitCommonSettings.php';
require_once dirname(__FILE__).'/SingFitDatabaseConnection.php';
require_once dirname(__FILE__).'/SingFitMakeUUID.php';

function SingFitEditingProductManagerIdentifierUsingName($name) {
	$nonLossyASCII = mb_convert_encoding($name, 'HTML-ENTITIES', 'auto');
	$nonLossyASCII = preg_replace(
		array('/&szlig;/', '/&(..)lig;/', '/&([aouAOU])uml;/', '/&(.)[^;]*;/'),
		array('ss',"$1","$1".'e',"$1"), 
		$nonLossyASCII
	);
	return _kSingFitStoreProductRoot.".".strtoupper(
		preg_replace(
			"/[^a-zA-Z0-9]/", 
			'', 
			$nonLossyASCII
		)
	);
}

function SingFitEditingProductManagerShortIdentifier($apple_product_identifier) {
	return str_replace(_kSingFitStoreProductRoot.".", "", $apple_product_identifier);
}

function SingFitEditingProductManagerSetActivateProduct($idproduct = 0, $activate = 0) {
	$link = false;
	if ($idproduct > 0 && false !== ($link = SingFitDataBaseConnect())) {
		$activate = $activate > 0 ? 1 : 0;
		$sql = "UPDATE store_product SET visible=".$activate." WHERE id=".$idproduct;
		@mysql_query($sql, $link);
		SingFitDataBaseClose($link);
	}
}

function SingFitEditingProductManagerParseField($data) {
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

function SingFitEditingProductManagerNewSlug() {
	$slug = SingFitMakeUUIDV4(true);
	$link = false;
	if (false !== ($link = SingFitDataBaseConnect())) {
		$rescan = false;
		$res = false;
		do {
			$sql = "SELECT id FROM store_product WHERE slug='".$slug."'";
			if (false !== ($res = mysql_query($sql, $link))) {
				if (mysql_num_rows($res) != 0) {
					$slug = SingFitMakeUUIDV4(true);
					$rescan = true;
				} else {
					$rescan = false;
				}
				mysql_free_result($res);
			}
			if (!$rescan) {
				break;
			}
			$guard++;
			if ($guard > 50) {
				$slug = SingFitMakeUUIDV4(true);
				break;
			}
		} while (true);
		SingFitDataBaseClose($link);
	}
	return $slug;
}

function SingFitEditingProductManagerProductExists($apple_product_id) {
	$link = false;
	$result = false;
	if (false !== ($link = SingFitDataBaseConnect())) {
		$res = false;
		$sql = "SELECT id FROM store_product WHERE apple_product_id='".$apple_product_id."'";
		if (false !== ($res = mysql_query($sql, $link))) {
			if (mysql_num_rows($res) != 0) {
				$result = true;
			}
			mysql_free_result($res);
		}
		SingFitDataBaseClose($link);
	}
	return $result;
}

function SingFitEditingProductManagerSetProduct($request, $slug, $update = false) {
	$result = false;
	$link = false;
	if (false !== ($link = SingFitDataBaseConnect())) {
		$apple_product_name = $request['POST']['apple_product_name'];
		$apple_product_id = SingFitEditingProductManagerIdentifierUsingName($apple_product_name);
		$apple_product_price = empty($request['POST']['apple_product_price']) ? 0.0 : floatval($request['POST']['apple_product_price']);
		$onlyforsubscriber = isset($request['POST']['onlyforsubscriber']) ? 1 : 0;
		$freeforsubscriber = isset($request['POST']['freeforsubscriber']) ? 1 : 0;
		$freeforall = isset($request['POST']['freeforall']) ? 1 : 0;
		$genres = json_decode($request['POST']['attached_genres']);
		$features = json_decode($request['POST']['attached_features']);
		$category = array_merge($genres, $features);
		$song = json_decode($request['POST']['attached_songs']);
		$bundle = count($song) > 1 ? 1 : 0;
		if ($update == true) {
			$sql = "
				UPDATE store_product
					SET apple_product_id=".SingFitEditingProductManagerParseField($apple_product_id).",
					apple_product_name=".SingFitEditingProductManagerParseField($apple_product_name).",
					apple_product_price=".SingFitEditingProductManagerParseField($apple_product_price).",
					onlyforsubscriber=".SingFitEditingProductManagerParseField($onlyforsubscriber).",
					freeforsubscriber=".SingFitEditingProductManagerParseField($freeforsubscriber).",
					freeforall=".SingFitEditingProductManagerParseField($freeforall).",
					bundle=".SingFitEditingProductManagerParseField($bundle)."
				WHERE slug=".SingFitEditingProductManagerParseField($slug).";
			";
		} else {
			$sql = "
				INSERT INTO store_product 
				(
					slug,
					apple_product_id,
					apple_product_name,
					apple_product_price,
					onlyforsubscriber,
					freeforsubscriber,
					freeforall,
					bundle,
					visible
				) VALUES (
					".SingFitEditingProductManagerParseField($slug).",
					".SingFitEditingProductManagerParseField($apple_product_id).",
					".SingFitEditingProductManagerParseField($apple_product_name).",
					".SingFitEditingProductManagerParseField($apple_product_price).",
					".SingFitEditingProductManagerParseField($onlyforsubscriber).",
					".SingFitEditingProductManagerParseField($freeforsubscriber).",
					".SingFitEditingProductManagerParseField($freeforall).",
					".SingFitEditingProductManagerParseField($bundle).",
					0
				);
			";
		}
		if (false !== @mysql_query($sql, $link)) {
			$idproduct = 0;
			if ($update == true) {
				$sql = "SELECT id FROM store_product WHERE slug='".$slug."'";
				if (false !== ($res = mysql_query($sql, $link))) {
					if (mysql_num_rows($res) == 1) {
						while ($row = mysql_fetch_assoc($res)) {
							$idproduct = $row['id'];
						}
					}
					mysql_free_result($res);
				}
			} else {
				$idproduct = mysql_insert_id();
			}
			if ($idproduct > 0) {
				if ($update == true) {
					$sql = "DELETE FROM store_product_to_category WHERE product_id=".$idproduct;
					@mysql_query($sql, $link);
					$sql = "DELETE FROM store_product_to_singfit_song WHERE product_id=".$idproduct;
					@mysql_query($sql, $link);
				}
				$values = null;
				foreach ($category as $idcat) {
					$values[] = "(".$idcat."  , ".$idproduct.")";
				}
				$sql = "
					INSERT INTO store_product_to_category
					(
						category_id, 
						product_id
					)
					VALUES ".implode(",", $values)."
				";
				@mysql_query($sql, $link);
				$values = null;
				foreach ($song as $idsong) {
					$values[] = "(".$idsong."  , ".$idproduct.")";
				}
				$sql = "
					INSERT INTO store_product_to_singfit_song
					(
						song_id, 
						product_id
					)
					VALUES ".implode(",", $values)."
				";
				@mysql_query($sql, $link);
				$result = true;
			}	
		}
		SingFitDataBaseClose($link);
	}
	return $result;
}

/* EOF */ ?>