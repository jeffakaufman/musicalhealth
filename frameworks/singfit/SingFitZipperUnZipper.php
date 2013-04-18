<?php
# -*- coding: utf-8 -*-

//
//  Copyright (C) 2011 Sonoma Wire Works. All rights reserved.
//

require_once dirname(__FILE__)."/SingFitCommonSettings.php";
require_once dirname(__FILE__)."/SingFitPathUtilities.php";

function SingFitUnZipFile($zipFile, $destinationPath = ".") {
	$zip = @zip_open(realpath($zipFile));
	$filepaths = array();
	if(!$zip) { 
		return $filepaths;
	}
	while($zentry = @zip_read($zip)) {
		$zdir = @dirname(@zip_entry_name($zentry));
		$zfilename = @zip_entry_name($zentry);
		if(!@zip_entry_open($zip, $zentry, "r")) {
			continue;
		}
		if(!is_dir($destinationPath.DIRECTORY_SEPARATOR.$zdir)) {
			_SingFitPathUtilitiesMakeDirRecursive($destinationPath.DIRECTORY_SEPARATOR.$zdir, 0755);
		}
		$zfilesize = @zip_entry_filesize($zentry);
		if(empty($zfilesize)) {
			continue;
		}
		$fp = @fopen($destinationPath.DIRECTORY_SEPARATOR.$zfilename, "wb");
		if ($fp !== false) {
			$filesize = $zfilesize;
			while ($filesize > 0) {
				$readsize = min($filesize, 10240);
				$filesize -= $readsize;
				$zentryread = @zip_entry_read($zentry, $readsize);
				@fwrite($fp, $zentryread);
			}
			@fclose($fp);
			$filepaths[] = $destinationPath.DIRECTORY_SEPARATOR.$zfilename;
		}
		@zip_entry_close($zentry);
	}
	@zip_close($zip);
	return $filepaths;
}

function SingFitZipFile($filenames = array(), $zipfilename = null, $overwrite = true) {
	$newfilenames = array();
	$fileadded = array();
	if(is_array($filenames)) {
		foreach($filenames as $f) {
			if(@file_exists($f)) {
				$newfilenames[] = $f;
			}
		}
	}
	if(count($newfilenames)) {
		$zip = new ZipArchive();
		if($zip->open($zipfilename, $overwrite ? ZIPARCHIVE::OVERWRITE : ZIPARCHIVE::CREATE) !== true) {
			return null;
		}
		foreach($newfilenames as $f) {
			$info = pathinfo($zipfilename);
			$entryname = $info['filename'].DIRECTORY_SEPARATOR.basename($f);
			$zip->addFile($f, $entryname);
			$fileadded[] = $zipfilename.$entryname;
		}
		$zip->close();
		SingFitThreadMicroSleep(2 * 1000 * 1000);
		if (@file_exists($zipfilename)) {
			return $fileadded;
		}
	} else {
		return null;
	}
}

/* EOF */ ?>