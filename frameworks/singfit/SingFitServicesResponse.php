<?php
# -*- coding: utf-8 -*-

//
//  Copyright (C) 2011 Sonoma Wire Works. All rights reserved.
//

require_once dirname(__FILE__).'/../vendor/cfpropertylist-1.1.1/CFPropertyList.php';

function SingFitServicesResponse($array) {
	$plist = new CFPropertyList();
	$typeDetector = new CFTypeDetector();
	$plist->add($typeDetector->toCFType($array));
	return $plist;
}

function SingFitServicesPrintXMLResponse($plist) {
	//return SingFitServicesPrintBinaryResponse($plist);
	@header("Content-Type: text/xml; charset=utf-8");
	$xml = @$plist->toXML(true);
	@header("Content-Length:".@strlen($xml) + 1);
	echo $xml;
}

function SingFitServicesPrintBinaryResponse($plist) {
	@header("Content-Type: application/octet-stream");
	@header("Content-Disposition: attachment");
	@header("Content-Transfer-Encoding: binary\n");
	$binary = @$plist->toBinary();
	@header("Content-Length:".@strlen($binary) + 1);
	echo $binary;
}

/* EOF */ ?>