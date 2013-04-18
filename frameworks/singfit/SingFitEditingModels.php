<?php
# -*- coding: utf-8 -*-

//
//  Copyright (C) 2011 Sonoma Wire Works. All rights reserved.
//

require_once dirname(__FILE__).'/SingFitDatabaseConnection.php';

function SingFitEditingSetSongPublishingYearModel() {
	$now = date('Y');
	$model = array();
	$model['ModelName'] = 'DifficultySinging';
	$model['ModelType'] = 'List';
	$model['ModelItemsIndex'] = $now;
	$model['ModelItems'] = array();
	$i = intval($now);
	$max = $i - 420;
	for ($i; $i >= $max; $i--) {
		$model['ModelItems'][$i] = $i;
	}
	return $model;
}

function SingFitEditingDifficultySingingModel() {
	$model = array();
	$model['ModelName'] = 'DifficultySinging';
	$model['ModelType'] = 'List';
	$model['ModelItemsIndex'] = "1";
	$model['ModelItems'] = array(
		0 => "Very easy",
		1 => "Easy",
		2 => "Moderate",
		3 => "Somewhat hard",
		4 => "Hard",
		5 => "Very Hard"
	);
	return $model;
}

function SingFitEditingMusicalKeyModel() {
	$model = array();
	$model['ModelName'] = 'MusicalKey';
	$model['ModelType'] = 'List';
	$model['ModelItemsIndex'] = "F+_C";
	$model['ModelItems'] = array();
	$model['ModelItems']['FlatsMajor'] = array(
		"F+_C" => "C",
		"F+_F" => "F",
		"F+_B^" => "B♭",
		"F+_E^" => "E♭",
		"F+_A^" => "A♭",
		"F+_D^" => "D♭",
		"F+_G^" => "G♭",
		"F+_C^" => "C♭",
		"F+_F^" => "F♭"
	);
	$model['ModelItems']['FlatsMinor'] = array(
		"F-_a" => "a",
		"F-_d" => "d",
		"F-_g" => "g",
		"F-_c" => "c",
		"F-_f" => "f",
		"F-_b^" => "b♭",
		"F-_e^" => "e♭",
		"F-_a^" => "a♭",
		"F-_d^" => "d♭"
	);
	$model['ModelItems']['SharpsMajor'] = array(
		"S+_C" => "C",
		"S+_G" => "G",
		"S+_D" => "D",
		"S+_A" => "A",
		"S+_E" => "E",
		"S+_B" => "B",
		"S+_F#" => "F♯",
		"S+_C#" => "C♯",
		"S+_G#" => "G♯"
	);
	$model['ModelItems']['SharpsMinor'] = array(
		"S-_a" => "a",
		"S-_e" => "e",
		"S-_b" => "b",
		"S-_f#" => "f♯",
		"S-_c#" => "c♯",
		"S-_g#" => "g♯",
		"S-_d#" => "d♯",
		"S-_a#" => "a♯",
		"S-_e#" => "e♯"
	);
	return $model;
}

function SingFitEditingMusicalEraModel() {
	$model = array();
	$model['ModelName'] = 'MusicalEra';
	$model['ModelType'] = 'List';
	$model['ModelItemsIndex'] = 1990;
	$model['ModelItems'] = array(
		0 =>"Unspecified",
		2060 =>"20-60s",
		2050 =>"20-50s",
		2040 =>"20-40s",
		2030 =>"20-30s",
		2020 =>"20-20s",
		2010 =>"20-10s",
		2000 =>"20-00s",
		1990 =>"90's",
		1980 =>"80's",
		1970 =>"70's",
		1960 =>"60's",
		1950 =>"50's",
		1940 =>"40's",
		1930 =>"30's",
		1920 =>"20's",
		1910 =>"10's",
		1800 =>"19th century",
		1700 =>"18th century",
		1600 =>"17th century",
		1500 =>"16th century",
		1400 =>"15th century",
		1300 =>"14th century",
		1200 =>"13th century",
		1100 =>"12th century"
	);
	return $model;
}

function SingFitSongModel($idsong = 0) {
	$link = false;
	$model = array();
	$model['ModelName'] = 'Song';
	$model['ModelType'] = 'List';
	$model['ModelItemsIndex'] = -1;
	$model['ModelItems'] = array(
		'creation_date' => null,
		'canbeshared' => null,
		'slug' => null,
		'title' => null,
		'author' => null,
		'artist' => null,
		'producer' => null,
		'publisher' => null,
		'publishing_year' => null,
		'rerecord_rights' => '',
		'credits' => null,
		'length_in_seconds' => null,
		'musical_key' => null,
		'tempo_bpm' => null,
		'musical_era' => null,
		'difficulty_singing' => null,
		'notes'  => null
	);
	if ($idsong > 0 && false !== ($link = SingFitDataBaseConnect())) {
		$row = null;
		$res = false;
		$sql = "
			SELECT 
				creation_date,
				canbeshared,
				slug,
				title,
				author,
				artist,
				producer,
				publisher,
				publishing_year,
				rerecord_rights,
				credits,
				length_in_seconds,
				musical_key,
				tempo_bpm,
				musical_era,
				difficulty_singing,
				notes
			FROM singfit_song 
			WHERE id=".$idsong." 
		";
		if (false !== ($res = mysql_query($sql, $link))) {
			if (mysql_num_rows($res) != 0) {
				while ($row = mysql_fetch_assoc($res)) {
					foreach ($row as $k => $v) {
						$model['ModelItems'][$k] = $v;
					}
				}
			}
			mysql_free_result($res);
		}
		/** recovering the creation date using the zip.
		if ($model['ModelItems']['creation_date'] == null) {
			$slug = $model['ModelItems']['slug'];
			$destpath = kSingFitStoreDataRoot."/".(substr($slug, 0, 2));
			$destzip = $destpath."/".$slug.".zip";
			if (@file_exists($destzip)) {
				$creation_date = date("Y-m-d H:i:s", @filemtime($destzip));
				$sql = "UPDATE singfit_song SET creation_date='".$creation_date."' WHERE slug='".$slug."'";
				@mysql_query($sql, $link);
				$model['ModelItems']['creation_date'] = $creation_date;
			}
		}
		**/
		SingFitDataBaseClose($link);
	}
	return $model;
}

function SingFitEditingCategoryModel($featured = false) {
	$link = false;
	$model = array();
	$model['ModelName'] = $featured ? 'Genre' : 'Feature';
	$model['ModelType'] = 'List';
	$model['ModelItemsIndex'] = 0;
	$model['ModelItems'] = array();
	if (false !== ($link = SingFitDataBaseConnect())) {
		$row = null;
		$res = false;
		$menuhead = $featured ? '__head_featured__' : '__head_genre__';
		$sql = "
			SELECT 
				a.id AS id, 
				a.name AS name 
			FROM store_product_category AS a 
			INNER JOIN store_product_category AS b ON b.name='".$menuhead."' 
			WHERE a.id_parent = b.id ORDER BY a.name";
		if (false !== ($res = mysql_query($sql, $link))) {
			if (mysql_num_rows($res) != 0) {
				while ($row = mysql_fetch_assoc($res)) {
					array_push($model['ModelItems'], $row);
				}
			}
			mysql_free_result($res);
		}
		SingFitDataBaseClose($link);
	}
	return $model;
}

function SingFitEditingAttachedCategoryModel($idproduct = 0, $featured = false) {
	$link = false;
	$model = array();
	$model['ModelName'] = $featured ? 'AttachedGenre' : 'AttachedFeature';
	$model['ModelType'] = 'List';
	$model['ModelItemsIndex'] = -1;
	$model['ModelItems'] = array();
	if ($idproduct > 0 && false !== ($link = SingFitDataBaseConnect())) {
		$row = null;
		$res = false;
		$menuhead = $featured ? '__head_featured__' : '__head_genre__';
		$sql = "
			SELECT 
				a.id AS id, 
				a.name AS name
			FROM store_product_category AS a
			INNER JOIN store_product_category AS b ON b.name =  '".$menuhead."'
			RIGHT JOIN store_product_to_category AS c ON c.product_id=".$idproduct."
			WHERE a.id_parent = b.id
			AND c.category_id = a.id
		";
		if (false !== ($res = mysql_query($sql, $link))) {
			if (mysql_num_rows($res) != 0) {
				while ($row = mysql_fetch_assoc($res)) {
					array_push($model['ModelItems'], $row);
				}
			}
			mysql_free_result($res);
		}
		SingFitDataBaseClose($link);
	}
	return $model;
}

function SingFitEditingAttachedSongModel($idproduct = 0) {
	$link = false;
	$model = array();
	$model['ModelName'] = 'AttachedSong';
	$model['ModelType'] = 'List';
	$model['ModelItemsIndex'] = -1;
	$model['ModelItems'] = array();
	if ($idproduct > 0 && false !== ($link = SingFitDataBaseConnect())) {
		$row = null;
		$res = false;
		$sql = "
			SELECT  
				 singfit_song.id,
				 singfit_song.title
			FROM store_product_to_singfit_song,  singfit_song
			WHERE store_product_to_singfit_song.song_id	 =  singfit_song.id 
			AND store_product_to_singfit_song.product_id=".$idproduct."
		";
		if (false !== ($res = mysql_query($sql, $link))) {
			if (mysql_num_rows($res) != 0) {
				while ($row = mysql_fetch_assoc($res)) {
					array_push($model['ModelItems'], $row);
				}
			}
			mysql_free_result($res);
		}
		SingFitDataBaseClose($link);
	}
	return $model;
}

function SingFitProductModel($idproduct = 0) {
	$link = false;
	$model = array();
	$model['ModelName'] = 'Product';
	$model['ModelType'] = 'List';
	$model['ModelItemsIndex'] = -1;
	$model['ModelItems'] = array(
		'slug' => null,
		'apple_product_id' => null,
		'apple_product_name' => null,
		'apple_product_price' => null,
		'onlyforsubscriber' => 0,
		'freeforsubscriber' => 0,
		'freeforall' => 0,
		'bundle' => 0,
		'visible' => 0
	);	
	if ($idproduct > 0 && false !== ($link = SingFitDataBaseConnect())) {
		$row = null;
		$res = false;
		$sql = "
			SELECT 
				slug,
				apple_product_id,
				apple_product_name,
				FORMAT(apple_product_price, 2) AS apple_product_price,
				onlyforsubscriber,
				freeforsubscriber,
				freeforall,
				bundle
			FROM store_product  
			WHERE id=".$idproduct." 
			AND apple_product_type=0
		";
		if (false !== ($res = mysql_query($sql, $link))) {
			if (mysql_num_rows($res) != 0) {
				while ($row = mysql_fetch_assoc($res)) {
					foreach ($row as $k => $v) {
						$model['ModelItems'][$k] = $v;
					}
				}
			}
			mysql_free_result($res);
		}
		SingFitDataBaseClose($link);
	}
	return $model;
}

function SingFitAllSongModel() {
	$link = false;
	$model = array();
	$model['ModelName'] = 'AllSong';
	$model['ModelType'] = 'List';
	$model['ModelItemsIndex'] = "None";
	$model['ModelItems'] = array();
	if (false !== ($link = SingFitDataBaseConnect())) {
		$row = null;
		$res = false;
		$sql = "SELECT id, title, author, artist FROM singfit_song ORDER BY id desc";
		if (false !== ($res = mysql_query($sql, $link))) {
			if (mysql_num_rows($res) != 0) {
				while ($row = mysql_fetch_assoc($res)) {
					array_push($model['ModelItems'], $row);
				}
			}
			mysql_free_result($res);
		}
		SingFitDataBaseClose($link);
	}
	return $model;
}

function SingFitAllProductModel() {
	$link = false;
	$model = array();
	$model['ModelName'] = 'AllProduct';
	$model['ModelType'] = 'List';
	$model['ModelItemsIndex'] = -1;
	$model['ModelItems'] = array();
	if (false !== ($link = SingFitDataBaseConnect())) {
		$row = null;
		$res = false;
		$sql = "
			SELECT 
				id, 
				apple_product_id, 
				apple_product_name, 
				visible 
			FROM store_product 
			WHERE apple_product_type=0 
			ORDER BY id desc";
		if (false !== ($res = mysql_query($sql, $link))) {
			if (mysql_num_rows($res) != 0) {
				while ($row = mysql_fetch_assoc($res)) {
					array_push($model['ModelItems'], $row);
				}
			}
			mysql_free_result($res);
		}
		SingFitDataBaseClose($link);
	}
	return $model;
}

/* EOF */ ?>