<?php
# -*- coding: utf-8 -*-

//
//  Copyright (C) 2011 Sonoma Wire Works. All rights reserved.
//

require_once dirname(__FILE__)."/SingFitServicesResponse.php";
require_once dirname(__FILE__)."/SingFitPathUtilities.php";
require_once dirname(__FILE__)."/SingFitZipperUnZipper.php";
require_once dirname(__FILE__)."/SingFitMusicEncoder.php";
require_once dirname(__FILE__)."/SingFitMakeUUID.php";

function SingFitUploadAndShareSong($request) {
	if (isset($request['FILES']['songsharemix'])) {
		if (!is_uploaded_file($request['FILES']['songsharemix']['tmp_name'])) {
			return SingFitServicesResponse(
				array(
					'errno' => 10000,
					'url' => 'None'
				)
			);
		}
		$errno = $request['FILES']['songsharemix']['error'];
		switch ($errno) {
			case UPLOAD_ERR_OK:
			{
				$slug = SingFitMakeUUIDV4(true);
				$link = false;
				$res = false;
				$guard = 0;
				if (false !== ($link = SingFitDataBaseConnect())) {
					$rescan = false;
					do {
						$sql = "SELECT id FROM singfit_mysong WHERE slug='".$slug."'";
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
				$nowgmt = gmdate("Y-m-d\TH:i:s\Z");
				$tmppath = kSingFitTmpDataRoot."/".$slug."_".$nowgmt;
				$zipfile = $tmppath."/songsharemix.zip";
				$tmpmp3 = $tmppath."/".$slug.".mp3";
				$status = false;
				$url = null;
				$errno = 500;
				if (SingFitPathUtilitiesMakeDir($tmppath) == 0) {
					$errno = 501;
					if (false !== @move_uploaded_file($request['FILES']['songsharemix']['tmp_name'], $zipfile)) {
						$filepaths = SingFitUnZipFile($zipfile, $tmppath);
						$wavefile = null;
						foreach($filepaths as $k => $v) {
							$vv = ltrim(rtrim($v));
							if (preg_match("/\/songsharemix\.wav$/", $vv)) {
								$wavefile = $vv;
								break;
							}
						}
						$errno = 502;
						if ($wavefile != null && @file_exists($wavefile)) {
							$errno = 503;
							if (SingFitMusicEncoderWAVToWebMP3($wavefile , $tmpmp3) == 0) {
								$destpath = kSingFitSharedSongDataRoot."/".(substr($slug, 0, 2));
								$destmp3 = $destpath."/".$slug.".mp3";
								$errno = 504;
								if (SingFitPathUtilitiesMakeDir($destpath) == 0) {
									$errno = 505;
									if (SingFitPathUtilitiesMoveFile($tmpmp3, $destmp3) == 0) {
										$link = false;
										$errno = 506;
										if (false !== ($link = SingFitDataBaseConnect())) {
											$apple_udid = null;
											if (isset($request['POST']['clientidentifier'])) {
												$apple_udid = $request['POST']['clientidentifier'];
											}
											$sql = "INSERT INTO singfit_mysong (apple_udid, slug, uploaded) VALUES('".$apple_udid."', '".$slug."', NOW())";
											@mysql_query($sql, $link);
											SingFitDataBaseClose($link);
										}
										$status = true;
										$errno = 0;
										$url = kSingFitAppMySongUrl."/".$slug;
									}
								}
							}
						}
					}
				}
				SingFitPathUtilitiesRemoveDir($tmppath, true);
				if ($status) {
					return SingFitServicesResponse(
						array(
							'errno' => $errno,
							'url' => $url
						)
					);
				}
				return SingFitServicesResponse(
					array(
						'errno' => $errno,
						'url' => 'None'
					)
				);
			}	
			break;
			default:
				return SingFitServicesResponse(
					array(
						'errno' => ($errno * 10000),
						'url' => 'None'
					)
				);
		}
	}
	return SingFitServicesResponse(
				array(
					'errno' => 600,
					'url' => 'None'
				)
			);
}

/* EOF */ ?>