<?php
# -*- coding: utf-8 -*-

//
//  Copyright (C) 2011 Sonoma Wire Works. All rights reserved.
//

require_once dirname(__FILE__).'/SingFitAppModels.php';
require_once dirname(__FILE__).'/SingFitServicesResponse.php';

function SingFitStoreSongInfoView($slug = null, $tm = null) {
	$model = SingFitStoreSongInfoModel($slug, $tm = null);
	return SingFitServicesResponse($model);
}

function SingFitStoreProductView($idcat = 0, $featured = false, $tm = null) {
	$model = SingFitStoreProductModel($idcat, $featured, $tm);
	return SingFitServicesResponse($model);
}

function SingFitStoreCategoryView($featured = false, $tm = null) {
	$model = SingFitStoreCategoryModel($featured, $tm);
	return SingFitServicesResponse($model);
}

function SingFitAppMainView() {
	$model = SingFitAppMainModel();
	return SingFitServicesResponse($model);
}

/* EOF */ ?>