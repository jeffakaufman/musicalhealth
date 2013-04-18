<?php
# -*- coding: utf-8 -*-

//
//  Copyright (C) 2011 Sonoma Wire Works. All rights reserved.
//

require_once dirname(__FILE__)."/SingFitCommonSettings.php";

function _SingFitPathUtilitiesMakeDirRecursive($pn, $mode = null) {
	if(is_dir($pn) || empty($pn)) {
		return true;
	}
	$pn = str_replace(array('/', ''), DIRECTORY_SEPARATOR, $pn);
	if(is_file($pn)) { 
		return false;
	}
	$next_pathname = substr($pn, 0, strrpos($pn, DIRECTORY_SEPARATOR));
	if(_SingFitPathUtilitiesMakeDirRecursive($next_pathname, $mode)) {
		if(!file_exists($pn)) {
			return mkdir($pn, $mode);
		}
	}
	return false;
}

function SingFitPathUtilitiesFileSize($filepath) {
	if (SingFitPathUtilitiesIsRegularFile($filepath)) {
		$stdout = array();
		$cmd = sprintf("wc -c \"%s\" | awk '{ print $1 }' 2>&1", $filepath);
		$status = -100;
		exec($cmd, $stdout, $status);
		if ($status == 0 && count($stdout)) {
			return trim($stdout[0]);
		}
	}
	return false;
}

function SingFitPathUtilitiesBaseName($filepath) {
	return basename($filepath);
}

function SingFitPathUtilitiesDirectoryName($filepath) {
	return dirname($filepath);
}

function SingFitPathUtilitiesRealPath($filepath) {
	return realpath($filepath);
}

function SingFitPathUtilitiesIsSymbolicLink($filepath) {
	$stdout = array();
	$cmd = sprintf("eval 'if [ -L \"%s\" ]; then echo 1; else echo 0; fi' 2>&1", $filepath);
	$status = -100;
	exec($cmd, $stdout, $status);
	if ($status == 0 && count($stdout)) {
		if (trim($stdout[0]) == "1") {
			return true;
		}
	}
	return false;
}

function SingFitPathUtilitiesIsRegularFile($filepath) {
	$stdout = array();
	$cmd = sprintf("eval 'if [ -f \"%s\" ]; then echo 1; else echo 0; fi' 2>&1", $filepath);
	$status = -100;
	exec($cmd, $stdout, $status);
	if ($status == 0 && count($stdout)) {
		if (trim($stdout[0]) == "1") {
			return true;
		}
	}
	return false;
}

function SingFitPathUtilitiesIsDirectory($filepath) {
	$stdout = array();
	$cmd = sprintf("eval 'if [ -d \"%s\" ]; then echo 1; else echo 0; fi' 2>&1", $filepath);
	$status = -100;
	exec($cmd, $stdout, $status);
	if ($status == 0 && count($stdout)) {
		if (trim($stdout[0]) == "1") {
			return true;
		}
	}
	return false;
}

function SingFitPathUtilitiesChangeModes($filepath, $mode = 1777, $recursive = false) {
	$stdout = array();
	$cmd = null;
	if ($recursive) {
		$cmd = sprintf("chmod -R %s 2>&1", $mode);
	} else {
		$cmd = sprintf("chmod %s 2>&1", $mode);
	}
	$status = -100;
	exec($cmd, $stdout, $status);
	return $status;
}

function SingFitPathUtilitiesMakeDir($dirpath) {
	$stdout = array();
	$cmd = sprintf("mkdir -p \"%s\" 2>&1", $dirpath);
	$status = -100;
	exec($cmd, $stdout, $status);
	return $status;
}

function SingFitPathUtilitiesRemoveDir($dirpath, $recursive = false) {
	$stdout = array();
	$cmd = null;
	if ($recursive) {
		$cmd = sprintf("rm -Rf \"%s\" 2>&1", $dirpath);
	} else {
		$cmd = sprintf("rmdir \"%s\" 2>&1", $dirpath);
	}
	$status = -100;
	exec($cmd, $stdout, $status);
	return $status;
}

function SingFitPathUtilitiesRemoveFile($filepath, $isdir = false, $recursive = false) {
	if ($isdir) {
		return SingFitPathUtilitiesRemoveDir($filepath, $recursive);
	}
	$stdout = array();
	$cmd = sprintf("rm -f \"%s\" 2>&1", $filepath);
	$status = -100;
	exec($cmd, $stdout, $status);
	return $status;
}

function SingFitPathUtilitiesMoveFile($inpath, $outpath, $overwrite = true) {
	$status = -100;
	if (!file_exists($inpath)) {
		return $status;
	}
	if ($inpath == $outpath) {
		return 0;
	}
	$stdout = array();
	$cmd = sprintf("mv %s \"%s\" \"%s\" 2>&1", ($overwrite == true ? "-f" : "-n"), $inpath, $outpath);
	exec($cmd, $stdout, $status);
	return $status;
}

function SingFitPathUtilitiesMoveRegularFile($inpath, $outpath, $atomic = true, $secure = true) {
	return SingFitPathUtilitiesMoveFile($inpath, $outpath, true);
}

/* EOF */ ?>