<?php

/*------------------------------------------------------------------------
# mod_content_statistics_compare - Content Statistics Comparison Module
# ------------------------------------------------------------------------
# author				Germinal Camps
# copyright 			Copyright (C) 2011 JoomlaContentStatistics.com. All Rights Reserved.
# @license				http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: 			http://www.joomlacontentstatistics.com
# Technical Support:	Forum - http://www.joomlacontentstatistics.com/forum
-------------------------------------------------------------------------*/

// no direct access
defined('_JEXEC') or die('Restricted access');

// Include the syndicate functions only once
require_once (dirname(__FILE__).DS.'helper.php');

// Get the items
$result	= modContentStatisticsCompareHelper::getItems($params);

$items	= $result['items'] ;
$items2	= $result['items2'] ;
$items3 = $result['items3'] ;
$items4	= $result['items4'] ;

$layout_path = JPATH_BASE.DS.'plugins'.DS.'contentstats'.DS.$params->get('component').DS.'mod_content_statistics_compare'.DS.$params->get('layout', 'new').'php';
if (!file_exists($layout_path)) $layout_path = JModuleHelper::getLayoutPath('mod_content_statistics_compare', $params->get('layout', 'new')) ;

require($layout_path);
