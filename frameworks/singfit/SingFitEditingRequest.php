<?php
# -*- coding: utf-8 -*-

//
//  Copyright (C) 2011 Sonoma Wire Works. All rights reserved.
//

require_once dirname(__FILE__)."/SingFitEditingViews.php";
require_once dirname(__FILE__)."/SingFitEditingProductManager.php";
require_once dirname(__FILE__)."/SingFitEditingPlaylistManager.php";
require_once dirname(__FILE__)."/SingFitEditingGenreManager.php";
require_once dirname(__FILE__)."/SingFitEditingSongManager.php";
require_once dirname(__FILE__)."/SingFitEditingUploadSong.php";
require_once dirname(__FILE__)."/SingFitEditingResponse.php";

$_sigfit_editing_request = null;

function SingFitEditingRequestNoCachedResponse() {
	@header("Pragma:public");
	@header("Expires:0"); 
	@header("Cache-Control:must-revalidate, post-check=0, pre-check=0"); 
	@header("Cache-Control:private", false);
}

function SingFitEditingIsValidSecureRequest() {
	$validrequest = false;
	$request = SingFitEditingRequest();
	if (isset($_SERVER['HTTPS'])) {
		$validrequest = true;
		if (isset($request['POST']['serverauthkey'])) {
			$validrequest = false;
			$sessionid = session_id();
			if (strlen($sessionid) && $request['POST']['serverauthkey'] == $sessionid) {
				$validrequest = true;
			}
		}
	}
	if ($validrequest) {
		return $request;
	}
	unset($request);
	return null;
}

function SingFitEditingRequest() {
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

function SingFitEditingRequestForView($templateroot) {
	$request = null;
	if (null != ($request = SingFitEditingIsValidSecureRequest())) {
		$r = 'main';
		if (isset($request['GET']['r'])) {
			$r = $request['GET']['r'];
		}
		switch ($r) {
			case 'main':
			case 'allproducts':
				return SingFitEditingAllProductView(
					$templateroot."/editing-allproducts.html"
				);
			break;
			case 'allsongs':
				return SingFitEditingAllSongView(
					$templateroot."/editing-allsongs.html"
				);
			break;
			case 'editproduct':
			case 'newproduct':
				SingFitEditingRequestNoCachedResponse();
				$idproduct = 0;
				if (isset($request['GET']['idproduct'])) {
					$idproduct = $request['GET']['idproduct'];
				}
				return SingFitEditingSetProductView(
						$templateroot."/editing-setproduct.html",
						$idproduct
					);
			break;
			case 'editsong':
			case 'newsong':
				SingFitEditingRequestNoCachedResponse();
				$idsong = 0;
				if (isset($request['GET']['idsong'])) {
					$idsong = $request['GET']['idsong'];
				}
				return SingFitEditingSetSongView(
						$templateroot."/editing-setsong.html",
						$idsong
					);
			break;
			case 'catalogreport':
				SingFitEditingRequestNoCachedResponse();
				return SingFitEditingSetCatalogReportView(
						$templateroot."/editing-catalogreport.html"
					);
			break;
			
			case 'newplaylist':
			case 'editplaylist':			
				SingFitEditingRequestNoCachedResponse();
				$idplaylist = 0;
				if (isset($request['GET']['idplaylist'])) {
					$idplaylist = $request['GET']['idplaylist'];
				}
				return SingFitEditingSetPlaylistView(
						$templateroot."/editing-setplaylist.html",
						$idplaylist
					);											
			break;
						
			case 'newgenre':
			case 'editgenre':			
				SingFitEditingRequestNoCachedResponse();
				$idgenre = 0;
				if (isset($request['GET']['idgenre'])) {
					$idgenre = $request['GET']['idgenre'];
				}
				return SingFitEditingSetGenreView(
						$templateroot."/editing-setgenre.html",
						$idgenre
					);											
			break;			

			case 'allgenres':
				return SingFitEditingAllGenreView(
					$templateroot."/editing-allgenres.html"
				);
			break;		
			
			case 'allplaylists':
				return SingFitEditingAllPlaylistView(
					$templateroot."/editing-allplaylists.html"
				);
			break;			
		}
	}
	return null;
}

function SingFitEditingRequestForReportDownload()
{
	$data = null;
	$filename = null;
	if (null != ($request = SingFitEditingIsValidSecureRequest())) {
		$data = SingFitEditingSongManagerGetReport($request);
		if (isset($request['GET']['t']) && $request['GET']['t'] == "raw") {
			$filename = "singfit-catalog-raw-report-".mysql_escape_string($request['GET']['d']).".csv";
		} else {
			$filename = "singfit-catalog-precalculated-report-".mysql_escape_string($request['GET']['d']).".txt";
		}
	}
//	@header("Content-Type: application/octet-stream");
//	@header("Cache-Control: public, must-revalidate, max-age=0");
//	@header("Pragma: no-cache");
//	@header("Content-Disposition: attachment; filename=".$filename);
//	@header("Content-Transfer-Encoding: binary\n");
	echo $data;
}

function SingFitEditingRequestForSetSong() {
	if (null != ($request = SingFitEditingIsValidSecureRequest())) {
		$response = array(
			'errno' => 0,
			'msg' => ''
		);
		if (!isset($request['POST']['q'])) {
			$response['errno'] = 100;
			$response['msg'] = 'Wrong request.';
		} else if ($request['POST']['q'] != 9000 && $request['POST']['q'] != 9001) {
			$response['errno'] = 200;
			$response['msg'] = 'Wrong request.';
		} else if (strlen(trim($request['POST']['title'])) < 1) {
			$response['errno'] = 300;
			$response['msg'] = 'Please provide a song title.';
		} else if (strlen(trim($request['POST']['author'])) < 1) {
			$response['errno'] = 400;
			$response['msg'] = 'Please provide a song author.';
		}
		if ($response['errno'] == 0) {
			$update = false;
			if ($request['POST']['q'] == 9001) {
				$update = true;
			}
			$slug = $request['POST']['slug'];
			if (empty($slug)) {
				$update = false;
				$slug = SingFitEditingSongManagerNewSlug();
			}
			if ($update) {
				if (isset($request['FILES']['songpackage']) && !empty($request['FILES']['songpackage']['tmp_name'])) {
					$response = SingFitEditingUploadSong($request, $slug, $update);
				}
				if (false === SingFitEditingSongManagerSetSong($request, $slug, $update)) {
					if ($response['errno'] == 0) {
						$response['errno'] = 508;
						$response['msg'] = 'This request cannot be processed. Try Again.';
					}
				}
			} else {
				if (SingFitEditingSongManagerSongExists($request['POST']['title'])) {
					$response['errno'] = 800;
					$response['msg'] = 'This song title already exists.';
				} else {
					$response = SingFitEditingUploadSong($request, $slug, $update);
					if ($response['errno'] == 0) {
						if (false === SingFitEditingSongManagerSetSong($request, $slug, $update)) {
							$response['errno'] = 508;
							$response['msg'] = 'This request cannot be processed. Try Again.';
						}
					}
				}
			}
		}
		return SingFitEditingJSONResponse($response);
	}
	return null;
}

function SingFitEditingRequestForFingSong() {
	if (null != ($request = SingFitEditingIsValidSecureRequest())) {
		$response = SingFitEditingSongManagerFindSong($request);
		return SingFitEditingJSONResponse($response);
	}
	return null;
}

function SingFitEditingRequestForProductIdentifier()
{
	if (null != ($request = SingFitEditingIsValidSecureRequest())) {
		$response = array(
			'errno' => 0,
			'identifier' => ''
		);
		if (!isset($request['POST']['name']) || empty($request['POST']['name'])) {
			$response['errno'] = 1;
			$response['identifier'] = '';
		} else {
			$response['errno'] = 0;
			$response['identifier'] = SingFitEditingProductManagerIdentifierUsingName($request['POST']['name']);
		}
		return SingFitEditingJSONResponse($response);
	}
	return null;
}

function SingFitEditingRequestForSetActivateProduct()
{
	if (null != ($request = SingFitEditingIsValidSecureRequest())) {
		$response = array(
			'errno' => 0
		);
		if (!isset($request['POST']['id'])) {
			$response['errno'] = 1;
		} else if (!isset($request['POST']['activate'])) {
			$response['errno'] = 2;
		} else {
			$response['errno'] = 0;
			SingFitEditingProductManagerSetActivateProduct($request['POST']['id'], $request['POST']['activate']);
		}
		return SingFitEditingJSONResponse($response);
	}
	return null;
}

function SingFitEditingRequestForSetPlaylist() {
	if (null != ($request = SingFitEditingIsValidSecureRequest())) {
		$response = array(
			'errno' => 0,
			'msg' => ''
		);
		if (!isset($request['POST']['q'])) {
			$response['errno'] = 100;
			$response['msg'] = 'Wrong request.';
		} else if ($request['POST']['q'] != 9004 && $request['POST']['q'] != 9005) {
			$response['errno'] = 200;
			$response['msg'] = 'Wrong request.';
		} else if (strlen(trim($request['POST']['name'])) < 1) {
			$response['errno'] = 300;
			$response['msg'] = 'Please provide a playlist name.';
		}
		if ($response['errno'] == 0) {
			$update = false;
			if ($request['POST']['q'] == 9005) {
				$update = true;
			}
			$idplaylist = $request['POST']['id'];
			if (empty($idplaylist)) {
				$update = false;
			}
			if (!$update && SingFitEditingPlaylistManagerPlaylistExists($request['POST']['name'])) {
				$response['errno'] = 800;
				$response['msg'] = 'This playlist already exists.';
			}
			if ($response['errno'] == 0) {
				if (false === SingFitEditingPlaylistManagerSetPlaylist($request, $idplaylist, $update)) {
					$response['errno'] = 808;
					$response['msg'] = 'This request cannot be processed. Try Again.';
				}
			}
		}
		return SingFitEditingJSONResponse($response);
	}
	return null;
}

function SingFitEditingRequestForDeletePlaylist() {
	if (null != ($request = SingFitEditingIsValidSecureRequest())) {
		$response = array(
			'errno' => 0,
			'msg' => ''
		);
		if (!isset($request['POST']['q'])) {
			$response['errno'] = 100;
			$response['msg'] = 'Wrong request.';
		} else if ($request['POST']['q'] != 992) {
			$response['errno'] = 200;
			$response['msg'] = 'Wrong request.';
		} else if (!isset($request['POST']['id'])) {
			$response['errno'] = 300;
			$response['msg'] = 'Please provide a playlist id.';
		}
		if ($response['errno'] == 0) {
			if ($response['errno'] == 0) {
				if (false === SingFitEditingPlaylistManagerDeletePlaylist($request)) {
					$response['errno'] = 808;
					$response['msg'] = 'This request cannot be processed. Try Again.';
				}
			}
		}
		return SingFitEditingJSONResponse($response);
	}
	return null;
}

function SingFitEditingRequestForDeleteGenre() {
	if (null != ($request = SingFitEditingIsValidSecureRequest())) {
		$response = array(
			'errno' => 0,
			'msg' => ''
		);
		if (!isset($request['POST']['q'])) {
			$response['errno'] = 100;
			$response['msg'] = 'Wrong request.';
		} else if ($request['POST']['q'] != 991) {
			$response['errno'] = 200;
			$response['msg'] = 'Wrong request.';
		} else if (!isset($request['POST']['id'])) {
			$response['errno'] = 300;
			$response['msg'] = 'Please provide a playlist id.';
		}
		if ($response['errno'] == 0) {
			if ($response['errno'] == 0) {
				if (false === SingFitEditingGenreManagerDeleteGenre($request)) {
					$response['errno'] = 808;
					$response['msg'] = 'This request cannot be processed. Try Again.';
				}
			}
		}
		return SingFitEditingJSONResponse($response);
	}
	return null;
}

function SingFitEditingRequestForSetProduct() {
	if (null != ($request = SingFitEditingIsValidSecureRequest())) {
		$response = array(
			'errno' => 0,
			'msg' => ''
		);
		if (!isset($request['POST']['q'])) {
			$response['errno'] = 100;
			$response['msg'] = 'Wrong request.';
		} else if ($request['POST']['q'] != 9002 && $request['POST']['q'] != 9003) {
			$response['errno'] = 200;
			$response['msg'] = 'Wrong request.';
		} else if (strlen(trim($request['POST']['apple_product_name'])) < 1) {
			$response['errno'] = 300;
			$response['msg'] = 'Please provide a product name.';
		} else if (floatval($request['POST']['apple_product_price']) == 0 && !isset($request['POST']['freeforall']) && !isset($request['POST']['onlyforsubscriber'])) {
			$response['errno'] = 400;
			$response['msg'] = 'Please provide a product price greater than 0.00, any non-free product must be replicated on iTunes App Store, then activated.';
		} else if (floatval($request['POST']['apple_product_price']) == 0 && isset($request['POST']['onlyforsubscriber']) && !isset($request['POST']['freeforsubscriber'])) {
			$response['errno'] = 500;
			$response['msg'] = 'Please provide a product price greater than 0.00, any non-free product must be replicated on iTunes App Store, then activated.';
		}
		if ($response['errno'] == 0) {
			$genres = json_decode($request['POST']['attached_genres']);
			$features = json_decode($request['POST']['attached_features']);
			$playlists = json_decode($request['POST']['attached_playlists']);			
			$err = 1;
			$category = null;
			if (is_array($genres) && is_array($features)) {
				$category = array_merge($genres, $features);
				if (count($category)) {
					$err = 0;
				}
			}
			if ($err) {
				$response['errno'] = 600;
				$response['msg'] = 'Please attach at least one category, genre or feature.';
			}
			
		}
		if ($response['errno'] == 0) {
			$songs = json_decode($request['POST']['attached_songs']);
			$err = 1;
			if (is_array($songs) && count($songs)) {
				$err = 0;
			}
			if ($err) {
				$response['errno'] = 700;
				$response['msg'] = 'Please attach at least one song, several to create a song package.';
			}
		}
		if ($response['errno'] == 0) {
			$update = false;
			if ($request['POST']['q'] == 9003) {
				$update = true;
			}
			$slug = $request['POST']['slug'];
			if (empty($slug)) {
				$update = false;
				$slug = SingFitEditingProductManagerNewSlug();
			}
			if (!$update && SingFitEditingProductManagerProductExists(SingFitEditingProductManagerIdentifierUsingName($request['POST']['apple_product_name']))) {
				$response['errno'] = 800;
				$response['msg'] = 'This product identifier already exists.';
			}
			if ($response['errno'] == 0) {
				if (false === SingFitEditingProductManagerSetProduct($request, $slug, $update)) {
					$response['errno'] = 808;
					$response['msg'] = 'This request cannot be processed. Try Again.';
				}
			}
		}
		return SingFitEditingJSONResponse($response);
	}
	return null;
}

function SingFitEditingRequestForSetGenre() {
	if (null != ($request = SingFitEditingIsValidSecureRequest())) {
		$response = array(
			'errno' => 0,
			'msg' => ''
		);
		if (!isset($request['POST']['q'])) {
			$response['errno'] = 100;
			$response['msg'] = 'Wrong request.';
		} else if ($request['POST']['q'] != 9007 && $request['POST']['q'] != 9008) {
			$response['errno'] = 200;
			$response['msg'] = 'Wrong request.';
		} else if (strlen(trim($request['POST']['name'])) < 1) {
			$response['errno'] = 300;
			$response['msg'] = 'Please provide a genre name.';
		}
		if ($response['errno'] == 0) {
			$err = 1;			
		}
		if ($response['errno'] == 0) {
			$update = false;
			if ($request['POST']['q'] == 9008) {
				$update = true;
			}
			if (!$update && SingFitEditingGenreManagerGenreExists($request['POST']['name'])) {
				$response['errno'] = 800;
				$response['msg'] = 'This genre already exists.';
			}
			if ($response['errno'] == 0) {
				if (false === SingFitEditingGenreManagerSetGenre($request, $update)) {
					$response['errno'] = 808;
					$response['msg'] = 'This request cannot be processed. Try Again.';
				}
			}
		}
		return SingFitEditingJSONResponse($response);
	}
	return null;
}

function SingFitEditingActionNameForRequest() {
	$request = SingFitEditingRequest();
	$name = "editing.view";
	if (isset($request['GET']['an'])) {
		$name = $request['GET']['an'];
	}
	return $name;
}

/* EOF */ ?>