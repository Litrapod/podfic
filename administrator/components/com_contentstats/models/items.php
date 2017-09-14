<?php

/*------------------------------------------------------------------------
# com_contentstats - Content Statistics for Joomla
# ------------------------------------------------------------------------
# author				Germinal Camps
# copyright 			Copyright (C) 2013 JoomlaContentStatistics.com. All Rights Reserved.
# @license				http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: 			http://www.JoomlaContentStatistics.com
# Technical Support:	Forum - http://www.JoomlaContentStatistics.com/forum
-------------------------------------------------------------------------*/

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.model' );


class StatsModelItems extends JModelLegacy
{

	var $_data;
  	var $_total = null;
  	var $_pagination = null;
  	var $_keywords = null;
	var $components = null;
	var $component_id = null;
	var $type_id = null;
	var $item_id = null;
	var $date_in = null;
	var $date_out = null;

	function __construct(){
		parent::__construct();
	
		$mainframe = JFactory::getApplication();

		// Get pagination request variables
		$limit = $mainframe->getUserStateFromRequest('contentstats.entries.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart = $mainframe->getUserStateFromRequest('contentstats.entries.limitstart', 'limitstart', 0, 'int');
		$keywords = $mainframe->getUserStateFromRequest('contentstats.entries.keywords','keywords','','keywords');
		$component_id = $mainframe->getUserStateFromRequest('contentstats.entries.component_id','component_id',0,'component_id');
		$type_id = $mainframe->getUserStateFromRequest('contentstats.entries.type_id','type_id','','type_id');
		$item_id = $mainframe->getUserStateFromRequest('contentstats.entries.item_id','item_id','','item_id');
		$user_id = $mainframe->getUserStateFromRequest('contentstats.entries.user_id','user_id','','user_id');
		$date_in = $mainframe->getUserStateFromRequest('contentstats.entries.date_in','date_in','','date_in');
		$date_out = $mainframe->getUserStateFromRequest('contentstats.entries.date_out','date_out','','date_out');
		$country_id = $mainframe->getUserStateFromRequest('contentstats.entries.country_id','country_id',0,'country_id');
		
		$filter_order     = $mainframe->getUserStateFromRequest('contentstats.entries.filter_order', 'filter_order', 'st.date_event', 'cmd' );
        $filter_order_Dir = $mainframe->getUserStateFromRequest('contentstats.entries.filter_order_Dir', 'filter_order_Dir', 'DESC', 'word' );
		
		$this->setState('filter_order', $filter_order);
        $this->setState('filter_order_Dir', $filter_order_Dir);
		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
		
		$this->setState('keywords', $keywords);
		$this->setState('component_id', $component_id);
		$this->setState('type_id', $type_id);
		$this->setState('item_id', $item_id);
		$this->setState('user_id', $user_id);
		$this->setState('date_in', $date_in);
		$this->setState('date_out', $date_out);
		$this->setState('country_id', $country_id);
		
  	}
	
function getTotal()
  {
 	// Load the content if it doesn't already exist
 	if (empty($this->_total)) {
 	    $query = $this->_buildQuery();
 	    $this->_total = $this->_getListCount($query);	
 	}
 	return $this->_total;
  }
  
 function getPagination()
  {
 	// Load the content if it doesn't already exist
 	if (empty($this->_pagination)) {
 	    jimport('joomla.html.pagination');
 	    $this->_pagination = new JPagination($this->getTotal(), $this->getState('limitstart'), $this->getState('limit') );
 	}
 	return $this->_pagination;
  }


	function getKeywords(){
		if (empty($this->_keywords)) {
			$this->_keywords = $this->getState('keywords')	;
		}
		return $this->_keywords;
	}
	function getComponentId(){
		if (empty($this->component_id)) {
			$this->component_id = $this->getState('component_id')	;
		}
		return $this->component_id;
	}
	function getTypeId(){
		if (empty($this->type_id)) {
			$this->type_id = $this->getState('type_id')	;
		}
		return $this->type_id;
	}
	function getItemId(){
		if (empty($this->item_id)) {
			$this->item_id = $this->getState('item_id')	;
		}
		return $this->item_id;
	}
	function getUserId(){
		if (empty($this->user_id)) {
			$this->user_id = $this->getState('user_id')	;
		}
		return $this->user_id;
	}
	function getDateIn(){
		if (empty($this->date_in)) {
			$this->date_in = $this->getState('date_in')	;
		}
		return $this->date_in;
	}
	function getDateOut(){
		if (empty($this->date_out)) {
			$this->date_out = $this->getState('date_out')	;
		}
		return $this->date_out;
	}
	function getCountryId(){
		if (empty($this->country_id)) {
			$this->country_id = $this->getState('country_id')	;
		}
		return $this->country_id;
	}
 
	
	function getFilterOrder(){
		return  $this->getState('filter_order') ;
  }
  function getFilterOrderDir(){
		return  $this->getState('filter_order_Dir') ;
  }
  
  function _buildContentOrderBy()
	{
			
			$filter_order     = $this->getState('filter_order' ) ;
			$filter_order_Dir = $this->getState('filter_order_Dir') ;
			
			$orderby = ' ORDER BY '.$filter_order.' '.$filter_order_Dir . ' ';
	 
			return $orderby;
	}
	 
	function _buildQuery()
	{
		
		$keywords = $this->getKeywords();
		$component_id = $this->getComponentId();
		$type_id = $this->getTypeId();
		$item_id = $this->getItemId();
		$user_id = $this->getUserId();
		$date_in = $this->getDateIn();
		$date_out = $this->getDateOut();
		$country_id = $this->getCountryId();
		
		$where_clause = array();

		if ($keywords != "")
			$where_clause[] = ' ( u.name LIKE "%'.$keywords.'%" OR st.component LIKE "%'.$keywords.'%" OR st.ip LIKE "%'.$keywords.'%" ) ';
		if ($component_id) {
			$where_clause[] = ' st.component = "'. $component_id . '" ';
		}
		if ($type_id) {
			$where_clause[] = ' st.type = '. (int)$type_id . ' ';
		}
		if ($item_id) {
			$where_clause[] = ' st.reference_id = '. (int)$item_id . ' ';
		}
		if ($user_id) {
			$where_clause[] = ' st.user_id = '. (int)$user_id . ' ';
		}
		if ($date_in) {
			$where_clause[] = ' st.date_event >= "'. $date_in . '" ';
		}
		if ($date_out) {
			$where_clause[] = ' st.date_event <= "'. $date_out . '" ';
		}
		if ($country_id) {
			$where_clause[] = ' st.country = "'. $country_id . '" ';
		}
		
		$orderby = $this->_buildContentOrderBy();
		
		// Build the where clause of the content record query
		$where_clause = (count($where_clause) ? ' WHERE '.implode(' AND ', $where_clause) : '');

		$query = ' SELECT st.*, u.name as username '
				. ' FROM #__content_statistics as st '
				. ' LEFT JOIN #__users as u ON u.id = st.user_id ' 
				. $where_clause
				. $orderby
		;
		
		return $query;
	}
	
	function getTypes(){
		if (empty( $this->_types )){

			$params = JComponentHelper::getParams( 'com_contentstats' );
			$format = $params->get('viewformat');
			if($format == "stream") $stream = true ;
			else $stream = false ;
			
			$dispatcher   = JDispatcher::getInstance();
			$plugin_ok = JPluginHelper::importPlugin('contentstats');
			$results = $dispatcher->trigger('getTypes', array($stream));
			
			$this->_types = array();
			
			foreach($results as $result){
				$this->_types[$result->component] = $result->options;	
			}
			
		}
		
		return $this->_types;
	
	}
	
	function getData(){

		if (empty( $this->_data )){
			$query = $this->_buildQuery();

			if(JRequest::getVar('task') == "export") $this->_data = $this->_getList($query);
			else $this->_data = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit'));
			
		}

 	return $this->_data;

	}
	function getComponents()
	{
		if (empty( $this->components )){
			$query = 	' SELECT DISTINCT component '
						. ' FROM #__content_statistics '
						.' ORDER BY component ' 
						;
			$this->_db->setQuery( $query );
			$this->components = $this->_db->loadAssocList('component');
		}
		
 	return $this->components;

	}
	
	function getCountries()
	{
		if (empty( $this->countries )){
			$query = 	' SELECT DISTINCT country '
						. ' FROM #__content_statistics '
						.' ORDER BY country ' 
						;
			$this->_db->setQuery( $query );
			$this->countries = $this->_db->loadAssocList('country');
		}
		
 	return $this->countries;

	}

	function delete()
	{
		
		$keywords = $this->getKeywords();
		$component_id = $this->getComponentId();
		$type_id = $this->getTypeId();
		$item_id = $this->getItemId();
		$user_id = $this->getUserId();
		$date_in = $this->getDateIn();
		$date_out = $this->getDateOut();
		$country_id = $this->getCountryId();
		
		$where_clause = array();

		if ($keywords != "")
			$where_clause[] = ' (  #__content_statistics.component LIKE "%'.$keywords.'%" OR #__content_statistics.ip LIKE "%'.$keywords.'%" ) ';
		if ($component_id) {
			$where_clause[] = ' #__content_statistics.component = "'. $component_id . '" ';
		}
		if ($type_id) {
			$where_clause[] = ' #__content_statistics.type = '. (int)$type_id . ' ';
		}
		if ($item_id) {
			$where_clause[] = ' #__content_statistics.reference_id = '. (int)$item_id . ' ';
		}
		if ($user_id) {
			$where_clause[] = ' #__content_statistics.user_id = '. (int)$user_id . ' ';
		}
		if ($date_in) {
			$where_clause[] = ' #__content_statistics.date_event >= "'. $date_in . '" ';
		}
		if ($date_out) {
			$where_clause[] = ' #__content_statistics.date_event <= "'. $date_out . '" ';
		}
		if ($country_id) {
			$where_clause[] = ' #__content_statistics.country = "'. $country_id . '" ';
		}
		
		// Build the where clause of the content record query
		$where_clause = (count($where_clause) ? ' WHERE '.implode(' AND ', $where_clause) : '');

		$query = ' DELETE '
				. ' FROM #__content_statistics '
				. $where_clause
				
		;
		
		$this->_db->setQuery($query);
		$this->_db->query();

		$query = ' OPTIMIZE TABLE #__content_statistics ';
		$this->_db->setQuery($query);
		$this->_db->query();
		
		return true ;
	}

	function delete_items()
	{
		$cids = JRequest::getVar( 'cid', array(0), 'post', 'array' );

		$row = $this->getTable('item');
		
		if (count( $cids )) {
			foreach($cids as $cid) {
				
				if (!$row->delete( $cid )) {
					$this->setError( $row->getErrorMsg() );
					return false;
				}
			}
		}
		return true;
	}
}