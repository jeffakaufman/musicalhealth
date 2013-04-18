<?php
# -*- coding: utf-8 -*-

//
//  Copyright (C) 2011 Sonoma Wire Works. All rights reserved.
//

function _SingFitAudioFileToolReadMagic($filepath, $size) {
	$magic = null;
	$fp = @fopen($filepath, 'rb');
	if (false !== $fp) {
		@fseek(0);
		$magic = @fread($fp, $size);
		@fclose($fp);
	}
	return $magic;
}

function SingFitAudioFileToolIsWAV($filepath) {
	return _SingFitAudioFileToolReadMagic($filepath, 4) == "RIFF" ? true : false;
}

function SingFitAudioFileToolIsMP3($filepath) {
	return _SingFitAudioFileToolReadMagic($filepath, 3) == "ID3" ? true : false;
}

function SingFitAudioFileToolWAVInfo($filepath, $sndfileinfobin = "/svr/bin/sndfile-info") {
	$stdout = array();
	$info = array(
		"bitwidth" => null,
		"blockalign" => null,
		"bytespersec" => null,
		"channels" => null,
		"datalength" => null,
		"duration" => null,
		"frames" => null,
		"length" => null,
		"samplerate" => null
	);
	$sndfileinfo = sprintf("%s \"%s\" 2>&1", $sndfileinfobin, $filepath);
	$status = -100;
	exec($sndfileinfo, $stdout, $status);
	if ($status == 0 && count($stdout)) {
		foreach ($stdout as $v) {
			if (preg_match("/^Error/", $v) || preg_match("/^System error/", $v)) {
				return false;
			}
			if (preg_match("/^([a-zA-Z\/ ]{1,25})+[ : ]+([a-zA-Z-0-9\.\:]{1,25})$/", $v, $matches)) {
				if (count($matches) == 3) {
					$key = trim($matches[1]);
					$val = trim($matches[2]);
					if ("Length" == $key) {
						$info['length'] = $val;
					} else if ("Block Align" == $key) {
						$info['blockalign'] = $val;
					} else if ("Bit Width" == $key) {
						$info['bitwidth'] = $val;
					} else if ("Bytes/sec" == $key) {
						$info['bytespersec'] = $val;
					} else if ("Sample Rate" == $key) {
						$info['samplerate'] = $val;
					} else if ("Frames" == $key) {
						$info['frames'] = $val;
					} else if ("data" == $key) {
						$info['datalength'] = $val;
					} else if ("Channels" == $key) {
						$info['channels'] = $val;
					} else if ("Duration" == $key) {
						$info['duration'] = $val;
					}
				}
			}
		}
		return $info;
	}
	return false;
}

/* EOF */ ?>