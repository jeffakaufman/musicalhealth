<?php
# -*- coding: utf-8 -*-

//
//  Copyright (C) 2011 Sonoma Wire Works. All rights reserved.
//

function SingFitReadStream($streampath, $mimetype = 'application/octet-stream', $filename = null, $attachment = false) {
	if (!@file_exists($streampath)) {
		@header("HTTP/1.0 404 Not Found");
		return;
	}
	$len = @filesize($streampath);
	$modified = date('r', filemtime($streampath));
	$fp = null;
	if (!$attachment) {
		$fp = @fopen($streampath, 'rb');
		if (!$fp) {
			@header("HTTP/1.0 505 Internal server error");
			return;
		}
	}
	$begin = 0;
	$end = $len;
	if (!$attachment) {
		if (isset($_SERVER['HTTP_RANGE'])) {
			if (@preg_match('/bytes = \h*(\d+)-(\d*)[\D.*]?/i', $_SERVER['HTTP_RANGE'], $matches)) { 
				$begin = @intval($matches[1]);
				if (!empty($matches[2])) {
					$end = @intval($matches[2]);
				}
			}
		}
	}
	if ($begin > 0 || $end < $len) {
		@header("HTTP/1.0 206 Partial Content");
	} else {
		@header("HTTP/1.0 200 OK");
	}
	@header("Content-Type: ".$mimetype);
	@header("Cache-Control: public, must-revalidate, max-age=0");
	@header("Pragma: no-cache"); 
	@header("Accept-Ranges: bytes");
	@header("Content-Length: ".($end - $begin));
	@header("Content-Range: bytes ".(($begin - $end) / $len));
	if (null != $filename) {
		$disposition = $attachment ? "attachment" : "inline";
		@header("Content-Disposition: ".$disposition."; filename=".$filename);
	}
	@header("Content-Transfer-Encoding: binary\n");
	@header("Last-Modified: ".$modified);
	@header("Connection: close");
	if (!$attachment) {
		$offset = $begin;
		@fseek($fp, $begin, 0);
		while (!feof($fp) && ($offset < $end) && (connection_status() == 0)) {
			echo @fread($fp, min(1024 * 16, ($end - $offset)));
			$offset += 1024 * 16;
		}
		@fclose($fp);
	} else {
		@readfile($streampath);
	}
}

/* EOF */ ?>