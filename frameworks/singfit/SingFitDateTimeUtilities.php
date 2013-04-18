<?php
# -*- coding: utf-8 -*-

//
//  Copyright (C) 2011 Sonoma Wire Works. All rights reserved.
//

require_once dirname(__FILE__)."/SingFitCommonSettings.php";

function SingFitDateTimeUtilitiesisUnixTimeStamp($unixTimeStamp) {
	if (!is_null($unixTimeStamp) && is_numeric($unixTimeStamp) && (intval($unixTimeStamp) > 0 && intval($unixTimeStamp) < PHP_INT_MAX)) {
		return true;
	}
	return false;
}

function SingFitDateTimeUtilitiesCheckDateTime($year = 0, $month = 0, $day = 0, $hour = 0, $minute = 0, $second = 0) {
	if (checkdate($month, $day, $year)) {
		if (($hour >= 0 && $hour < 24) && ($minute >= 0 && $minute < 60) && ($second >= 0 && $second < 60)) { 
			return true; 
		}
	}
	return false;
}

function SingFitDateTimeUtilitiesParseDateTime($dateTime, &$parsedDateTime) {
	if (preg_match("/^(\d{4})-(\d{1,2})-(\d{1,2})([Tt ]{0,2})(\d{1,2}):(\d{1,2}):(\d{1,2})/", $dateTime, $matches)) {
		$year = $matches[1];
		$month = $matches[2];
		$day = $matches[3];
		$hour = $matches[5];
		$minute = $matches[6];
		$second = $matches[7];
		if (SingFitDateTimeUtilitiesCheckDateTime($year, $month, $day, $hour, $minute, $second)) {
			$parsedDateTime = sprintf("%04d-%02d-%02d %02d:%02d:%02d", $year, $month, $day, $hour, $minute, $second);
			return true;
		}
	}
	$parsedDateTime = null;
	return false;
}

function SingFitDateTimeUtilitiesNonIsoGMTDateToDateTime($noniso_gmtdate) {
	if ($noniso_gmtdate === null) {
		return null;
	}
	$clean_date = null;
	if (SingFitDateTimeUtilitiesParseDateTime($noniso_gmtdate, $clean_date)) {
		try {
			$datetime = new DateTime($clean_date, new DateTimeZone('GMT'));
			$datetime->setTimezone(new DateTimeZone(date_default_timezone_get()));
			return $datetime->format("Y-m-d H:i:s");
		} catch(Exception $e) {}
	}
	return null;
}

function SingFitDateTimeUtilitiesUnixTimeStampToDateTime($unixTimeStamp) {
	if ($unixTimeStamp === null) {
		return null;
	}
	if (SingFitDateTimeUtilitiesisUnixTimeStamp($unixTimeStamp)) {
		try {
			$datetime = new DateTime();
			$datetime->setTimezone(new DateTimeZone(date_default_timezone_get()));
			$datetime->setTimestamp($unixTimeStamp);
			return $datetime->format("Y-m-d H:i:s");
		} catch(Exception $e) {}
	}
	return null;
}

/* EOF */ ?>