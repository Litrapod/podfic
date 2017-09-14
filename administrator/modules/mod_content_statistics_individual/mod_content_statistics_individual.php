<?php
// no direct access
defined('_JEXEC') or die('Restricted access');

// Include the syndicate functions only once
require_once (dirname(__FILE__).DS.'helper.php');

// Get the items
$result	= modContentStatisticsIndividualHelper::getItems($params);

$items	= $result['items'] ;
//$names	= $result['names'] ;

$layout_path = JPATH_BASE.DS.'plugins'.DS.'contentstats'.DS.$params->get('component').DS.'mod_content_statistics_individual'.DS.$params->get('layout', 'default').'php';
if (!file_exists($layout_path)) $layout_path = JModuleHelper::getLayoutPath('mod_content_statistics_individual', $params->get('layout', 'default')) ;

require($layout_path);

