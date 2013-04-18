<?php
# -*- coding: utf-8 -*-

//
//  Copyright (C) 2011 Sonoma Wire Works. All rights reserved.
//

function SingFitMusicEncoderWAVToWebMP3($wavsource , $mp3dest, $lamebin = "/svr/bin/lame") {
	$stdout = array();
	if ($wavsource == $mp3dest) {
		return -200;
	}
	$lamecmd = sprintf("%s -V3 --vbr-old -mj --resample 44.1 \"%s\" \"%s\" 2>&1", $lamebin, $wavsource, $mp3dest);
	$status = -100;
	exec($lamecmd, $stdout, $status);
	return $status;
}

/* EOF */ ?>