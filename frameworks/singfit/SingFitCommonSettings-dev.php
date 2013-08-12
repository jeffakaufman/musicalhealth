<?php
# -*- coding: utf-8 -*-

//
//  Copyright (C) 2011 Sonoma Wire Works. All rights reserved.
//

date_default_timezone_set('America/Los_Angeles');

define('_kSingFitDataBaseHost', 'localhost');
define('_kSingFitDataBaseUserName', 'singfitapp');
define('_kSingFitDataBaseUserPassword', 'me#sing!@44');

define('_kSingFitDatabaseName', 'singfit');

define('_kSingFitAppleStoreIsSandBox', false);

define('_kSingFitStoreProductRoot', 'com.musicalhealthtech.SingFit');
define('_kSingFitStoreProductMonthlySubscription', _kSingFitStoreProductRoot.'.MonthlySubscription');
define('kSingFitServiceClientAuthKey', '53575754424854545055524c446f776e6c6f61646572416c6c6f77416c');

define('kSingFitAppUserSecret', '@33user!sing@all');

define('kMusicalHealthTechHostname', 'www.musicalhealthtechdev.com');
define('kMusicalHealthTechHostUrl', 'http://'.kMusicalHealthTechHostname);

define('kSingFitHostname', 'singfit.musicalhealthtechdev.com');
define('kSingFitAppServiceUrl', 'http://'.kSingFitHostname.'/services');
define('kSingFitAppSecureServiceUrl', 'https://'.kSingFitHostname.'/services');
define('kSingFitAppMySongUrl', 'http://'.kSingFitHostname.'/mysong');

define('kSingFitDataRoot', '/svr/data/www/singfit');
define('kSingFitStoreDataRoot', '/svr/data/www/singfit/store');
define('kSingFitSharedSongDataRoot', '/svr/data/www/singfit/shared');
define('kSingFitTmpDataRoot', '/svr/data/tmp');

define('_kSingFitDebugOutput', kSingFitTmpDataRoot.'/singfit-debug.log');

function SingFitThreadMicroSleep($microseconds = 0)
{
	@usleep($microseconds);
}

function __SingFitErrorLog($context = null, $data = null) {
	//if (_kSingFitAppleStoreIsSandBox == true) {
		if (!@is_scalar($data)) {
			$data = @serialize($data);
		}
		@error_log("+- ".$context.":".$data." \n", 3, _kSingFitDebugOutput);
		return;
	//}
}

/* if (_kSingFitAppleStoreIsSandBox == true) {
	require_once dirname(__FILE__).'/SingFitAutoDebugTools.php';
	register_shutdown_function('__SingFitAutoDebugToolsDataBaseShutdown');
	register_shutdown_function('__SingFitAutoDebugToolsMemoryShutdown');
} */

/* EOF */ ?>