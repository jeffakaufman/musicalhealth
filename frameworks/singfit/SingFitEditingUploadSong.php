<?php
# -*- coding: utf-8 -*-

//
//  Copyright (C) 2011 Sonoma Wire Works. All rights reserved.
//

require_once dirname(__FILE__)."/SingFitCommonSettings.php";
require_once dirname(__FILE__)."/SingFitPathUtilities.php";
require_once dirname(__FILE__)."/SingFitZipperUnZipper.php";
require_once dirname(__FILE__)."/SingFitMusicEncoder.php";
require_once dirname(__FILE__)."/SingFitEditingSongManager.php";

function SingFitEditingUploadSong($request, $slug, $update = false) {
	@set_time_limit(0);
	if (isset($request['FILES']['songpackage'])) {
		if (!is_uploaded_file($request['FILES']['songpackage']['tmp_name'])) {
			return array(
					'errno' => 10000,
					'msg' => 'Please attach a zip archive file.'
				);
		}
		$errno = $request['FILES']['songpackage']['error'];	
		switch ($errno) {
			case UPLOAD_ERR_OK:
			{
				$nowgmt = gmdate("Y-m-d\TH:i:s\Z");
				$tmppath = kSingFitTmpDataRoot."/".$slug."_".$nowgmt;
				$zipfile = $tmppath."/songpackage.zip";
				$status = false;
				$tmperr = 500;
				$destpath = null;
				$destzip = null;
				$destmp3 = null;
				if (SingFitPathUtilitiesMakeDir($tmppath) == 0) {
					$tmperr = 501;
					if (false !== @move_uploaded_file($request['FILES']['songpackage']['tmp_name'], $zipfile)) {
						$tmperr = 502;
						$filepaths = SingFitUnZipFile($zipfile, $tmppath);
						$error = 1;
						$matchedfiles = SingFitEditingSongManagerCheckPackage($filepaths, $error);
						if ($error == 0) {
							$tmperr = 503;
							$tmpzip = $tmppath."/".$slug.".zip";
							$tmppreviewmp3 = $tmppath."/".$slug.".mp3";
							if (SingFitPathUtilitiesMoveFile($matchedfiles['preview'], $tmppreviewmp3) == 0) {
								SingFitThreadMicroSleep(300 * 1000);
								$tmperr = 504;
								unset($matchedfiles['preview']);
								$addedfiles = SingFitZipFile($matchedfiles, $tmpzip, true);
								if ($addedfiles != null && count($matchedfiles) == count($addedfiles)) {
									$tmperr = 505;
									$destpath = kSingFitStoreDataRoot."/".(substr($slug, 0, 2));
									$destzip = $destpath."/".$slug.".zip";
									$destmp3 = $destpath."/".$slug.".mp3";
									if (SingFitPathUtilitiesMakeDir($destpath) == 0) {
										$tmperr = 506;
										if (SingFitPathUtilitiesMoveFile($tmpzip, $destzip, true) == 0) {
											SingFitThreadMicroSleep(5 * 1000 * 1000);
											$tmperr = 507;
											if (SingFitPathUtilitiesMoveFile($tmppreviewmp3, $destmp3, true) == 0) {
												SingFitThreadMicroSleep(5 * 1000 * 1000);
												$tmperr = 0;
											}
										}
									}
								}
							}
						}
					}
					$msg = '';
					if ($tmperr >= 500) {
						$msg = 'Please provide a valid zip archive file.';
						if ($tmperr >= 503) {
							$msg = 'Request cancelled, internal server error.';
						}
						if ($update == false && $tmperr > 505) {
							if ($destzip != null) {
								SingFitPathUtilitiesRemoveFile($destzip);
							}
							if ($destmp3 != null) {
								SingFitPathUtilitiesRemoveFile($destmp3);
							}
							if ($destpath != null) {
								SingFitPathUtilitiesRemoveDir($destpath, false);
							}
						}
						
					}
					SingFitPathUtilitiesRemoveDir($tmppath, true);
					return array(
						'errno' => $tmperr,
						'msg' => $msg
					);
				}
			}	
			break;
			default:
				return array(
						'errno' => ($errno * 10000),
						'msg' => 'This request cannot be processed.'
					);
		}
	}
	return array(
			'errno' => 600,
			'msg' => 'This request cannot be processed.'
		);
}

/* EOF */ ?>