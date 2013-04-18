<?php
# -*- coding: utf-8 -*-

//
//  Copyright (C) 2012 Sonoma Wire Works. All rights reserved.
//

require_once dirname(__FILE__).'/SingFitDatabaseConnection.php';

function SingFitAppUserRegister($request) {
	
}

function SingFitAppUserExists($request) {

}

function SingFitAppUserAquireSessionId($user_id = 0, $link = false) {
	_SingFitDataBaseGetCurrentConnection($link);
	if ($link !== false && $user_id != 0) {
		
	}
	return null;
}

function SingFitAppUserAuth($request) {
	$link = false;
	$row = false;
	$res = false;
	$sql = null;
	$user_id = 0;
	$is_email = false;
	$is_auth = false;
	$bundleid = null;
	$apple_udid = null;
	$appuser_screen_or_email = null;
	$appuser_password = null;
	$appuser_session = null;
	$response = array();
	$response['errno'] = 500;
	$response['appuser_session'] = null;
	if (isset($request['POST']['appuser_session'])) {
		$appuser_session = $request['POST']['appuser_session'];
		if (false !== ($link = SingFitDataBaseConnect())) {
			$sql = "
				SELECT *
				FROM singfit_app_user_session
				WHERE session_id='".mysql_escape_string($appuser_session)."'
			";
			if (false !== ($res = mysql_query($sql, $link))) {
				if (mysql_num_rows($res) == 1) {
					while ($row = mysql_fetch_assoc($res)) {
						$is_auth = true;
						$user_id = $row['user_id'];
					}
				}
				mysql_free_result($res);
				if ($is_auth) {
					$response['errno'] = 0;
					$response['appuser_session'] = $appuser_session;
				} else {
					$response['errno'] = 100;
				}
			}
		} else {
			$response['errno'] = 101;
		}
		return SingFitServicesResponse($response);
	}
	if (isset($request['POST']['clientappid'])) {
		$bundleid = $request['POST']['clientappid'];
	}
	if (isset($request['POST']['clientidentifier'])) {
		$apple_udid = $request['POST']['clientidentifier'];
	}
	if ($apple_udid != null) {
		if (false !== ($link = SingFitDataBaseConnect())) {
			if (isset($request['POST']['appuser_screen_or_email'])) {
				$appuser_screen_or_email = $request['POST']['appuser_screen_or_email'];
				$pos = strpos($appuser_screen_or_email, "@");
				if ($pos !== false && $pos > 0) {
					if(!filter_var($appuser_screen_or_email, FILTER_VALIDATE_EMAIL)) {
						$appuser_screen_or_email = null;
						$response['errno'] = 200;
					} else {
						$is_email = true;
					}
				}
				if (isset($request['POST']['appuser_password'])) {
					$appuser_password = $request['POST']['appuser_password'];
				}
			}
			if ($appuser_screen_or_email != null && $appuser_password != null) {
				$sql = "
					SELECT *
					FROM singfit_app_user";
				if ($is_email) {
					$sql .= " WHERE email='".mysql_escape_string($appuser_screen_or_email)."' ";
				} else {
					$sql .= " WHERE screenname='".mysql_escape_string($appuser_screen_or_email)."' ";
				}
				$sql .= "
					AND password=AES_ENCRYPT('".mysql_escape_string($appuser_password)."', '".kSingFitAppUserSecret."')
					AND activated=1
					AND enabled=1
				";
				if (false !== ($res = mysql_query($sql, $link))) {
					if (mysql_num_rows($res) == 1) {
						while ($row = mysql_fetch_assoc($res)) {
							$is_auth = true;
							$user_id = $row['id'];
						}
					}
					mysql_free_result($res);
					if ($is_auth) {
						$appuser_session = SingFitAppUserAquireSessionId($user_id, $link);
						if ($appuser_session != null) {
							$response['errno'] = 0;
							$response['appuser_session'] = $appuser_session;
						}
					} else {
						$response['errno'] = 403;
					}
				} else {
					$response['errno'] = 201;
				}
			} else {
				$response['errno'] = 202;
			}
			SingFitDataBaseClose($link);
		}
	}
	return SingFitServicesResponse($response);
}

function SingFitAppUserUpdate($request) {

}

/* EOF */ ?>