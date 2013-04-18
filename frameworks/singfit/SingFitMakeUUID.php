<?php
# -*- coding: utf-8 -*-

//
//  Copyright (C) 2011 Sonoma Wire Works. All rights reserved.
//

function SingFitMakeUUIDV4($sha1 = false, $exec = true) {
	$stdout = array();
	$cmd = "uuidgen 2>&1";
	$status = -100;
	$uuid = null;
	$haveUUID = false;
	if ($exec) {
		exec($cmd, $stdout, $status);
	}
	if ($status == 0 && count($stdout)) {
		$uuid = $stdout[0];
		if (strlen($uuid) == 36) {
			$haveUUID = true;
			$uuid = strtoupper($uuid);
		}
	}
	if (!$haveUUID) {
		$md5 = strtoupper(md5(uniqid(microtime(true), true)));
		$uuid = sprintf("%04s%04s-%04s-%04s-%04s-%04s%04s%04s",
			$md5[0].$md5[1].$md5[2].$md5[3],
			$md5[4].$md5[5].$md5[6].$md5[7],
			$md5[8].$md5[9].$md5[10].$md5[11],
			$md5[12].$md5[13].$md5[14].$md5[15],
			$md5[16].$md5[17].$md5[18].$md5[19],
			$md5[20].$md5[21].$md5[22].$md5[23],
			$md5[24].$md5[25].$md5[26].$md5[27],
			$md5[28].$md5[29].$md5[30].$md5[31]
		);
	}
	if ($sha1) {
		$uuid = sha1($uuid);
	}
	return $uuid;
}

/* EOF */ ?>