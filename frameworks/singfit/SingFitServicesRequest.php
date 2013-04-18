<?php
# -*- coding: utf-8 -*-

//
//  Copyright (C) 2011 Sonoma Wire Works. All rights reserved.
//

require_once dirname(__FILE__).'/SingFitCommonSettings.php';
require_once dirname(__FILE__).'/SingFitStoreManager.php';
require_once dirname(__FILE__).'/SingFitUploadSongMix.php';
require_once dirname(__FILE__).'/SingFitAppViews.php';

$_sigfit_services_request = null;

function _SingFitServicesIsDeveloperRequest() {
	$validrequest = false;
	if (!isset($_sigfit_services_request['DEVELOPER'])) {
		if (
			$_SERVER['REMOTE_ADDR'] == "99.8.139.39" || // Jeff Kaufman
			$_SERVER['REMOTE_ADDR'] == "173.228.86.114" || // sww-office
			$_SERVER['REMOTE_ADDR'] == "98.149.26.84" || // hayden-home
			$_SERVER['REMOTE_ADDR'] == "71.198.38.33" || // mmw-home
			$_SERVER['REMOTE_ADDR'] == "83.202.111.112" // mmw-home-paris
		) {
			$_sigfit_services_request['DEVELOPER'] = 'ON';
			$validrequest = true;
		}
	} else {
		$validrequest = true;
	}
	return $validrequest;
}

function SingFitServicesIsValidSecureRequest() {
	$validrequest = _SingFitServicesIsDeveloperRequest();
	//$validrequest = false;
	$request = SingFitServicesRequest();
	if (isset($request['POST']['clientauthkey']) && isset($_SERVER['HTTPS'])) {
		if ($request['POST']['clientauthkey'] == kSingFitServiceClientAuthKey) {
			$validrequest = true;
		}
	}
	if ($validrequest) {
		return $request;
	}
	unset($request);
	return null;
}

function SingFitServicesRequest() {
	global $_sigfit_services_request;
	if ($_sigfit_services_request === null) {
		$_sigfit_services_request = array();
		$_sigfit_services_request['POST'] = array();
		$_sigfit_services_request['GET'] = array();
		$_sigfit_services_request['FILES'] = array();
		foreach($_POST as $k => $v) {
			$_sigfit_services_request['POST'][$k] = $v;
		}
		foreach($_GET as $k => $v) {
			$_sigfit_services_request['GET'][$k] = $v;
		}
		foreach($_FILES as $k => $v) {
			$_sigfit_services_request['FILES'][$k] = $v;
		}
	}
	return $_sigfit_services_request;
}

function SingFitServicesRequestForSongInfo()
{
	$request = null;
	if (null != ($request = SingFitServicesIsValidSecureRequest())) {
		$slug = null;
		if (isset($request['POST']['songid'])) {
			$slug = $request['POST']['songid'];
		}
		return SingFitStoreSongInfoView($slug, null);
	}
	return null;
}

function SingFitServicesRequestForBuyProduct() {
	$request = null;
	if (null != ($request = SingFitServicesIsValidSecureRequest())) {
		return SingFitStoreManagerBuyProduct($request);
	}
	return null;
}

function SingFitServicesRequestForSongDownload() {
	$request = null;
	if (null != ($request = SingFitServicesIsValidSecureRequest())) {
		if (isset($request['POST']['storesongid'])) {
			$slug = null;
			if (isset($request['POST']['storesongid'])) {
				$slug = $request['POST']['storesongid'];
			}
			$streamfile = null;
			if ($slug) {
				$streamfile = kSingFitStoreDataRoot."/".(substr($slug, 0, 2))."/".$slug.".zip";
				if (@file_exists($streamfile)) {
					return $streamfile;
				}
			}
		}
	}
	return null;
}

function SingFitServicesRequestForPreviewAudioStreamFile() {
	$request = SingFitServicesRequest();
	if (isset($request['GET']['id'])) {
		$streamfile = null;
		$slug = SingFitDecrypt($request['GET']['id']);
		if ($slug) {
			$streamfile = kSingFitStoreDataRoot."/".(substr($slug, 0, 2))."/".$slug.".mp3";
		}
		if (!@file_exists($streamfile)) {
			$streamfile = kSingFitDataRoot."/alarm-error.mp3";
		}
		return $streamfile;
	}
	return null;
}

function SingFitServicesRequestForMySongAudioStreamFile() {
	$request = SingFitServicesRequest();
	if (isset($request['GET']['id'])) {
		$streamfile = null;
		$slug = $request['GET']['id'];
		$streamfile = kSingFitSharedSongDataRoot."/".(substr($slug, 0, 2))."/".$slug.".mp3";
		if (!@file_exists($streamfile)) {
			$streamfile = kSingFitDataRoot."/alarm-error.mp3";
		}
		return $streamfile;
	}
	return null;
}

function SingFitServicesRequestForUploadAndShareSong() {
	$request = null;
	if (null != ($request = SingFitServicesIsValidSecureRequest())) {
		return SingFitUploadAndShareSong($request);
	}
	return null;
}

function SingFitServicesRequestForView() {
	$request = null;
	if (null != ($request = SingFitServicesIsValidSecureRequest())) {
		$r = 'main';
		if (isset($request['GET']['r'])) {
			$r = $request['GET']['r'];
		}
		switch ($r) {
			case 'main':
				return SingFitAppMainView();
			break;
			case 'storecategory':
			case 'storefeaturedcategory':
				$featured = ($r == 'storefeaturedcategory') ? true : false;
				$tm = null;
				if (isset($request['GET']['tm'])) {
					$tm = $request['GET']['tm'];
				}
				return SingFitStoreCategoryView($featured, $tm);
			break;
			case 'storeproduct':
			case 'storefeaturedproduct':
				$featured = ($r == 'storefeaturedproduct') ? true : false;
				$tm = null;
				$cat = 0;
				if (isset($request['GET']['tm'])) {
					$tm = $request['GET']['tm'];
				}
				if (isset($request['GET']['cat'])) {
					$cat = $request['GET']['cat'];
				}
				return SingFitStoreProductView($cat, $featured, $tm);
			break;
		}
	}
	return null;
}

function SingFitServicesNameForRequest() {
	$request = SingFitServicesRequest();
	$name = "app.view";
	if (isset($request['GET']['sn'])) {
		$name = $request['GET']['sn'];
	}
	return $name;
}

/* EOF */ ?>