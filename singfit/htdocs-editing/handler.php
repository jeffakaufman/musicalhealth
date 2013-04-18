<?php
# -*- coding: utf-8 -*-

//
//  Copyright (C) 2011 Sonoma Wire Works. All rights reserved.
//

require_once '/svr/www/frameworks/singfit/SingFitEditingRequest.php';
require_once '/svr/www/frameworks/singfit/SingFitEditingResponse.php';

$sessionid = session_id();
if (empty($sessionid)) {
	session_start();
}

$actionname = SingFitEditingActionNameForRequest();
switch ($actionname) {
	case "editing.view":
	{
		$templateroot = dirname(__FILE__)."/resources";
		$response = null;
		if (null != ($response = SingFitEditingRequestForView($templateroot))) {
			SingFitEditingPrintHTMLResponse($response);
			exit(0);
		}
	}
	case "editing.downloadreport":
	{
		SingFitEditingRequestForReportDownload();
		exit(0);
	}
	case "editing.setproduct":
	{
		$response = null;
		if (null != ($response = SingFitEditingRequestForSetProduct())) {
			SingFitServicesPrintJSONResponse($response);
		}
		exit(0);
	}
	case "editing.getproductidentifier":
	{
		$response = null;
		if (null != ($response = SingFitEditingRequestForProductIdentifier())) {
			SingFitServicesPrintJSONResponse($response);
		}
		exit(0);
	}
	case "editing.setactivateproduct":
	{
		$response = null;
		if (null != ($response = SingFitEditingRequestForSetActivateProduct())) {
			SingFitServicesPrintJSONResponse($response);
		}
		exit(0);
	}
	case "editing.setsong":
	{
		$response = null;
		if (null != ($response = SingFitEditingRequestForSetSong())) {
			SingFitServicesPrintJSONResponse($response);
		}
		exit(0);
	}
	case "editing.findsong":
	{
		$response = null;
		if (null != ($response = SingFitEditingRequestForFingSong())) {
			SingFitServicesPrintJSONResponse($response);
		}
		exit(0);
	}
	break;
}

@header("location:/404");

/* EOF */ ?>