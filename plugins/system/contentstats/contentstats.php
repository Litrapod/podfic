<?php

/*------------------------------------------------------------------------
# plg_contentstats - Content Statistics core plugin
# ------------------------------------------------------------------------
# author				Germinal Camps
# copyright 			Copyright (C) 2011 JoomlaContentStatistics.com. All Rights Reserved.
# @license				http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: 			http://www.joomlacontentstatistics.com
# Technical Support:	Forum - http://www.joomlacontentstatistics.com/forum
-------------------------------------------------------------------------*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.plugin.plugin' );
jimport( 'joomla.database.table' );

//new for Joomla 3.0
if(!defined('DS')){
define('DS',DIRECTORY_SEPARATOR);
}

class  plgSystemContentstats extends JPlugin
{
	
	var $supported_modules = array("mod_content_statistics", "mod_content_statistics_individual", "mod_content_statistics_compare", "mod_content_statistics_map");
	var $one_of_us = false ;

	function plgSystemContentstats(& $subject, $config)
	{
		parent::__construct($subject, $config);

	}
	
	function onAfterInitialise()
	{
		$mainframe = JFactory::getApplication();
		
		if(!$mainframe->isSite()){ // is administrator
			
			$db = JFactory::getDBO();
			
			$uri = JFactory::getURI();
			$document = JFactory::getDocument();
			
			$component = JRequest::getString('option') ;
			$module = JRequest::getString('module') ;
			$task = JRequest::getString('task') ;
			$layout = JRequest::getString('layout') ;
			$cid = JRequest::getVar('cid') ;
			$cid = $cid[0];
			if(!$cid){
				$cid = JRequest::getVar('id') ;	
				
			}
			
			if(($component == "com_modules" || $component == "com_advancedmodules") && !$module){
				// view /admin/com_modules/models/module.php line 56
				$extensionId = $mainframe->getUserState('com_modules.add.module.extension_id') ;
				if($component == "com_advancedmodules") $extensionId = $mainframe->getUserState('com_advancedmodules.add.module.extension_id') ;
				
				if($extensionId){
					$query = ' SELECT element FROM #__extensions WHERE type = "module" AND extension_id = ' . $extensionId ;
					$db->setQuery($query);
					$module = $db->loadResult();
				}
				//echo $module;die;
			}
			
			$cid = (int) $cid ;
			if($cid){ // existing module
				$query = ' SELECT module FROM #__modules WHERE id = ' . $cid ;
				$db->setQuery($query);
				$module = $db->loadResult();
			}
			
			$one_of_us = false ;	
			
			if(in_array($module, $this->supported_modules)){
				$one_of_us = true ;	
				$this->one_of_us = true ;
				$js = "var module_statistics = '".$module."';" ;
				$document->addScriptDeclaration($js);
			}
			
			
			if(($component == "com_modules" || $component == "com_advancedmodules") && $layout == "edit" && $one_of_us){
				
				JHtmlBehavior::framework();
				$document->addScript($uri->root().'plugins/system/contentstats/contentstats/select.js');
				
				if($cid){ // the module was already created, we are modifying it.
					
					$query = ' SELECT params FROM #__modules WHERE id = ' . $cid ;
					$db->setQuery($query);
					$resultparams = $db->loadResult();
					//print_r($resultparams);die;
					$moduleparams = new JRegistry($resultparams);
					$module_criteria = $moduleparams->get('criteria');
					$module_selector = $moduleparams->get('selector');
					
					$module_criteria2 = $moduleparams->get('criteria2');
					$module_selector2 = $moduleparams->get('selector2');
					
					$module_criteria3 = $moduleparams->get('criteria3');
					$module_selector3 = $moduleparams->get('selector3');
					
					$module_criteria4 = $moduleparams->get('criteria4');
					$module_selector4 = $moduleparams->get('selector4');
					
					$js = "var modulecriteria = '".$module_criteria."';\n" ;
					$js .= "var moduleselector = '".$module_selector."';\n" ;
					
					$js .= "var modulecriteria2 = '".$module_criteria2."';\n" ;
					$js .= "var moduleselector2 = '".$module_selector2."';\n" ;
					
					$js .= "var modulecriteria3 = '".$module_criteria3."';\n" ;
					$js .= "var moduleselector3 = '".$module_selector3."';\n" ;
					
					$js .= "var modulecriteria4 = '".$module_criteria4."';\n" ;
					$js .= "var moduleselector4 = '".$module_selector4."';\n" ;
					
					$document->addScriptDeclaration($js);
					
				}
				
				$dispatcher   = JDispatcher::getInstance();
				$plugin_ok = JPluginHelper::importPlugin('contentstats');
				
				$js = "";
				
				switch($module){
					case "mod_content_statistics" :
				
						// the data for the CRITERIA - Ranking module
						$results = $dispatcher->trigger('getCriteriaRanking');
						
						// the data for the SELECTOR - Ranking module
						$results2 = $dispatcher->trigger('getSelectorsRanking');
						
				
					break;
					case "mod_content_statistics_individual" : case "mod_content_statistics_compare" : case "mod_content_statistics_map" :
				
						// the data for the CRITERIA - Evolution module
						$results = $dispatcher->trigger('getCriteriaEvolution');
						
						// the data for the SELECTOR - Evolution module
						$results2 = $dispatcher->trigger('getSelectorsEvolution');
						
						
					break;
				} // end switch
				
				$js .= "var criteriaoptions = new Array();\n";
						
				foreach($results as $result){
				
					$options = array();
					$options[] = JHTML::_('select.option',  '', '- '. JText::_( 'Choose the criteria' ) .' -' );
					$options = array_merge($options, $result->options);
					
					$js .= "criteriaoptions['".$result->component."'] = new Array();\n" ;
					$i = 0;
					foreach($options as $option){
						$js .= "criteriaoptions['".$result->component."'][".$i."] = new Array();\n" ;
						$js .= "criteriaoptions['".$result->component."'][".$i."]['value'] = '".$option->value."';\n" ;
						$js .= "criteriaoptions['".$result->component."'][".$i."]['name'] = '".$option->text."';\n" ;
						$i++;
					}
				
				}
				
				// the data for the SELECTOR - Ranking module
				$results = $dispatcher->trigger('getSelectorsRanking');
				
				$js .= "var selectoroptions = new Array();\n";
				
				foreach($results2 as $result){
				
					$options = array();
					$options[] = JHTML::_('select.option',  '', '- '. JText::_( 'Choose the selector' ) .' -' );
					$options = array_merge($options, $result->options);
					
					$js .= "selectoroptions['".$result->component."'] = new Array();\n" ;
					$i = 0;
					foreach($options as $option){
						$js .= "selectoroptions['".$result->component."'][".$i."] = new Array();\n" ;
						$js .= "selectoroptions['".$result->component."'][".$i."]['value'] = '".$option->value."';\n" ;
						$js .= "selectoroptions['".$result->component."'][".$i."]['name'] = '".$option->text."';\n" ;
						$i++;
					}
					
				}
				
				$document->addScriptDeclaration($js);
				
			}
			
		}
		
		if($mainframe->isSite()){
		
			$component = JRequest::getString('option') ;
			
			if(substr($component, 0, 4) != "com_") $component = "com_" . $component ;
			
			$dispatcher   = JDispatcher::getInstance();
			$plugin_ok = JPluginHelper::importPlugin('contentstats', $component);
			//$results = $dispatcher->trigger('registerStatisticBefore');
			$results = $dispatcher->trigger('registerStatisticBefore_'.$component);
			
			foreach($results as $result){
				if($result['register']){
					if(!$result['component']) $result['component'] = $component ;
					
					// reference_id and type are mandatory vars.
					if(($result['reference_id'] || $result['value'] || $result['valuestring']) && $result['type']){
						
						//we store the data on the DataBase
						$this->saveData($result);
						
					}
					
				}
			}
			
		}
		
	}

	function onAfterRoute()
	{
		$mainframe = JFactory::getApplication();
		
		if($mainframe->isSite()){
		
			$component = JRequest::getString('option') ;
			
			if(substr($component, 0, 4) != "com_") $component = "com_" . $component ;
			
			$dispatcher   = JDispatcher::getInstance();
			$plugin_ok = JPluginHelper::importPlugin('contentstats', $component);
			//$results = $dispatcher->trigger('registerStatistic');
			$results = $dispatcher->trigger('registerStatistic_'.$component);
			
			foreach($results as $result){
				if($result['register']){
					if(!$result['component']) $result['component'] = $component ;
					
					// reference_id and type are mandatory vars.
					if(($result['reference_id'] || $result['value'] || $result['valuestring']) && $result['type']){
						
						//we store the data on the DataBase
						$this->saveData($result);
						
					}
					
				}
			}
			
		}
		
		else{ // is admin
		
			$component = JRequest::getString('option') ;
			
			if(substr($component, 0, 4) != "com_") $component = "com_" . $component ;
			
			$dispatcher   = JDispatcher::getInstance();
			$plugin_ok = JPluginHelper::importPlugin('contentstats', $component);
			//$results = $dispatcher->trigger('registerStatistic');
			$results = $dispatcher->trigger('registerStatisticAdmin_'.$component);
			
			foreach($results as $result){
				if($result['register']){
					if(!$result['component']) $result['component'] = $component ;
					
					// reference_id and type are mandatory vars.
					if(($result['reference_id'] || $result['value'] || $result['valuestring']) && $result['type']){
						
						//we store the data on the DataBase
						$this->saveData($result);
						
					}
					
				}
			}
			
		}
		
		
	}
	
	function onAfterDispatch()
	{
		$mainframe = JFactory::getApplication();
		
		if($mainframe->isSite()){
		
			$component = JRequest::getString('option') ;
			
			if(substr($component, 0, 4) != "com_") $component = "com_" . $component ;
			
			$dispatcher   = JDispatcher::getInstance();
			$plugin_ok = JPluginHelper::importPlugin('contentstats', $component);
			//$results = $dispatcher->trigger('registerStatisticAfter');
			$results = $dispatcher->trigger('registerStatisticAfter_'.$component);
			
			foreach($results as $result){
				if($result['register']){
					if(!$result['component']) $result['component'] = $component ;
					
					// reference_id and type are mandatory vars.
					if(($result['reference_id'] || $result['value'] || $result['valuestring']) && $result['type']){
						
						//we store the data on the DataBase
						$this->saveData($result);
						
					}
					
				}
			}
			
		}
		else{
			//admin
			//new for 3.2
			if($this->one_of_us){
				$document = JFactory::getDocument();
				
				$script = $document->_script["text/javascript"];
				
				$document->_script["text/javascript"] = str_replace("jQuery('select').chosen", "jQuery('select:not(.not_chosen)').chosen", $script);
				
			}
				
		}
		
	}
	
	function saveData($data){
		
		$mainframe = JFactory::getApplication();
		$db = JFactory::getDBO();
		$row = new TableContentStatistic($db);
		//new for version 1.6.0
		if($this->params->get('hours')) $row->hours = $this->params->get('hours');
		
		$blockips = $this->params->get('blockips');
		$blockips = explode(",", $blockips);
		
		$ip = $_SERVER['REMOTE_ADDR'] ;
		$user = JFactory::getUser();
		
		$blockusers = $this->params->get('blockusers');
		$blockusers = explode(",", $blockusers);
		
		if($user->id) $user_block = in_array($user->id, $blockusers) ;
		else $user_block = false ;
		
		$ip_block = in_array($ip, $blockips) ;
		$robot_block = $this->is_robot() ;
		
		$track = true ;
		
		if($this->params->get('only_registered') && !$user->id){
			$track = false ;
		}
		
		if(!$ip_block && !$robot_block && !$user_block && $track){
			
			if($this->params->get('ip_geolocation') && $this->params->get('api_key')){
				$geolocation = $this->set_cookie($ip);
				$data['country'] = $geolocation['countryCode'] ;
				$data['state'] = $geolocation['regionName'] ;
				$data['city'] = $geolocation['cityName'] ;
			}
			
			// Bind the form fields to the statistics table
			if (!$row->bind($data)) {
				$mainframe->enqueueMessage($db->getErrorMsg());
				return false;
			}
	
			if (!$row->check()) {
				$mainframe->enqueueMessage($db->getErrorMsg());
				return false;
			}
			
			if (!$row->store()) {
				$mainframe->enqueueMessage($db->getErrorMsg());
				return false;
			}
		
		}
		
		
	}
	
	function is_robot(){
		$user_agent = $_SERVER['HTTP_USER_AGENT'] ;
		
		$robot = false ;
		
		//you can add more robots to block: http://www.searchenginedictionary.com/spider-names.shtml
		
		$blockuseragents = $this->params->get('blockuseragents');
		$blockuseragents = explode(",", $blockuseragents);
		
		foreach($blockuseragents as $current_agent){
		
			if(preg_match("/".preg_quote($current_agent)."/i",$user_agent)) $robot = true ;
		
		}
		
		return $robot ;
		
	}
	
	function set_cookie($ip){
		
		require_once (dirname(__FILE__).DS.'contentstats'.DS.'ip2locationlite.class.php');
 
		//Set geolocation cookie
		if(!$_COOKIE["cs_geolocation"]){
		  $ipLite = new ip2location_lite;
		  $ipLite->setKey($this->params->get('api_key'));
		 
		  $visitorGeolocation = $ipLite->getCity($ip, $this->params->get('apimethod',1));

		  if ($visitorGeolocation['statusCode'] == 'OK') {
			//Array ( [statusCode] => OK [statusMessage] => [ipAddress] => 74.125.45.100 [countryCode] => US [countryName] => UNITED STATES [regionName] => CALIFORNIA [cityName] => MOUNTAIN VIEW [zipCode] => 94043 [latitude] => 37.3861 [longitude] => -122.084 [timeZone] => -08:00 )	
			$store = array();
			$store['statusCode'] = $visitorGeolocation['statusCode'] ;
			$store['ipAddress'] = $visitorGeolocation['ipAddress'] ;
			$store['countryCode'] = $visitorGeolocation['countryCode'] ;
			$store['regionName'] = $visitorGeolocation['regionName'] ;
			$store['cityName'] = $visitorGeolocation['cityName'] ;
			
			$data = base64_encode(serialize($store));
			
			setcookie("cs_geolocation", $data, time()+3600*24*7); //set cookie for 1 week
		  }
		}else{
		  $store = unserialize(base64_decode($_COOKIE["cs_geolocation"]));
		}
		 
		return $store;	
	}
	
}


class TableContentStatistic extends JTable
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

	function TableContentStatistic(& $db) {
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