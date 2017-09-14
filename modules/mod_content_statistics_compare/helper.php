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

class modContentStatisticsCompareHelper
{

	function getItems(&$params){
		
		$mainframe = JFactory::getApplication();
		$_db	= JFactory::getDBO();
		$user	= JFactory::getUser();
		
		$component = $params->get( 'component' );
		$criteria = $params->get( 'criteria' );
		$selector = $params->get( 'selector' );
		$specific_id = $params->get( 'specific_id' );
		
		$component2 = $params->get( 'component2' );
		$criteria2 = $params->get( 'criteria2' );
		$selector2 = $params->get( 'selector2' );
		$specific_id2 = $params->get( 'specific_id2' );
		
		$component3 = $params->get( 'component3' );
		$criteria3 = $params->get( 'criteria3' );
		$selector3 = $params->get( 'selector3' );
		$specific_id3 = $params->get( 'specific_id3' );
		
		$component4 = $params->get( 'component4' );
		$criteria4 = $params->get( 'criteria4' );
		$selector4 = $params->get( 'selector4' );
		$specific_id4 = $params->get( 'specific_id4' );
		
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
		if($component2) $plugin_ok = JPluginHelper::importPlugin('contentstats', $component2);
		if($component3) $plugin_ok = JPluginHelper::importPlugin('contentstats', $component3);
		if($component4) $plugin_ok = JPluginHelper::importPlugin('contentstats', $component4);
		
		$query = '' ;
		
		$current = $days - 1 ;

		$first_month = "";
		$first_year = "";
		
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
					
					$first_month = $month ;
					$first_year = $year ;
					
					$where = ' AND st.date_event >= "'.$year.'-'.$month.'-01 00:00:00" AND NOW() ' ;
					
					break;
				case 'YEARS':
				
					$year = date('Y') - $days + 1;
					$first_year = $year ;
					
					$where = ' AND st.date_event >= "'.$year.'-01-01 00:00:00" AND NOW() ' ;
					
					break;
			}
			
			$where2 = ' AND st.component = "'.$component2.'" ' . $where . $more_where ;
			$where3 = ' AND st.component = "'.$component3.'" ' . $where . $more_where ;
			$where4 = ' AND st.component = "'.$component4.'" ' . $where . $more_where ;
			$where = ' AND st.component = "'.$component.'" ' . $where . $more_where ;
			
			//new in 1.5
			if($params->get( 'unique' ) == 2){ $where .= " AND st.user_id != 0 "; }
			if($params->get( 'unique2' ) == 2){ $where2 .= " AND st.user_id != 0 "; }
			if($params->get( 'unique3' ) == 2){ $where3 .= " AND st.user_id != 0 "; }
			if($params->get( 'unique4' ) == 2){ $where4 .= " AND st.user_id != 0 "; }
			
			//first component
			$results = $dispatcher->trigger('getQueryEvolution_'.$component, array($criteria, $selector, $specific_id, $where, $params));
			
			foreach($results as $result){
				if($result->component == $component){
					$query = $result->query ;
				}
			}
			$temp2 = array();
			$items = array();
			
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
					
					foreach($temp_result as $res){
						$temp2[substr($res->date_event, 0, 10)] = $res ;
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
					
					foreach($temp_result as $res){
						$temp2[substr($res->date_event, 0, 7).'-01'] = $res ;
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
			
			if($component2){
			//second component
			$params->set('extra_filters', $params->get('extra_filters2'));
			$results2 = $dispatcher->trigger('getQueryEvolution_'.$component2, array($criteria2, $selector2, $specific_id2, $where2, $params));
			
			foreach($results2 as $result){
				if($result->component == $component2){
					$query = $result->query ;
				}
			}
			
			$month = $first_month ;
			$year = $first_year ;
			
			$temp2 = array();
			$items2 = array();
			
			//new in 1.5
			switch($params->get( 'unique2' )){
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
					
					foreach($temp_result as $res){
						$temp2[substr($res->date_event, 0, 10)] = $res ;
					}
					
					$j = 0 ;
					for($i = $days - 1; $i >= 0 ; $i--){
						$time1 = strtotime('-'.$i.' DAY');
						$date_day = date('Y-m-d', $time1) ;
						
						$items2[$j] = new stdClass();

						if(array_key_exists($date_day, $temp2)){
							$items2[$j] = $temp2[$date_day] ;
						}
						else{
							
							 $items2[$j]->howmuch = 0 ;
							 $items2[$j]->date_event = $date_day;
						}
						$j++;
					}
				
				break;
				case 'MONTHS':
			
					$query = $query . ' GROUP BY MONTH(date_event), YEAR(date_event) ORDER BY date_event ' ;
					$_db->setQuery( $query );
					$temp_result = $_db->loadObjectList();
					
					foreach($temp_result as $res){
						$temp2[substr($res->date_event, 0, 7).'-01'] = $res ;
					}
					
					$j = 0 ;
					for($i = $days - 1; $i >= 0 ; $i--){
						if($month < 10){
							$disp_month = '0' . (int)$month ;
						}
						else $disp_month = $month ;
						
						$date_day = $year . '-' . $disp_month . '-01' ;
						
						$items2[$j] = new stdClass();

						if(array_key_exists($date_day, $temp2)){
							$items2[$j] = $temp2[$date_day] ;
						}
						else{
							 $items2[$j]->howmuch = 0 ;
							 $items2[$j]->date_event = $date_day;
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
						
						$items2[$j] = new stdClass();

						if(array_key_exists($date_day, $temp2)){
							$items2[$j] = $temp2[$date_day] ;
						}
						else{
							 $items2[$j]->howmuch = 0 ;
							 $items2[$j]->date_event = $date_day;
						}
						$j++;
						
						$year++;
						
					}
				
				break;
			}
			}
			
			
			if($component3){
			//third component
			$params->set('extra_filters', $params->get('extra_filters3'));
			$results3 = $dispatcher->trigger('getQueryEvolution_'.$component3, array($criteria3, $selector3, $specific_id3, $where3, $params));
			
			foreach($results3 as $result){
				if($result->component == $component3){
					$query = $result->query ;
				}
			}
			
			$month = $first_month ;
			$year = $first_year ;
			
			$temp2 = array();
			$items3 = array();
			
			//new in 1.5
			switch($params->get( 'unique3' )){
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
					
					foreach($temp_result as $res){
						$temp2[substr($res->date_event, 0, 10)] = $res ;
					}
					
					$j = 0 ;
					for($i = $days - 1; $i >= 0 ; $i--){
						$time1 = strtotime('-'.$i.' DAY');
						$date_day = date('Y-m-d', $time1) ;
						
						$items3[$j] = new stdClass();

						if(array_key_exists($date_day, $temp2)){
							$items3[$j] = $temp2[$date_day] ;
						}
						else{
							
							 $items3[$j]->howmuch = 0 ;
							 $items3[$j]->date_event = $date_day;
						}
						$j++;
					}
				
				break;
				case 'MONTHS':
			
					$query = $query . ' GROUP BY MONTH(date_event), YEAR(date_event) ORDER BY date_event ' ;
					$_db->setQuery( $query );
					$temp_result = $_db->loadObjectList();
					
					foreach($temp_result as $res){
						$temp2[substr($res->date_event, 0, 7).'-01'] = $res ;
					}
					
					$j = 0 ;
					for($i = $days - 1; $i >= 0 ; $i--){
						if($month < 10){
							$disp_month = '0' . (int)$month ;
						}
						else $disp_month = $month ;
						
						$date_day = $year . '-' . $disp_month . '-01' ;
						
						$items3[$j] = new stdClass();

						if(array_key_exists($date_day, $temp2)){
							$items3[$j] = $temp2[$date_day] ;
						}
						else{
							 $items3[$j]->howmuch = 0 ;
							 $items3[$j]->date_event = $date_day;
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
						
						$items3[$j] = new stdClass();

						if(array_key_exists($date_day, $temp2)){
							$items3[$j] = $temp2[$date_day] ;
						}
						else{
							 $items3[$j]->howmuch = 0 ;
							 $items3[$j]->date_event = $date_day;
						}
						$j++;
						
						$year++;
						
					}
				
				break;
			}
			
			}
			
			
			if($component4){
			//fourth component
			$params->set('extra_filters', $params->get('extra_filters4'));
			$results4 = $dispatcher->trigger('getQueryEvolution_'.$component4, array($criteria4, $selector4, $specific_id4, $where4, $params));
			
			foreach($results4 as $result){
				if($result->component == $component4){
					$query = $result->query ;
				}
			}
			
			$month = $first_month ;
			$year = $first_year ;
			
			$temp2 = array();
			$items4 = array();
			
			//new in 1.5
			switch($params->get( 'unique4' )){
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
					
					foreach($temp_result as $res){
						$temp2[substr($res->date_event, 0, 10)] = $res ;
					}
					
					$j = 0 ;
					for($i = $days - 1; $i >= 0 ; $i--){
						$time1 = strtotime('-'.$i.' DAY');
						$date_day = date('Y-m-d', $time1) ;
						
						$items4[$j] = new stdClass();

						if(array_key_exists($date_day, $temp2)){
							$items4[$j] = $temp2[$date_day] ;
						}
						else{
							
							 $items4[$j]->howmuch = 0 ;
							 $items4[$j]->date_event = $date_day;
						}
						$j++;
					}
				
				break;
				case 'MONTHS':
			
					$query = $query . ' GROUP BY MONTH(date_event), YEAR(date_event) ORDER BY date_event ' ;
					$_db->setQuery( $query );
					$temp_result = $_db->loadObjectList();
					
					foreach($temp_result as $res){
						$temp2[substr($res->date_event, 0, 7).'-01'] = $res ;
					}
					
					$j = 0 ;
					for($i = $days - 1; $i >= 0 ; $i--){
						if($month < 10){
							$disp_month = '0' . (int)$month ;
						}
						else $disp_month = $month ;
						
						$date_day = $year . '-' . $disp_month . '-01' ;
						
						$items4[$j] = new stdClass();

						if(array_key_exists($date_day, $temp2)){
							$items4[$j] = $temp2[$date_day] ;
						}
						else{
							 $items4[$j]->howmuch = 0 ;
							 $items4[$j]->date_event = $date_day;
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
						
						$items4[$j] = new stdClass();

						if(array_key_exists($date_day, $temp2)){
							$items4[$j] = $temp2[$date_day] ;
						}
						else{
							 $items4[$j]->howmuch = 0 ;
							 $items4[$j]->date_event = $date_day;
						}
						$j++;
						
						$year++;
						
					}
				
				break;
			}
			
			}
			
			//print_r($items);print_r($items2);print_r($items3);print_r($items4);die;
			//print_r(str_replace("#_","jos",$query));echo "\n";
			
			$current-- ;
		
		if(!isset($items2)) $items2 = array();
		if(!isset($items3)) $items3 = array();
		if(!isset($items4)) $items4 = array();
		
		$return['items'] = $items ;
		//$return['names'] = $names ;
		
		$return['items2'] = $items2 ;
		//$return['names2'] = $names2 ;
		
		$return['items3'] = $items3 ;
		//$return['names3'] = $names3 ;
		
		$return['items4'] = $items4 ;
		//$return['names4'] = $names4 ;
		
		return $return;

	}

}