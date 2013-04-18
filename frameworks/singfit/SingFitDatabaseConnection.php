<?php
# -*- coding: utf-8 -*-

//
//  Copyright (C) 2011 Sonoma Wire Works. All rights reserved.
//

require_once dirname(__FILE__)."/SingFitCommonSettings.php";

$_singfit_db_conn_ref = 0;
$_singfit_db_conn_ptr = false;

function __SingFitDataBaseCreateConnection($hostname, $username, $password, $dbname) {
	$link = false;
	if (false !== ($link = @mysql_connect($hostname, $username, $password, true))) {
		if (@mysql_select_db($dbname, $link)) {
			return $link;
		}
		__SingFitDataBaseDeleteConnection($link);
	}
	return false;
}

function __SingFitDataBaseDeleteConnection(&$link = false) {
	if ($link !== false) {
		@mysql_close($link);
	}
}

function _SingFitDataBaseGetCurrentConnection(&$link = false) {
	global $_singfit_db_conn_ptr;
	if (false == $link) {
		if (false == $_singfit_db_conn_ptr) {
			$link = SingFitDataBaseConnect();
		} else {
			$link = $_singfit_db_conn_ptr;
		}
	}
}

function SingFitDataBaseConnect() {
	global $_singfit_db_conn_ref;
	global $_singfit_db_conn_ptr;
	if ($_singfit_db_conn_ptr === false) {
		$_singfit_db_conn_ref = 0;
	}
	if ($_singfit_db_conn_ref == 0) {
		$_singfit_db_conn_ptr = __SingFitDataBaseCreateConnection(
			_kSingFitDataBaseHost, 
			_kSingFitDataBaseUserName, 
			_kSingFitDataBaseUserPassword, 
			_kSingFitDatabaseName
		);
		if (false !== $_singfit_db_conn_ptr) {
			$_singfit_db_conn_ref = 1;
			return $_singfit_db_conn_ptr;
		}
		return false;
	} else {
		$_singfit_db_conn_ref++;
		return $_singfit_db_conn_ptr;
	}
	return false;
}

function SingFitDataBaseClose($link) {
	global $_singfit_db_conn_ref;
	global $_singfit_db_conn_ptr;
	if (false === $link) {
		$_singfit_db_conn_ref = 0;
		return;
	}
	if ($_singfit_db_conn_ref) {
		$_singfit_db_conn_ref--;
	}
	if ($_singfit_db_conn_ref == 0) {
		__SingFitDataBaseDeleteConnection($_singfit_db_conn_ptr);
		$_singfit_db_conn_ptr = false;
	}
}

/* EOF */ ?>