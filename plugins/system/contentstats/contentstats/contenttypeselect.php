<?php

/** 
 * @version		1.0.1
 * @package		muscol
 * @copyright	2009 JoomlaMusicSolutions.com
 * @license		GPLv2
 */

defined('_JEXEC') or die();

class JElementContenttypeSelect extends JElement
{

	var	$_name = 'ContenttypeSelect';

	function fetchElement($name, $value, &$node, $control_name)
	{

		$db =& JFactory::getDBO();
		/*
		$query = 	' SELECT pl.element AS value, CONCAT(co.name, " (", pl.element, ")") AS text '.
					' FROM #__plugins AS pl '.
					' LEFT JOIN #__components AS co ON co.option = pl.element  AND co.parent = 0 '.
					' WHERE pl.folder = "contentstats" ';
		$db->setQuery( $query );
		$results = $db->loadObjectList();
		*/
		
		$module = JRequest::getString('module') ;
		$cid = JRequest::getVar('cid') ;
		$cid = $cid[0];
		if(!$cid){
			$cid = JRequest::getVar('id') ;	
			
		}
		
		if($cid){ // existing module
			$query = ' SELECT module FROM #__modules WHERE id = ' . $cid ;
			$db->setQuery($query);
			$module = $db->loadResult();
		}
		
		
		$dispatcher   =& JDispatcher::getInstance();
		$plugin_ok = JPluginHelper::importPlugin('contentstats');
		
		if($module == "mod_content_statistics") $results = $dispatcher->trigger('getRankingCompatibility');
		elseif($module == "mod_content_statistics_individual") $results = $dispatcher->trigger('getEvolutionCompatibility');
		
		//print_r($results);die;
		
		if(empty($results))	$first_option = JText::_( 'No providers found. Please install separate compatible plugins.' ) ;
		else 				$first_option = JText::_( 'Select a content provider (component)' ) ;
		
		$options = array();
		$options[] = JHTML::_('select.option',  '', '- '. $first_option .' -' );
		$options = array_merge($options, $results);
		
		return JHTML::_('select.genericlist', $options, 'urlparams['.$name.']', 'class="inputbox" size="1" ', 'value', 'text', $value);

	}
	
}