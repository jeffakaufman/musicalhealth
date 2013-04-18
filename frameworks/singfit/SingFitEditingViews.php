<?php
# -*- coding: utf-8 -*-

//
//  Copyright (C) 2011 Sonoma Wire Works. All rights reserved.
//

require_once dirname(__FILE__).'/SingFitCommonSettings.php';
require_once dirname(__FILE__).'/SingFitEditingModels.php';
require_once dirname(__FILE__)."/SingFitPathUtilities.php";

function SingFitEditingEllipsisText($text, $max = 80, $append = '...') {
	if (strlen($text) <= $max) {
		return $text;
	}
	$out = substr($text, 0, $max - strlen($append));
	if (strpos($text,' ') === false) {
		return $out.$append;
	}
	return preg_replace('/\w+$/', '', $out).$append;
}

function SingFitEditingPublishingYearSubView($year = null) {
	$model = SingFitEditingSetSongPublishingYearModel();
	if ($year === null) {
		$year = $model['ModelItemsIndex'];
	}
	$list = '';
	foreach ($model['ModelItems'] as $k => $v) {
		if ($year == $k) {
			$list .= '<option value="'.$k.'" selected="selected">'.$v.'</option>';
		} else {
			$list .= '<option value="'.$k.'">'.$v.'</option>';
		}
	}
	return $list;
}

function SingFitEditingMusicalKeySubView($key = null) {
	$model = SingFitEditingMusicalKeyModel();
	if ($key === null) {
		$key = $model['ModelItemsIndex'];
	}
	$list = '';
	foreach ($model['ModelItems'] as $k => $v) {
		$list .= '<option value="None" disabled>-- '.$k.' --</option>';
		foreach ($v as $kk => $vv) {
			if ($key == $kk) {
				$list .= '<option value="'.$kk.'" selected="selected">'.$vv.'</option>';
			} else {
				$list .= '<option value="'.$kk.'">'.$vv.'</option>';
			}
		}
	}
	return $list;
}

function SingFitEditingMusicalEraSubView($era = null) {
	$model = SingFitEditingMusicalEraModel();
	if ($era === null) {
		$era = $model['ModelItemsIndex'];
	}
	$list = '';
	foreach ($model['ModelItems'] as $k => $v) {
		if ($era == $k) {
			$list .= '<option value="'.$k.'" selected="selected">'.htmlentities($v).'</option>';
		} else {
			$list .= '<option value="'.$k.'">'.htmlentities($v).'</option>';
		}
	}
	return $list;
}

function SingFitEditingDifficultySingingSubView($difficulty = null) {
	$model = SingFitEditingDifficultySingingModel();
	if ($difficulty === null) {
		$difficulty = $model['ModelItemsIndex'];
	}
	$list = '';
	foreach ($model['ModelItems'] as $k => $v) {
		if ($difficulty == $k) {
			$list .= '<option value="'.$k.'" selected="selected">'.$v.'</option>';
		} else {
			$list .= '<option value="'.$k.'">'.$v.'</option>';
		}
	}
	return $list;
}

function SingFitEditingSetCatalogReportView($templatepath) {
	$template = @file_get_contents($templatepath);
	$page_title = "Catalog Report";
	$search = array(
		'{PAGE_TITLE}',
		'{REPORT_LIST}'
	);
	$list = '';
	$current_year = date('Y');
	$current_month = date('m');
	$last_year = ((int)$current_year) -1;
	$list .= '<tr>';
	$list .= '<td class="action">&nbsp;</td>';
	$list .= '<td style="width:200px"><small>Precalculated Text File</small></td>';
	$list .= '<td><small>Raw CSV File</small></td>';
	$list .= '</tr>';
	for ($i = 1; $i <= 12; $i++) {
		$m = ($i >= 12) ? 12 : $i;
		if ($m >= 12) {
			$m = 12;
		}
		$isodate = $last_year.'-'.sprintf("%02d", ($m)).'-01';
		$list .= '<tr>';
		$list .= '<td class="action">'.date("F", mktime(0, 0, 0, $m, 10)).'&nbsp;</td>';
		$list .= '<td style="width:200px"><a href="?an=editing.downloadreport&amp;d='.$isodate.'">'.$isodate.'</a></td>';
		$list .= '<td><a href="?an=editing.downloadreport&amp;d='.$isodate.'&amp;t=raw">'.$isodate.'</a></td>';
		$list .= '</tr>';
	}
	for ($i = 1; $i <= 12; $i++) {
		$m = ($i >= 12) ? 12 : $i;
		if ($m >= 12) {
			$m = 12;
		}
		$isodate = $current_year.'-'.sprintf("%02d", ($m)).'-01';
		$list .= '<tr>';
		$list .= '<td class="action">'.date("F", mktime(0, 0, 0, $m, 10)).'&nbsp;</td>';
		$list .= '<td style="width:200px"><a href="?an=editing.downloadreport&amp;d='.$isodate.'">'.$isodate.'</a></td>';
		$list .= '<td><a href="?an=editing.downloadreport&amp;d='.$isodate.'&amp;t=raw">'.$isodate.'</a></td>';
		$list .= '</tr>';
		if ($i >= $current_month) {
			break;
		}
	}
	$replace = array(
		$page_title,
		$list
	);
	$template = str_replace($search, $replace, $template);
	return $template;
}

function SingFitEditingSetSongView($templatepath, $idsong = 0) {
	$template = @file_get_contents($templatepath);
	if (strlen($template)) {
		$model = SingFitSongModel($idsong);
		$slug = $model['ModelItems']['slug'];
		$page_title = "New Song";
		$q_value = 9000;
		$k_value = session_id();
		$info = "";
		if ($slug != null) {
			$page_title = "Edit Song";
			$q_value = 9001;
			$zipsize = SingFitPathUtilitiesFileSize(kSingFitStoreDataRoot."/".(substr($slug, 0, 2))."/".$slug.".zip");
			$mp3size = SingFitPathUtilitiesFileSize(kSingFitStoreDataRoot."/".(substr($slug, 0, 2))."/".$slug.".mp3");
			$info = '<small>Song Package ~'.round($zipsize / 1024 / 1024).' Mb</small>';
			$info .= '<br /><small>MP3 Preview ~'.round($mp3size / 1024).' Kb</small>';
		}
		$search = array(
			'{PAGE_TITLE}',
			'{Q_VALUE}',
			'{K_VALUE}',
			'{RESOURCES_INFO}'
		);
		$replace = array(
			$page_title,
			$q_value,
			$k_value,
			$info
		);
		foreach ($model['ModelItems'] as $k => $v) {
			if ($k == 'publishing_year') {
				$search[] = "{".strtoupper($k)."_LIST}";
				$replace[] = SingFitEditingPublishingYearSubView( $v);
			} else if ($k == 'musical_key') {
				$search[] = "{".strtoupper($k)."_LIST}";
				$replace[] = SingFitEditingMusicalKeySubView( $v);
			} else if ($k == 'difficulty_singing') {
				$search[] = "{".strtoupper($k)."_LIST}";
				$replace[] = SingFitEditingDifficultySingingSubView( $v);
			} else if ($k == 'musical_era') {
				$search[] = "{".strtoupper($k)."_LIST}";
				$replace[] = SingFitEditingMusicalEraSubView($v);
			} else if ($k == 'canbeshared') {
				$search[] = "{".strtoupper($k)."_STATUS}";
				$replace[] = ($v == 1 ? 'checked="checked"' : '');	
			} else {
				$search[] = "{".strtoupper($k)."}";
				$replace[] = $v;
			}
		}
		$template = str_replace($search, $replace, $template);
	}
	return $template;
}

function SingFitEditingAllSongView($templatepath) {
	$template = @file_get_contents($templatepath);
	if (strlen($template)) {
		$model = SingFitAllSongModel();
		$page_title = "All Songs";
		$search = array(
			'{PAGE_TITLE}',
			'{SONG_LIST}'
		);
		$list = '';
		foreach ($model['ModelItems'] as $k => $v) {
			$list .= '<tr>';
			$list .= '<td colspan="4"><div class="elementline"></div></td>';			
			$list .= '</tr>';
			$list .= '<tr>';
			$list .= '<td class="title noselect"><span class="plain">'.htmlentities(SingFitEditingEllipsisText($v['title'], 35)).'</span></td>';
			$list .= '<td><small>'.SingFitEditingEllipsisText($v['author'], 25).'</small></td>';
			$list .= '<td><small>'.SingFitEditingEllipsisText($v['artist'], 25).'</small></td>';
			$list .= '<td class="action"><span>delete</span> | <a href="?an=editing.view&amp;r=editsong&amp;idsong='.$v['id'].'">edit</a></td>';
			$list .= '</tr>';
		}
		$replace = array(
			$page_title,
			$list
		);
		$template = str_replace($search, $replace, $template);
	}
	return $template;
}

function SingFitEditingGenreSubView() {
	$model = SingFitEditingCategoryModel();
	$list = '<option value="'.$model['ModelItemsIndex'].'" disabled selected="selected">Add Genre</option>';
	foreach ($model['ModelItems'] as $k => $v) {
		$list .= '<option value="'.$v['id'].'">'.htmlentities($v['name']).'</option>';
	}
	return $list;
}

function SingFitEditingFeatureSubView($feature = null) {
	$model = SingFitEditingCategoryModel(true);
	$list = '<option value="'.$model['ModelItemsIndex'].'" disabled selected="selected">Add Feature</option>';
	foreach ($model['ModelItems'] as $k => $v) {
		$list .= '<option value="'.$v['id'].'">'.htmlentities($v['name']).'</option>';
	}
	return $list;
}

function SingFitEditingAttachedGenreSubView($idproduct = 0) {
	$model = SingFitEditingAttachedCategoryModel($idproduct, false);
	$ids = array();
	$list = '';
	foreach ($model['ModelItems'] as $k => $v) {
		$ids[] = intval($v['id']);
		$list .= '<div class="noselect"><a class="noselect genredetach" href="#" id="'.$v['id'].'">detach</a> <span class="noselect plain-green">'.htmlentities($v['name']).'</span></div>';
	}
	$view = '<input type="hidden" id="attached_genres" name="attached_genres" value="['.implode(",", $ids).']" />';
	$view .= '<div class="noselect" id="genreattached">'.$list.'</div>';
	return $view;
}

function SingFitEditingAttachedFeatureSubView($idproduct = 0) {
	$model = SingFitEditingAttachedCategoryModel($idproduct, true);
	$ids = array();
	$list = '';
	foreach ($model['ModelItems'] as $k => $v) {
		$ids[] = intval($v['id']);
		$list .= '<div class="noselect"><a class="noselect featuredetach" href="#" id="'.$v['id'].'">detach</a> <span class="noselect plain-green">'.htmlentities($v['name']).'</span></div>';
	}
	$view = '<input type="hidden" id="attached_features" name="attached_features" value="['.implode(",", $ids).']" />';
	$view .= '<div class="noselect" id="featureattached">'.$list.'</div>';
	return $view;
}

function SingFitEditingAttachedSongSubView($idproduct = 0) {
	$model = SingFitEditingAttachedSongModel($idproduct);
	$ids = array();
	$list = '';
	foreach ($model['ModelItems'] as $k => $v) {
		$ids[] = intval($v['id']);
		$list .= '<div class="noselect"><a class="noselect findsongdetach" href="#" id="'.$v['id'].'">detach</a> <span class="noselect plain-green">'.htmlentities($v['title']).'</span><div class="elementspacer"></div></div>';
	}
	$view = '<input type="hidden" id="attached_songs" name="attached_songs" value="['.implode(",", $ids).']" />';
	$view .= '<div class="noselect" id="findsongattached">'.$list.'</div>';
	return $view;
}

function SingFitEditingSetProductView($templatepath, $idproduct = 0) {
	$template = @file_get_contents($templatepath);
	if (strlen($template)) {
		$model = SingFitProductModel($idproduct);
		$page_title = "New Product";
		$q_value = 9002;
		$k_value = session_id();
		if ($model['ModelItems']['slug'] != null) {
			$page_title = "Edit Product";
			$q_value = 9003;
		}
		$search = array(
			'{PAGE_TITLE}',
			'{Q_VALUE}',
			'{K_VALUE}',
			'{GENRE_LIST}',
			'{FEATURE_LIST}',
			'{ATTACHED_GENRES_VIEW}',
			'{ATTACHED_FEATURES_VIEW}',
			'{ATTACHED_SONGS_VIEW}'
		);
		
		$replace = array(
			$page_title,
			$q_value,
			$k_value,
			SingFitEditingGenreSubView(),
			SingFitEditingFeatureSubView(),
			SingFitEditingAttachedGenreSubView($idproduct),
			SingFitEditingAttachedFeatureSubView($idproduct),
			SingFitEditingAttachedSongSubView($idproduct)
		);
		foreach ($model['ModelItems'] as $k => $v) {
			if ($k == 'onlyforsubscriber' || $k == 'freeforsubscriber' || $k == 'freeforall') {
				$search[] = "{".strtoupper($k)."_STATUS}";
				$replace[] = ($v == 1 ? 'checked="checked"' : '');
			} else {
				$search[] = "{".strtoupper($k)."}";
				$replace[] = $v;
			}
		}
		$template = str_replace($search, $replace, $template);
	}
	return $template;
}

function SingFitEditingAllProductView($templatepath) {
	$template = @file_get_contents($templatepath);
	if (strlen($template)) {
		$model = SingFitAllProductModel();
		$page_title = "All Products";
		$search = array(
			'{PAGE_TITLE}',
			'{PRODUCT_LIST}'
		);
		$list = '';
		foreach ($model['ModelItems'] as $k => $v) {
			$name = $v['apple_product_name'];
			$shortidentifier = SingFitEditingProductManagerShortIdentifier($v['apple_product_id']);
			$list .= '<tr>';
			$list .= '<td colspan="4"><div class="elementline"></div></td>';			
			$list .= '</tr>';
			$list .= '<tr>';
			$list .= '<td class="title noselect"><span class="plain">'.htmlentities(SingFitEditingEllipsisText($name, 35)).'</span></td>';
			$list .= '<td><small>'.SingFitEditingEllipsisText($shortidentifier, 35).'</small></td>';
			$list .= '<td class="action"><span>delete</span> | <a href="?an=editing.view&amp;r=editproduct&amp;idproduct='.$v['id'].'">edit</a></td>';
			$list .= '<td class="action"><a class="'.($v['visible'] == 1 ? 'actiondeactivate' : 'actionactivate').'" id="'.$v['id'].'" href="#">'.($v['visible'] == 1 ? 'deactivate' : 'activate').'</a></td>';
			$list .= '</tr>';
		}
		$replace = array(
			$page_title,
			$list
		);
		$template = str_replace($search, $replace, $template);
	}
	return $template;
}

/* EOF */ ?>