<?php
# -*- coding: utf-8 -*-

//
//  Copyright (C) 2011 Sonoma Wire Works. All rights reserved.
//

require_once dirname(__FILE__).'/SingFitDatabaseConnection.php';
function SingFitEditingPlaylistManagerGetRawReport($request) {
	$result = null;
	if (false !== ($link = SingFitDataBaseConnect())) {
		$date = mysql_escape_string($request['GET']['d']);
		
		$endDate = new DateTime($date);
		$endDate = $endDate->add(DateInterval::createFromDateString('1 month'));
		$endDate = $endDate->format('Y-m-d');
		$sql = "SELECT * from playlist";		
		if (false !== ($res = mysql_query($sql, $link))) {
			$fields = mysql_num_fields($res);
			for ($i = 0; $i < $fields; $i++) {
				$result .= mysql_field_name($res, $i).",";
			}
			$result .= "\n";
			while($row = mysql_fetch_row($res)) {
				$line = "";
				foreach($row as $value) {
					if ((!isset($value)) || ($value == "")) {
						$value = '""'.",";
					} else {
						$value = str_replace('"', '""', $value);
						$value = '"'.$value.'"'.",";
					}
					$line .= $value;
				}
				$result .= trim($line)."\n";
			}
			$result = str_replace("\r", "", $result);
		}
		SingFitDataBaseClose($link);
	}
	return $result;
}

function SingFitEditingPlaylistManagerParseField($data) {
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

function SingFitEditingPlaylistManagerPlaylistExists($title) {
	$link = false;
	$result = false;
	if (false !== ($link = SingFitDataBaseConnect())) {
		$title = mysql_escape_string($title);
		$res = false;
		$sql = "SELECT id FROM playlist WHERE name='".$title."'";
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

function SingFitEditingPlaylistManagerFindPlaylist($request) {
	$result = array();
	$link = false;
	if (!isset($request['POST']['search']) || empty($request['POST']['search'])) {
		return $result;
	}
	if (false !== ($link = SingFitDataBaseConnect())) {
		$search = mysql_escape_string($request['POST']['search']);
		$res = false;
		$sql = "
			SELECT id, name
			FROM playlist 
			WHERE name LIKE '%".$search."%' 
			ORDER BY name
			LIMIT 40
		";
		if (false !== ($res = mysql_query($sql, $link))) {
			if (mysql_num_rows($res) != 0) {
				while ($row = mysql_fetch_assoc($res)) {
					//$row['title'] = $row['title'];
					array_push($result, $row);
				}
			}
			mysql_free_result($res);
		}
		SingFitDataBaseClose($link);
	}
	return $result;
}
function SingFitEditingPlaylistManagerDeletePlaylist($request)
{
    $result = false;
	$link = false;
	if (false !== ($link = SingFitDataBaseConnect())) {
    	$sql = sprintf("DELETE FROM store_product_to_playlist where playlist_id = %s", $request['POST']['id']);
    	@mysql_query($sql, $link);
    	$sql = sprintf("DELETE FROM playlist where id = %s", $request['POST']['id']);
    	@mysql_query($sql, $link);
    	$resul = true;
		SingFitDataBaseClose($link);
	}
	return $result;
}
function SingFitEditingPlaylistManagerSetPlaylist($request, $slug, $update = false) {
	$result = false;
	$link = false;
	if (false !== ($link = SingFitDataBaseConnect())) {

		if ($update == true) {
			$sql = "UPDATE playlist ";
			$sql .= "SET name=".SingFitEditingPlaylistManagerParseField($request['POST']['name']).
			"WHERE id =".SingFitEditingPlaylistManagerParseField($request['POST']['id']);
		} else {
			$sql = "
				INSERT INTO playlist 
				(
					id,
					name
				) 
				VALUES 
				(
					NULL,
					".SingFitEditingSongManagerParseField($request['POST']['name'])."
				)
			";
		}
		if (false !== @mysql_query($sql, $link)) {
			$result = true;
			
            if ($update != true)
        		$playlist_id = mysql_insert_id($link);
			else
    			$playlist_id = $request['POST']['id'];
    			
			$sql = "DELETE FROM store_app_to_playlist where playlist_id = " . $playlist_id;
            @mysql_query($sql, $link);
            foreach ($request['POST']['app_id'] as $app_id)
    		{
        		$sql = "INSERT INTO store_app_to_playlist (id, app_id, playlist_id) VALUES (NULL, " . $app_id . ", " . $playlist_id . ")";
    			@mysql_query($sql, $link);
    		}
    		$productOrder = 0;
    		if (array_key_exists('product_id', $request['POST']))
    		{
        		foreach ($request['POST']['product_id'] as $product_id)
        		{
            		$sql = sprintf("UPDATE store_product_to_playlist set store_product_to_playlist.order = %d where product_id = %d and playlist_id = %d", $productOrder, $product_id, $playlist_id);
            		@mysql_query($sql, $link);
            		$productOrder++;
        		}
        		
    		}
			$result = true;					
		}
		SingFitDataBaseClose($link);
	}
	return $result;
}

/* EOF */ ?>