<?php

/*------------------------------------------------------------------------
# com_contentstats - Content Statistics for Joomla
# ------------------------------------------------------------------------
# author				Germinal Camps
# copyright 			Copyright (C) 2012 JoomlaContentStatistics.com. All Rights Reserved.
# @license				http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: 			http://www.JoomlaContentStatistics.com
# Technical Support:	Forum - http://www.JoomlaContentStatistics.com/forum
-------------------------------------------------------------------------*/

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

//new for Joomla 3.0
if(!defined('DS')){
define('DS',DIRECTORY_SEPARATOR);
}

if (!JFactory::getUser()->authorise('core.manage', 'com_contentstats'))
{
    return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}

require_once( JPATH_COMPONENT.DS.'controller.php' );

// Require specific controller if requested
if($controller = JRequest::getWord('controller', 'items')) {

	$path = JPATH_COMPONENT.DS.'controllers'.DS.$controller.'.php';
	if (file_exists($path)) {
		require_once $path;
	} else {
		$controller = 'Items';
	}
}



switch($controller){
	
	default:
		$items = true ;
		$prefix	= 'Stats';
		$controller	= 'Items';
		break;
}

$lang = JFactory::getLanguage();
$lang->load('com_contentstats', JPATH_SITE);


//JSubMenuHelper::addEntry(JText::_('ENTRIES'), 'index.php?option=com_contentstats', $items );
		
// Create the controller

$classname	= $prefix.'Controller'.$controller;

$controller	= new $classname( );

// Perform the Request task
$controller->execute( JRequest::getVar( 'task' ) );

// Redirect if set by the controller
$controller->redirect();