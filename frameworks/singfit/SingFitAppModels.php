<?php
# -*- coding: utf-8 -*-

//
//  Copyright (C) 2011 Sonoma Wire Works. All rights reserved.
//

require_once dirname(__FILE__).'/../vendor/cfpropertylist-1.1.1/CFPropertyList.php';
require_once dirname(__FILE__).'/SingFitCryptor.php';
require_once dirname(__FILE__).'/SingFitDatabaseConnection.php';
require_once dirname(__FILE__).'/SingFitStoreManager.php';

function SingFitStoreSongInfoModel($slug = null, $lastpublished = null) {
	$link = false;
	$model = null;
	if ($lastpublished === null) {
		$lastpublished = time();
	}
	$model = array();
	$model['ModelName'] = 'SongInfo';
	$model['ModelType'] = 'Inline-flatten';
	$model['ModelPublished'] = new CFDate($lastpublished);
	$model['ModelItem'] = array();
	$model['ModelItem']['Title'] = null;
	$model['ModelItem']['Author'] = null;
	$model['ModelItem']['CanBeShared'] = false;
	$model['ModelItem']['Identifier'] = null;
	$model['ModelItem']['PreviewUrl'] = null;
	if (false !== ($link = SingFitDataBaseConnect())) {
		$row = null;
		$res = false;
		$sql = "SELECT title, author, slug, canbeshared FROM singfit_song WHERE singfit_song.slug='".$slug."'";
		if (false !== ($res = mysql_query($sql, $link))) {
			if (mysql_num_rows($res) == 1) {
				while ($row = mysql_fetch_assoc($res)) {
					$model['ModelItem']['Title'] = $row['title'];
					$model['ModelItem']['Author'] = $row['author'];
					$model['ModelItem']['CanBeShared'] = $row['canbeshared'] ? true : false;
					$model['ModelItem']['Identifier'] = $row['slug'];
					$model['ModelItem']['PreviewUrl'] = kSingFitAppServiceUrl."/?sn=audio.stream.preview&id=".SingFitEncrypt($row['slug']);
				}
			}
		}
		SingFitDataBaseClose($link);
	}
	return $model;
}



function SingFitStoreProductModel($idcat = 0, $featured = false, $lastpublished = null) {
	$link = false;
	$model = null;
	if ($lastpublished === null) {
		$lastpublished = time();
	}
	if (false !== ($link = SingFitDataBaseConnect())) {
		$row = null;
		$res = false;
		$sql = "";
		if ($idcat > 0) {
			$sql = "
				SELECT 
					store_product.id as store_product_id, 
					store_product.apple_product_id, 
					store_product.apple_product_name, 
					store_product.apple_product_price, 
					store_product.onlyforsubscriber, 
					store_product.freeforsubscriber, 
					store_product.freeforall, 
					store_product.bundle
				FROM store_product_to_category, store_product
				WHERE store_product_to_category.category_id = ".$idcat." 
					AND store_product.id = store_product_to_category.product_id 
					AND store_product.visible=1
					AND store_product.apple_product_type=0 
				ORDER BY store_product.apple_product_name
			";
		} else {
			$sql = "
				SELECT 
					store_product.id as store_product_id, 
					store_product.apple_product_id, 
					store_product.apple_product_name, 
					store_product.apple_product_price,
					store_product.onlyforsubscriber, 
					store_product.freeforsubscriber, 
					store_product.freeforall, 
					store_product.bundle
				FROM store_product
				WHERE store_product.visible=1 
					AND store_product.apple_product_type=0
				ORDER BY store_product.apple_product_name
			";
		}
		if (false !== ($res = mysql_query($sql, $link))) {
			$model = array();
			$model['ModelName'] = ($featured ? 'StoreFeaturedProduct' : 'StoreProduct');
			$model['ModelType'] = 'Collection';
			$model['ModelPublished'] = new CFDate($lastpublished);
			//$model['ModelUrl'] = kSingFitAppSecureServiceUrl.'/?sn=app.view&r='.($featured ? "storefeaturedproduct" : "storeproduct").'&cat='.$idcat.'&tm='.$lastpublished;
			$model['ModelUrl'] = kSingFitAppSecureServiceUrl.'/?sn=app.view&r='.($featured ? "storefeaturedproduct" : "storeproduct").'&cat='.$idcat;
			$model['ModelItems'] = array();
			if (mysql_num_rows($res) != 0) {
				$userid = 0;
				$request = null;
				if (null != ($request = SingFitServicesIsValidSecureRequest())) {
					if (isset($request['POST']['clientidentifier'])) {
						$userid = SingFitStoreUserIDWithAppleUDID($request['POST']['clientidentifier'], $link);
					}
				}
				while ($row = mysql_fetch_assoc($res)) {
					$sqlrow = "
						SELECT 
							singfit_song.title, singfit_song.author, singfit_song.slug, singfit_song.canbeshared
						FROM store_product_to_singfit_song, singfit_song 
						WHERE 
							store_product_to_singfit_song.product_id=".$row['store_product_id']." 
							AND store_product_to_singfit_song.song_id = singfit_song.id 
					";
					$resother = false;
					$rowother = null;
					if (false !== ($resother = mysql_query($sqlrow, $link))) {
						if (mysql_num_rows($resother) != 0) {
							$items = array();
							$songItems = array();
							while ($rowother = mysql_fetch_assoc($resother)) {
								$song = array();
								$song['Title'] = $rowother['title'];
								$song['Artist'] = $rowother['author']; // should go away beta compatibility
								$song['Author'] = $rowother['author'];
								$song['CanBeShared'] = $rowother['canbeshared'] ? true : false;
								$song['Identifier'] = $rowother['slug'];
								$song['PreviewUrl'] = kSingFitAppServiceUrl."/?sn=audio.stream.preview&id=".SingFitEncrypt($rowother['slug']);
								array_push($songItems, $song);
							}
							$isbundle = $row['bundle'] ? true : false;
							$productname = null;
							if ($isbundle) {
								$productname = $row['apple_product_name'];
							} else {
								$productname = $songItems[0]['Title'];
							}
							$items['Name'] = $productname;
							$items['Identifier'] = $row['apple_product_id'];
							$items['Price'] = $row['apple_product_price'];
							$items['Currency'] = 'USD';
							$items['OnlyForSubscriber'] = $row['onlyforsubscriber'] ? true : false;
							$items['FreeForSubscriber'] = $row['freeforsubscriber'] ? true : false;
							$items['FreeForAll'] = $row['freeforall'] ? true : false;
							$items['IsBundle'] = $isbundle;
							$items['Owned'] = false;
							if ($userid > 0) {
								if (null != $request) {
									$items['Owned'] = (null != SingFitStoreUserProductOwned($row['apple_product_id'], $userid, $link)) ? true : false;
								}
							}
							$items['Songs'] = $songItems;
							array_push($model['ModelItems'], $items);
						}
						mysql_free_result($resother);
					}
				}
			}
			mysql_free_result($res);
		}
		SingFitDataBaseClose($link);
	}
	return $model;
}

function SingFitStorePlaylistModel($idplaylist = 0, $idapp = 0) {
	$link = false;
	$model = null;
	if (false !== ($link = SingFitDataBaseConnect())) {
	   if ($idplaylist == 0)
	   {
			$model['ModelName'] = 'Playlist';
			$model['ModelType'] = 'Collection';
			$model['ModelPublished'] = new CFDate(time());
			$model['ModelUrl'] = kSingFitAppSecureServiceUrl.'/?sn=app.view&r=playlist';
			$model['ModelItems'] = array();	   
    	   require_once dirname(__FILE__).'/SingFitEditingModels.php';		
    		$playlists = SingFitAllPlaylistModel($idapp);
    		foreach($playlists['ModelItems'] as $playlist)
    		{
        		$playlistNode['Name'] = $playlist['name'];
        		$playlistNode['NextModelName'] = "Playlist";
        		$playlistNode['NextModelType'] = 'Collection';
        		$playlistNode['NextModelUrl'] = kSingFitAppSecureServiceUrl.'/?sn=app.view&r=playlist&id='. $playlist['id'];
        		$playlistNode['NextModelPublished'] = new CFDate(time());    		
        		array_push($model['ModelItems'], $playlistNode);
    		}	
    		return $model;
	   }
	   
		$row = null;
		$res = false;
		$sql = "SELECT 
					store_product.id as store_product_id, 
					store_product.apple_product_id, 
					store_product.apple_product_name, 
					store_product.apple_product_price,
					store_product.onlyforsubscriber, 
					store_product.freeforsubscriber, 
					store_product.freeforall, 
					store_product.bundle
				FROM store_product 
				INNER JOIN store_product_to_playlist as sp on sp.product_id = store_product.id and sp.playlist_id = %s
				WHERE store_product.visible=1 
					AND store_product.apple_product_type=0";
				$sql .= "ORDER BY sp.order, sp.id;
			";
			$sql = sprintf($sql, $idplaylist);
		if (false !== ($res = mysql_query($sql, $link))) {
			$model = array();
			$model['ModelName'] = 'Playlist';
			$model['ModelType'] = 'Collection';
			$model['ModelPublished'] = new CFDate(time());
			$model['ModelUrl'] = kSingFitAppSecureServiceUrl.'/?sn=app.view&r=playlist&id='.$idplaylist;
			$model['ModelItems'] = array();
			if (mysql_num_rows($res) != 0) {
				$userid = 0;
				$request = null;
				if (null != ($request = SingFitServicesIsValidSecureRequest())) {
					if (isset($request['POST']['clientidentifier'])) {
						$userid = SingFitStoreUserIDWithAppleUDID($request['POST']['clientidentifier'], $link);
					}
				}
				while ($row = mysql_fetch_assoc($res)) {
					$sqlrow = "
						SELECT 
							singfit_song.title, singfit_song.author, singfit_song.slug, singfit_song.canbeshared
						FROM store_product_to_singfit_song, singfit_song 
						WHERE 
							store_product_to_singfit_song.product_id=".$row['store_product_id']." 
							AND store_product_to_singfit_song.song_id = singfit_song.id 
					";
					$resother = false;
					$rowother = null;
					if (false !== ($resother = mysql_query($sqlrow, $link))) {
						if (mysql_num_rows($resother) != 0) {
							$items = array();
							$songItems = array();
							while ($rowother = mysql_fetch_assoc($resother)) {
								$song = array();
								$song['Title'] = $rowother['title'];
								$song['Artist'] = $rowother['author']; // should go away beta compatibility
								$song['Author'] = $rowother['author'];
								$song['CanBeShared'] = $rowother['canbeshared'] ? true : false;
								$song['Identifier'] = $rowother['slug'];
								//$song['PreviewUrl'] = kSingFitAppServiceUrl."/?sn=audio.stream.preview&id=".SingFitEncrypt($rowother['slug']);
								array_push($songItems, $song);
							}
							$isbundle = $row['bundle'] ? true : false;
							$productname = null;
							if ($isbundle) {
								$productname = $row['apple_product_name'];
							} else {
								$productname = $songItems[0]['Title'];
							}
							$items['Name'] = $productname;
							$items['Identifier'] = $row['apple_product_id'];
							$items['Price'] = $row['apple_product_price'];
							$items['Currency'] = 'USD';
							$items['OnlyForSubscriber'] = $row['onlyforsubscriber'] ? true : false;
							$items['FreeForSubscriber'] = $row['freeforsubscriber'] ? true : false;
							$items['FreeForAll'] = $row['freeforall'] ? true : false;
							$items['IsBundle'] = $isbundle;
							$items['Owned'] = false;
							if ($userid > 0) {
								if (null != $request) {
									$items['Owned'] = (null != SingFitStoreUserProductOwned($row['apple_product_id'], $userid, $link)) ? true : false;
								}
							}
							$items['Songs'] = $songItems;
							array_push($model['ModelItems'], $items);
						}
						mysql_free_result($resother);
					}
				}
			}
			mysql_free_result($res);
		}
		SingFitDataBaseClose($link);
	}
	return $model;
}

function SingFitStoreCategoryModel($featured = false, $lastpublished = null) {
	$link = false;
	$resother = false;
	$res = false;
	$model = null;
	$row = null;
	$rowother = null;
	$lastpublished = time();
	if (false !== ($link = SingFitDataBaseConnect())) {
		$menuhead = $featured ? '__head_featured__' : '__head_genre__';
		$sql = "SELECT a.id AS id, a.name AS name, UNIX_TIMESTAMP(a.published) AS published FROM store_product_category AS a INNER JOIN store_product_category AS b ON b.name='".$menuhead."' WHERE a.id_parent = b.id AND a.visible=1 ORDER BY a.name";
		if (false !== ($res = mysql_query($sql, $link))) {
			if ($lastpublished === null) {
				$lastpublished = time();
				$sql = "SELECT a.id AS id, a.name AS name, UNIX_TIMESTAMP(MAX(a.published)) AS published FROM store_product_category AS a INNER JOIN store_product_category AS b ON b.name='".$menuhead."' WHERE a.id_parent = b.id AND a.visible=1 ORDER BY a.name";
				if (false !== ($resother = mysql_query($sql, $link))) {
					if (mysql_num_rows($resother) == 1) {
						while ($rowother = mysql_fetch_assoc($resother)) {
							if (isset($rowother['published'])) {
								$lastpublished = $rowother['published'];
							}
						}
					}
					mysql_free_result($resother);
				}
			}
			$model = array();
			$model['ModelName'] = ($featured ? 'StoreFeaturedCategory' : 'StoreCategory');
			$model['ModelType'] = 'Collection';
			$model['ModelPublished'] = new CFDate($lastpublished);
			//$model['ModelUrl'] = kSingFitAppSecureServiceUrl.'/?sn=app.view&r='.($featured ? "storefeaturedcategory" : "storecategory").'&tm='.$lastpublished;
			$model['ModelUrl'] = kSingFitAppSecureServiceUrl.'/?sn=app.view&r='.($featured ? "storefeaturedcategory" : "storecategory");
			$model['ModelItems'] = array();
			$bundleid = _kSingFitStoreProductRoot;
			$appid = 1;
			if (null != ($request = SingFitServicesIsValidSecureRequest())) {
				if (isset($request['POST']['clientappid'])) {
					$bundleid = $request['POST']['clientappid'];
				}
				$appinfo = SingFitStoreGetAppInfo($bundleid);
				$appid = $appinfo['id'];
			}
			if (mysql_num_rows($res) != 0) {
				while ($row = mysql_fetch_assoc($res)) {
					$sqlcount = "
						SELECT 
							COUNT(store_product.id) as product_count 
						FROM store_product_to_category, store_product, store_app_to_category 
						WHERE store_app_to_category.app_id = ".$appid."
							AND store_app_to_category.category_id = ".$row['id']."
							AND store_product_to_category.category_id = ".$row['id']."
							AND store_product.id = store_product_to_category.product_id
							AND store_product.visible = 1
					";
					$productcount = 0;
					if (false !== ($resother = mysql_query($sqlcount, $link))) {
						while ($rowother = mysql_fetch_assoc($resother)) {
							$productcount = $rowother['product_count'];
						}
						if ($productcount) {
							$items = array();
							$items['Name'] = $row['name'];
							$items['NextModelName'] = 'StoreProduct';
							$items['NextModelType'] = 'Collection';
							$items['NextModelCount'] = $productcount;
							//$items['NextModelUrl'] = kSingFitAppSecureServiceUrl.'/?sn=app.view&r='.($featured ? "storefeaturedproduct" : "storeproduct").'&cat='.$row['id'].'&tm='.$row['published'];
							$items['NextModelUrl'] = kSingFitAppSecureServiceUrl.'/?sn=app.view&r='.($featured ? "storefeaturedproduct" : "storeproduct").'&cat='.$row['id'];
							$items['NextModelPublished'] = new CFDate($lastpublished);
							array_push($model['ModelItems'], $items);
						}
						mysql_free_result($resother);
					}
				}
			}
			mysql_free_result($res);
		}
		SingFitDataBaseClose($link);
	}
	return $model;
}

function SingFitAppMainModel() {
	$lastpublished = time();
	$model = array();
	$model['ModelName'] = 'Main';
	$model['ModelServiceDisabled'] = false;
	$model['ModelRemoveAllCaches'] = false;
	$model['ModelType'] = 'Collection';
	$model['ModelPublished'] = new CFDate($lastpublished);
	//$model['ModelUrl'] = kSingFitAppSecureServiceUrl.'/?sn=app.view&r=main&tm='.$lastpublished;
	$model['ModelUrl'] = kSingFitAppSecureServiceUrl.'/?sn=app.view&r=main';
	$model['ModelItems'] = array();
	$expiretime = 0;
	$userIsSubscriber = false;
	$corporate = false;
	$request = null;
	if (null != ($request = SingFitServicesIsValidSecureRequest())) {
		$userIsSubscriber = SingFitStoreUserIsSubscriber($request, false, $exp, $corporate);
		if ($userIsSubscriber) {
			$expiretime = $exp;
		}
		$settings['NextModelName'] = 'Settings';
		$settings['NextModelType'] = 'Inline-flatten';
		$settings['NextModelUrl'] = 'None';
		$settings['NextModelPublished'] = new CFDate($lastpublished);
		$settings['NextModelItem'] = array();
		if (!$corporate) {
			$settings['NextModelItem']['MonthlySubscriptionUserIsSubscriber'] = $userIsSubscriber;
			$settings['NextModelItem']['MonthlySubscriptionExpirationDate'] = new CFDate($expiretime);
			$settings['NextModelItem']['MonthlySubscriptionProductIdentifier'] = _kSingFitStoreProductMonthlySubscription;
		} else {
			$settings['NextModelItem']['CorporateAgreementUserIsSubscriber'] = $userIsSubscriber;
			$settings['NextModelItem']['CorporateAgreementExpirationDate'] = new CFDate($expiretime);
		}
		$settings['NextModelItem']['SongSharePostFileKey'] = 'songsharemix';
		$settings['NextModelItem']['SongSharePostFileZipName'] = 'songsharemix.zip';
		$settings['NextModelItem']['SongSharePostFileWavName'] = 'songsharemix.wav';
		$settings['NextModelItem']['SongSharePostFileUrl'] = kSingFitAppSecureServiceUrl.'/?sn=song.share';
		$settings['NextModelItem']['SongInfoUrl'] = kSingFitAppSecureServiceUrl.'/?sn=song.info';
		$settings['NextModelItem']['SongInfoIdentifier'] = 'songid';
		$settings['NextModelItem']['StoreServiceUrl'] = kSingFitAppSecureServiceUrl.'/?sn=store.buy';
		$settings['NextModelItem']['StoreDownloadSongUrl'] = kSingFitAppSecureServiceUrl.'/?sn=store.get';
		$settings['NextModelItem']['StoreDownloadSongIdentifier'] = 'storesongid';
		$settings['NextModelItem']['WebSupportUrl'] = kMusicalHealthTechHostUrl.'/support';
		$settings['NextModelItem']['WebPrivacyPolicyUrl'] = kMusicalHealthTechHostUrl.'/privacy-policy';
		
		array_push($model['ModelItems'], $settings);
		
		$storeCategory['NextModelName'] = 'StoreCategory';
		$storeCategory['NextModelType'] = 'Collection';
		//$storeCategory['NextModelUrl'] = kSingFitAppSecureServiceUrl.'/?sn=app.view&r=storecategory&tm='.$lastpublished;
		$storeCategory['NextModelUrl'] = kSingFitAppSecureServiceUrl.'/?sn=app.view&r=storecategory';
		$storeCategory['NextModelPublished'] = new CFDate($lastpublished);
		
		array_push($model['ModelItems'], $storeCategory);
		
		$storeFeatured['NextModelName'] = 'StoreFeaturedCategory';
		$storeFeatured['NextModelType'] = 'Collection';
		//$storeFeatured['NextModelUrl'] = kSingFitAppSecureServiceUrl.'/?sn=app.view&r=storefeaturedcategory&tm='.$lastpublished;
		$storeFeatured['NextModelUrl'] = kSingFitAppSecureServiceUrl.'/?sn=app.view&r=storefeaturedcategory';
		$storeFeatured['NextModelPublished'] = new CFDate($lastpublished);
		
		array_push($model['ModelItems'], $storeFeatured);

		$playlistNode['NextModelName'] = "Playlist";
		$playlistNode['NextModelType'] = 'Collection';
		$playlistNode['NextModelUrl'] = kSingFitAppSecureServiceUrl.'/?sn=app.view&r=playlist';
		$playlistNode['NextModelPublished'] = new CFDate($lastpublished);    		
		array_push($model['ModelItems'], $playlistNode);
	}
	return $model;
}

/* EOF */ ?>