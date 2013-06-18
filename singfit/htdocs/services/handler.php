<?php
# -*- coding: utf-8 -*-

//
//  Copyright (C) 2011 Sonoma Wire Works. All rights reserved.
//

require_once "/svr/www/frameworks/singfit/SingFitServicesRequest.php";
require_once "/svr/www/frameworks/singfit/SingFitServicesResponse.php";
require_once "/svr/www/frameworks/singfit/SingFitReadStream.php";

$servicename = SingFitServicesNameForRequest();
switch ($servicename) {
	case "app.view": //default
	{
		$response = null;
		if (null != ($response = SingFitServicesRequestForView())) {
			SingFitServicesPrintXMLResponse($response);
			exit(0);
		}
	}
	break;
	case "playlist.view":
	{
		if (null != ($response = SingFitServicesRequestForSongInfo())) {
			SingFitServicesPrintXMLResponse($response);
			exit(0);
		}
	}	
	case "song.info":
	{
		if (null != ($response = SingFitServicesRequestForSongInfo())) {
			SingFitServicesPrintXMLResponse($response);
			exit(0);
		}
	}
	case "song.share":
	{
		if (null != ($response = SingFitServicesRequestForUploadAndShareSong())) {
			SingFitServicesPrintXMLResponse($response);
			exit(0);
		}
	}
	break;
	case "audio.stream.preview":
	{
		$streamfile = null;
		if (null != ($streamfile = SingFitServicesRequestForPreviewAudioStreamFile())) {
			SingFitReadStream($streamfile, 'audio/mpeg', 'preview');
			exit(0);
		}
	}
	case "audio.stream.mysong":
	{
		$streamfile = null;
		if (null != ($streamfile = SingFitServicesRequestForMySongAudioStreamFile())) {
			SingFitReadStream($streamfile, 'audio/mpeg', 'mysong');
			exit(0);
		}
	}
	break;
	case "store.buy":
	{
		$response = null;
		if (null != ($response = SingFitServicesRequestForBuyProduct())) {
			SingFitServicesPrintXMLResponse($response);
			exit(0);
		}
	}
	break;
	case "store.get":
	{
		$streamfile = null;
		if (null != ($streamfile = SingFitServicesRequestForSongDownload())) {
			SingFitReadStream($streamfile, 'application/octet-stream', basename($streamfile), true);
			exit(0);
		}
	}
	break;
}
//@header("location:/404");

/* EOF */ ?>