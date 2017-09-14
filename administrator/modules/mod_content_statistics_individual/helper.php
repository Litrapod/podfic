<?php

/*------------------------------------------------------------------------
# mod_content_statistics_individual - Content Statistics Evolution Module
# ------------------------------------------------------------------------
# author				Germinal Camps
# copyright 			Copyright (C) 2011 JoomlaContentStatistics.com. All Rights Reserved.
# @license				http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: 			http://www.joomlacontentstatistics.com
# Technical Support:	Forum - http://www.joomlacontentstatistics.com/forum
-------------------------------------------------------------------------*/

// no direct access
defined('_JEXEC') or die('Restricted access');

class modContentStatisticsIndividualHelper
{

	static function getItems(&$params){
		
		$mainframe = JFactory::getApplication();
		$_db	= JFactory::getDBO();
		$user	= JFactory::getUser();
		
		$component = $params->get( 'component' );
		$criteria = $params->get( 'criteria' );
		$selector = $params->get( 'selector' );
		$specific_id = $params->get( 'specific_id' );
		
		$time = $params->get( 'time' ) ;
		
		$days = $params->get( 'num_units_time', 7 ); 
		
		$viewer = $params->get( 'filter_user' );
		$specific_user = $params->get( 'filter_specific_user' );

		$more_where = "";
		
		if($viewer){
			if($user->id) $more_where .= ' AND st.user_id = ' . $user->id ;
			else $more_where .= ' AND 0 ' ; // return NOTHING
		}
		elseif($specific_user){
			
			$more_where .= ' AND st.user_id = ' . $specific_user ;
		}
		
		$view = JRequest::getVar('view');
		$id = JRequest::getInt('id');
		
		//we call the PLUGIN specific function to get the QUERY
		$dispatcher   = JDispatcher::getInstance();
		$plugin_ok = JPluginHelper::importPlugin('contentstats', $component);
		
		$query = '' ;
		
		switch($time){
			case 'DAYS':
				
				$where = ' AND st.date_event BETWEEN SUBDATE(CURDATE(), '.($days).') AND NOW() ' ;
				
				break;
			
			case 'MONTHS':
				
				$current = $days - 1 ;
		
				$year = date('Y') ;
				$month = date('m') ;
				$month = ($month - $current) % 12 ;
				if($month <=0) $month += 12;
				
				$years = ceil(($days) / 12) ;
				$year -= $years ;
				
				//new
				$year = date('Y') - ceil($days / 12) + 1;
				$year = date ( 'Y' , strtotime ( '-'.($days - 1).' month' , strtotime ( date ('Y-m-15') ) ) );
				
				$where = ' AND st.date_event >= "'.$year.'-'.$month.'-01 00:00:00" AND NOW() ' ;
				
				break;
			case 'YEARS':
				
				
				$year = date('Y') - $days + 1;
				
				$where = ' AND st.date_event >= "'.$year.'-01-01 00:00:00" AND NOW() ' ;
				
				break;
		}
		
		//new in 1.5
		if($params->get( 'unique' ) == 2){ $more_where .= " AND st.user_id != 0 "; }
		
		$where = ' AND st.component = "'.$component.'" ' . $where . $more_where ;
		
		//$results = $dispatcher->trigger('getQueryEvolution', array($criteria, $selector, $specific_id, $where, $params));
		$results = $dispatcher->trigger('getQueryEvolution_'.$component, array($criteria, $selector, $specific_id, $where, $params));
		
		foreach($results as $result){
			if($result->component == $component){
				$query = $result->query ;
			}
		}
		
		//new in 1.5
		switch($params->get( 'unique' )){
			case 1: // IP
				$query = str_replace("COUNT(st.id)", "COUNT(DISTINCT st.reference_id, st.ip)", $query);
			break;
			case 2: // user
				$query = str_replace("COUNT(st.id)", "COUNT(DISTINCT st.reference_id, st.user_id)", $query);
			break;
		}
		
		
		switch($time){
			case 'DAYS':
		
				$query = $query . ' GROUP BY DAY(date_event), MONTH(date_event), YEAR(date_event) ORDER BY date_event ' ;
				$_db->setQuery( $query );
				$temp_result = $_db->loadObjectList();
				
				if(!empty($temp_result)){
					foreach($temp_result as $res){
						$temp2[substr($res->date_event, 0, 10)] = $res ;
					}
				}
				
				$j = 0 ;
				for($i = $days - 1; $i >= 0 ; $i--){
					$time1 = strtotime('-'.$i.' DAY');
					$date_day = date('Y-m-d', $time1) ;
					
					$items[$j] = new stdClass();

					if(array_key_exists($date_day, $temp2)){
						$items[$j] = $temp2[$date_day] ;
						$items[$j]->date_event = substr($items[$j]->date_event, 0, 10) ;
					}
					else{
						
						 $items[$j]->howmuch = 0 ;
						 $items[$j]->date_event = $date_day;
					}
					$j++;
				}
			
			break;
			case 'MONTHS':
		
				$query = $query . ' GROUP BY MONTH(date_event), YEAR(date_event) ORDER BY date_event ' ;
				$_db->setQuery( $query );
				$temp_result = $_db->loadObjectList();
				
				if(!empty($temp_result)){
					foreach($temp_result as $res){
						$temp2[substr($res->date_event, 0, 7).'-01'] = $res ;
					}
				}
				
				$j = 0 ;
				for($i = $days - 1; $i >= 0 ; $i--){
					if($month < 10){
						$disp_month = '0' . (int)$month ;
					}
					else $disp_month = $month ;
					
					$date_day = $year . '-' . $disp_month . '-01' ;
					
					$items[$j] = new stdClass();

					if(array_key_exists($date_day, $temp2)){
						$items[$j] = $temp2[$date_day] ;
					}
					else{
						 $items[$j]->howmuch = 0 ;
						 $items[$j]->date_event = $date_day;
					}
					$j++;
					
					$month++;
					if($month == 13){
						$month = 1 ;
						$year++;
					}
				}
			
			break;
			case 'YEARS':
		
				$query = $query . ' GROUP BY YEAR(date_event) ORDER BY date_event ' ;
				$_db->setQuery( $query );
				$temp_result = $_db->loadObjectList();
				
				foreach($temp_result as $res){
					$temp2[substr($res->date_event, 0, 4).'-01-01'] = $res ;
				}
				
				$j = 0 ;
				for($i = $days - 1; $i >= 0 ; $i--){
					
					$date_day = $year . '-01-01' ;
					
					$items[$j] = new stdClass();

					if(array_key_exists($date_day, $temp2)){
						$items[$j] = $temp2[$date_day] ;
					}
					else{
						 $items[$j]->howmuch = 0 ;
						 $items[$j]->date_event = $date_day;
					}
					$j++;
					
					$year++;
					
				}
			
			break;
		}
		
		$return['items'] = $items ;
		//$return['names'] = $names ;
		
		return $return;

	}

}