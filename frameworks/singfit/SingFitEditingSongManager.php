<?php
# -*- coding: utf-8 -*-

//
//  Copyright (C) 2011 Sonoma Wire Works. All rights reserved.
//

require_once dirname(__FILE__)."/SingFitAudioFileTool.php";
require_once dirname(__FILE__).'/SingFitDatabaseConnection.php';
require_once dirname(__FILE__)."/SingFitMakeUUID.php";

function SingFitEditingSongManagerGetReportForPublishers($request) {
	$result = null;
	if (false !== ($link = SingFitDataBaseConnect())) {
		$date = mysql_escape_string($request['GET']['d']);
		$sql = "select publisher from singfit_song where publisher != '' group by publisher order by publisher";
		$publishers = array();
		if (false !== ($res = mysql_query($sql, $link))) {
			if (mysql_num_rows($res) != 0) {
				while ($row = mysql_fetch_assoc($res)) {
					if (strpos($row['publisher'], "Universal ") !== 0) {
						array_push($publishers, $row['publisher']);
					}
				}
			}
			sort($publishers);
			mysql_free_result($res);
		}
		$sql = "select count(id) as cnt from singfit_song where creation_date < '".$date."'";
		$total = 1;
		if (false !== ($res = mysql_query($sql, $link))) {
			if (mysql_num_rows($res) == 1) {
				while ($row = mysql_fetch_assoc($res)) {
					$total = $row['cnt'];
				}
			}
			mysql_free_result($res);
		}
		for ($j = 0; $j < count($publishers); $j++) {
			$sql = "select count(id) as cnt from singfit_song where creation_date < '".$date."' and publisher LIKE '%".$publishers[$j]."%'";
			$cnt = 0;
			if (false !== ($res = mysql_query($sql, $link))) {
				if (mysql_num_rows($res) == 1) {
					while ($row = mysql_fetch_assoc($res)) {
						$cnt = $row['cnt'];
					}
				}
				mysql_free_result($res);
			}
			$percent =  $cnt / $total * 100;
			$result.= "\t".$publishers[$j]." = ".round($percent, 4, PHP_ROUND_HALF_ODD)."%\n";
		}
		SingFitDataBaseClose($link);
	}
	return $result;
}

function SingFitEditingSongManagerGetReportForRecordRights($request) {
	$result = null;
	if (false !== ($link = SingFitDataBaseConnect())) {
		$date = mysql_escape_string($request['GET']['d']);
		$sql = "select rerecord_rights from singfit_song where rerecord_rights != '' group by rerecord_rights order by rerecord_rights";
		$rerecord_rights = array();
		if (false !== ($res = mysql_query($sql, $link))) {
			if (mysql_num_rows($res) != 0) {
				while ($row = mysql_fetch_assoc($res)) {
					if (strpos($row['rerecord_rights'], "SBI ") !== 0 && strpos($row['rerecord_rights'], "JLI") !== 0) {
						array_push($rerecord_rights, $row['rerecord_rights']);
					}
				}
			}
			array_push($rerecord_rights, "JLI");
			sort($rerecord_rights);
			mysql_free_result($res);
		}
		$sql = "select count(id) as cnt from singfit_song where creation_date < '".$date."'";
		$total = 1;
		if (false !== ($res = mysql_query($sql, $link))) {
			if (mysql_num_rows($res) == 1) {
				while ($row = mysql_fetch_assoc($res)) {
					$total = $row['cnt'];
				}
			}
			mysql_free_result($res);
		}
		for ($j = 0; $j < count($rerecord_rights); $j++) {
			$sql = "select count(id) as cnt from singfit_song where creation_date < '".$date."' and rerecord_rights LIKE '%".$rerecord_rights[$j]."%'";
			$cnt = 0;
			if (false !== ($res = mysql_query($sql, $link))) {
				if (mysql_num_rows($res) == 1) {
					while ($row = mysql_fetch_assoc($res)) {
						$cnt = $row['cnt'];
					}
				}
				mysql_free_result($res);
			}
			$percent =  $cnt / $total * 100;
			$result.= "\t".$rerecord_rights[$j]." = ".round($percent, 4, PHP_ROUND_HALF_ODD)."%\n";
		}
		SingFitDataBaseClose($link);
	}
	return $result;
}

function SingFitEditingSongManagerGetRawReport($request) {
	$result = null;
	if (false !== ($link = SingFitDataBaseConnect())) {
		$date = mysql_escape_string($request['GET']['d']);
		
		$endDate = new DateTime($date);
		$endDate = $endDate->add(DateInterval::createFromDateString('1 month'));
		$endDate = $endDate->format('Y-m-d');
		$sql = "SELECT singfit_song.* , COUNT( store_transaction_request.id ) AS downloads
		FROM singfit_song
        LEFT JOIN store_product_to_singfit_song ON store_product_to_singfit_song.song_id = singfit_song.id
        LEFT JOIN store_product ON store_product_to_singfit_song.product_id = store_product.id
        LEFT JOIN store_transaction_request ON store_product.apple_product_id = store_transaction_request.apple_product_id and
        store_transaction_request.request_date > '%s' and store_transaction_request.request_date < '%s'
        GROUP BY singfit_song.id";		
		$sql = sprintf($sql, $date, $endDate);
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

function SingFitEditingSongManagerGetReport($request) {
	$report = "";
	if (isset($request['GET']['t']) && $request['GET']['t'] == "raw") {
		$report .= SingFitEditingSongManagerGetRawReport($request);
	} else {
		$report .= "-- Publishers\n";
		$report .= SingFitEditingSongManagerGetReportForPublishers($request);
		$report .= "\n-- Rerecord Rights\n";
		$report .= SingFitEditingSongManagerGetReportForRecordRights($request);
	}
	return $report;
}

function SingFitEditingSongManagerCheckPackage($filepaths, &$error = null) {
	$matchedfiles = array();
	foreach($filepaths as $k => $v) {
		$vv = ltrim(rtrim($v));
		if (!preg_match("/__MACOSX/", $vv)) {
			if (preg_match("/\/guide\.wav$/", $vv)) {
				if (SingFitAudioFileToolIsWAV($vv)) {
					$matchedfiles["guide"] = $vv;
				}
			}
			if (preg_match("/\/guide_transposed\.wav$/", $vv)) {
				if (SingFitAudioFileToolIsWAV($vv)) {
					$matchedfiles["guide_transposed"] = $vv;
				}
			}
			if (preg_match("/\/music\.wav$/", $vv)) {
				if (SingFitAudioFileToolIsWAV($vv)) {
					$matchedfiles["music"] = $vv;
				}
			}
			if (preg_match("/\/music_transposed\.wav$/", $vv)) {
				if (SingFitAudioFileToolIsWAV($vv)) {
					$matchedfiles["music_transposed"] = $vv;
				}
			}
			if (preg_match("/\/preview\.mp3$/", $vv)) {
				if (SingFitAudioFileToolIsMP3($vv)) {
					$matchedfiles["preview"] = $vv;
				}
			}
			if (preg_match("/\/prompter\.wav$/", $vv)) {
				if (SingFitAudioFileToolIsWAV($vv)) {
					$matchedfiles["prompter"] = $vv;
				}
			}
		}
	}
	if (isset($matchedfiles['guide']) && isset($matchedfiles['music'])  && isset($matchedfiles['preview'])  && isset($matchedfiles['prompter'])) {
		if ($error != null) {
			$error = 0;
		}
		return $matchedfiles;
	}
	if ($error != null) {
		$error = 1;
	}
	return null;
}

function SingFitEditingSongManagerParseField($data) {
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

function SingFitEditingSongManagerNewSlug() {
	$slug = SingFitMakeUUIDV4(true);
	$link = false;
	if (false !== ($link = SingFitDataBaseConnect())) {
		$rescan = false;
		$res = false;
		do {
			$sql = "SELECT id FROM singfit_song WHERE slug='".$slug."'";
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

function SingFitEditingSongManagerSongExists($title) {
	$link = false;
	$result = false;
	if (false !== ($link = SingFitDataBaseConnect())) {
		$title = mysql_escape_string($title);
		$res = false;
		$sql = "SELECT id FROM singfit_song WHERE title='".$title."'";
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

function SingFitEditingSongManagerFindSong($request) {
	$result = array();
	$link = false;
	if (!isset($request['POST']['search']) || empty($request['POST']['search'])) {
		return $result;
	}
	if (false !== ($link = SingFitDataBaseConnect())) {
		$search = mysql_escape_string($request['POST']['search']);
		$res = false;
		$sql = "
			SELECT id, title 
			FROM singfit_song 
			WHERE title LIKE '%".$search."%' 
				OR author LIKE '%".$search."%' 
				OR artist LIKE '%".$search."%' 
				OR producer LIKE '%".$search."%' 
				OR publisher LIKE '%".$search."%' 
				OR publishing_year LIKE '%".$search."%' 
			GROUP BY id 
			ORDER BY title 
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

function SingFitEditingSongManagerSetSong($request, $slug, $update = false) {
	$result = false;
	$link = false;
	if (false !== ($link = SingFitDataBaseConnect())) {
		$tempo_bpm = 0;
		if (!empty($request['POST']['tempo_bpm'])) {
			$tempo_bpm = $request['POST']['tempo_bpm'];
		}
		$canbeshared = isset($request['POST']['canbeshared']) ? 1 : 0;
		if ($update == true) {
			/*$destpath = kSingFitStoreDataRoot."/".(substr($slug, 0, 2));
			$destzip = $destpath."/".$slug.".zip";
			$creation_date = null;
			if (@file_exists($destzip)) {
				$creation_date = date("Y-m-d H:i:s", @filemtime($destzip));
			}*/
			$sql = "UPDATE singfit_song ";
			/*if (!is_null($creation_date)) {
				$sql .= " SET creation_date='".$creation_date."', ";
			} else {
				$sql .= " SET creation_date=NOW(), ";
			}*/
			$sql .= "SET title=".SingFitEditingSongManagerParseField($request['POST']['title']).",
					canbeshared=".SingFitEditingSongManagerParseField($canbeshared).",
					author=".SingFitEditingSongManagerParseField($request['POST']['author']).",
					artist=".SingFitEditingSongManagerParseField($request['POST']['artist']).",
					producer=".SingFitEditingSongManagerParseField($request['POST']['producer']).",
					publisher=".SingFitEditingSongManagerParseField($request['POST']['publisher']).",
					publishing_year=".SingFitEditingSongManagerParseField($request['POST']['publishing_year']).",
					rerecord_rights=".SingFitEditingSongManagerParseField($request['POST']['rerecord_rights']).",
					credits=".SingFitEditingSongManagerParseField($request['POST']['credits']).",
					length_in_seconds=".SingFitEditingSongManagerParseField($request['POST']['length_in_seconds']).",
					musical_key=".SingFitEditingSongManagerParseField($request['POST']['musical_key']).",
					tempo_bpm=".SingFitEditingSongManagerParseField($tempo_bpm).",
					musical_era=".SingFitEditingSongManagerParseField($request['POST']['musical_era']).",
					difficulty_singing=".SingFitEditingSongManagerParseField($request['POST']['difficulty_singing']).",
					notes=".SingFitEditingSongManagerParseField($request['POST']['notes'])."
				WHERE slug=".SingFitEditingSongManagerParseField($slug)."
			";
		} else {
			$sql = "
				INSERT INTO singfit_song 
				(
					id,
					creation_date,
					slug,
					title,
					canbeshared,
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
				) 
				VALUES 
				(
					NULL,
					NOW(),
					".SingFitEditingSongManagerParseField($slug).",
					".SingFitEditingSongManagerParseField($request['POST']['title']).",
					".SingFitEditingSongManagerParseField($canbeshared).",
					".SingFitEditingSongManagerParseField($request['POST']['author']).",
					".SingFitEditingSongManagerParseField($request['POST']['artist']).",
					".SingFitEditingSongManagerParseField($request['POST']['producer']).",
					".SingFitEditingSongManagerParseField($request['POST']['publisher']).",
					".SingFitEditingSongManagerParseField($request['POST']['publishing_year']).",
					".SingFitEditingSongManagerParseField($request['POST']['rerecord_rights']).",
					".SingFitEditingSongManagerParseField($request['POST']['credits']).",
					".SingFitEditingSongManagerParseField($request['POST']['length_in_seconds']).",
					".SingFitEditingSongManagerParseField($request['POST']['musical_key']).",
					".SingFitEditingSongManagerParseField($tempo_bpm).",
					".SingFitEditingSongManagerParseField($request['POST']['musical_era']).",
					".SingFitEditingSongManagerParseField($request['POST']['difficulty_singing']).",
					".SingFitEditingSongManagerParseField($request['POST']['notes'])."
				)
			";
		}
		if (false !== @mysql_query($sql, $link)) {
			$result = true;
		}
		SingFitDataBaseClose($link);
	}
	return $result;
}

/* EOF */ ?>