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

jimport('joomla.filesystem.file');

class TableItem extends JTable
{

	var $id = null;
	var $component = null;
	var $value = null;
	var $valuestring = null;
	var $reference_id = null;
	var $type = null;
	var $date_event = null;
	var $ip = null;
	var $user_id = null;
	
	var $location_id = null;
	var $country = null;
	var $state = null;
	var $city = null;
	
	var $hours = null;

	function TableItem(& $db) {
		parent::__construct('#__content_statistics', 'id', $db);
	}
	
	function check(){
		
		$user = JFactory::getUser();
		
		$this->ip = $_SERVER['REMOTE_ADDR'] ;
		$this->user_id = $user->id ;
		
		if($this->hours) $this->date_event = date('Y-m-d H:i:s', time() + (($this->hours) * 3600)) ;
		$this->hours = null ;
		
		return true;
	}
	  
}