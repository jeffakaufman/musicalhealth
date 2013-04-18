<?php
# -*- coding: utf-8 -*-

//
//  Copyright (C) 2011 Sonoma Wire Works. All rights reserved.
//

function SingFitEditingJSONResponse($array) {
	return @json_encode($array);
}

function SingFitEditingPrintHTMLResponse($html) {
	@header("Content-Type: text/html; charset=utf-8");
	@header("Content-Length:".@strlen($html) + 1);
	echo $html;
}

function SingFitServicesPrintJSONResponse($json) {
	@header("Content-Type: text/plain; charset=utf-8");
	@header("Content-Length:".@strlen($json) + 1);
	echo $json;
}

/* EOF */ ?>