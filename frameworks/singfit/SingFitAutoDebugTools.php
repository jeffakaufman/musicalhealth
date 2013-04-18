<?php
# -*- coding: utf-8 -*-

//
//  Copyright (C) 2011 Sonoma Wire Works. All rights reserved.
//

require_once dirname(__FILE__).'/SingFitDatabaseConnection.php';

function __SingFitAutoDebugToolsAnalyzeMemory($obj, $deep = false)
{
	if (!is_scalar($obj)) {
		$usage = array('Total'=>strlen(serialize($obj)));
		while (list($prop, $propVal) = each($obj))  {
			if ($deep && (is_object($propVal) || is_array($propVal))) {
				$usage['Children'][$prop] = __SingFitAutoDebugToolsAnalyzeMemory($propVal);
			}
			else {
				$usage['Children'][$prop] = strlen(serialize($propVal));
			}
		}
		return $usage;
	} else {
		return strlen(serialize($obj));
	}
}

function __SingFitAutoDebugToolsMemoryShutdown() {
	__SingFitErrorLog(
		__FUNCTION__, 
		memory_get_peak_usage(true)
	);
	__SingFitErrorLog(
		__FUNCTION__, 
		memory_get_peak_usage(false)
	);
	__SingFitErrorLog(
		__FUNCTION__, 
		memory_get_usage(true)
	);
	__SingFitErrorLog(
		__FUNCTION__, 
		memory_get_usage(false)
	);
}

function __SingFitAutoDebugToolsDataBaseShutdown() {
	global $_singfit_db_conn_ref;
	global $_singfit_db_conn_ptr;
	
	if ($_singfit_db_conn_ref > 0) {
		__SingFitErrorLog(
			__FUNCTION__, 
			"_singfit_db_conn_ref > 0"
		);
	}
	
	__SingFitDataBaseDeleteConnection($_singfit_db_conn_ptr);
}

/* EOF */ ?>