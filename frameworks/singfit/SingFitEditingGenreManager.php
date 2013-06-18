<?php
# -*- coding: utf-8 -*-

//
//  Copyright (C) 2011 Sonoma Wire Works. All rights reserved.
//

require_once dirname(__FILE__).'/SingFitDatabaseConnection.php';
function SingFitEditingGenreManagerGetRawReport($request) {
	$result = null;
	if (false !== ($link = SingFitDataBaseConnect())) {
		$date = mysql_escape_string($request['GET']['d']);
		
		$endDate = new DateTime($date);
		$endDate = $endDate->add(DateInterval::createFromDateString('1 month'));
		$endDate = $endDate->format('Y-m-d');
		$sql = "SELECT * from store_product_category";		
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

function SingFitEditingGenreManagerParseField($data) {
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

function SingFitEditingGenreManagerGenreExists($title) {
	$link = false;
	$result = false;
	if (false !== ($link = SingFitDataBaseConnect())) {
		$title = mysql_escape_string($title);
		$res = false;
		$sql = "SELECT id FROM store_product_category WHERE name='".$title."'";
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

function SingFitEditingGenreManagerFindGenre($request) {
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
			FROM store_product_category 
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

function SingFitEditingGenreManagerDeleteGenre($request)
{
    $result = false;
	$link = false;
	if (false !== ($link = SingFitDataBaseConnect())) {
    	$sql = sprintf("DELETE FROM store_product_to_category where category_id = %s", $request['POST']['id']);
    	@mysql_query($sql, $link);
    	$sql = sprintf("DELETE FROM store_product_category where id = %s", $request['POST']['id']);
    	@mysql_query($sql, $link);
    	$result = true;
		SingFitDataBaseClose($link);
	}
	return $result;
}

function SingFitEditingGenreManagerSetGenre($request, $update = false) {
	$result = false;
	$link = false;
	if (false !== ($link = SingFitDataBaseConnect())) {
    	if (array_key_exists('visible', $request['POST']))
    	{
        	$visible = 1;
    	}
    	else
    	{
        	$visible = 0;
    	}
		if ($update == true) {
			$sql = "UPDATE store_product_category ".
			"SET name=".SingFitEditingGenreManagerParseField($request['POST']['name'])
			.", visible=".$visible		
			.", published=now() "	
			."WHERE id =".SingFitEditingGenreManagerParseField($request['POST']['id']);
		} else {
			$sql = "
				INSERT INTO store_product_category 
				(
					id,
					id_parent,
					name,
					published,
					visible
				) 
				VALUES 
				(
					NULL, 28,
					".SingFitEditingGenreManagerParseField($request['POST']['name'])."
					, now()
					, ".$visible."
				)
			";
		}

		if (false !== @mysql_query($sql, $link)){
            if ($update != true)
        		$cat_id = mysql_insert_id($link);
			else
    			$cat_id = $request['POST']['id'];
    			
			$sql = "DELETE FROM store_app_to_category where category_id = " . $cat_id;
            @mysql_query($sql, $link);
            foreach ($request['POST']['app_id'] as $app_id)
    		{
        		$sql = "INSERT INTO store_app_to_category (id, app_id, category_id) VALUES (NULL, " . $app_id . ", " . $cat_id . ")";
    			@mysql_query($sql, $link);
    		}
			$result = true;			
		}
		SingFitDataBaseClose($link);
	}
	return $result;
}

/* EOF */ ?>