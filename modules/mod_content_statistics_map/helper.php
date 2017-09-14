<?php

/*------------------------------------------------------------------------
# mod_content_statistics_map - Content Statistics Map Module
# ------------------------------------------------------------------------
# author				Germinal Camps
# copyright 			Copyright (C) 2013 JoomlaContentStatistics.com. All Rights Reserved.
# @license				http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: 			http://www.joomlacontentstatistics.com
# Technical Support:	Forum - http://www.joomlacontentstatistics.com/forum
-------------------------------------------------------------------------*/

// no direct access
defined('_JEXEC') or die('Restricted access');

class modContentStatisticsMapHelper
{

	function getItems(&$params){
		
		$mainframe =& JFactory::getApplication();
		$_db	= & JFactory::getDBO();
		$user	= & JFactory::getUser();
		
		$component = $params->get( 'component' );
		$criteria = $params->get( 'criteria' );
		$selector = $params->get( 'selector' );
		$specific_id = $params->get( 'specific_id' );
		
		$time = $params->get( 'time' ) ;
		
		$days = $params->get( 'num_units_time', 7 ); 
		
		$viewer = $params->get( 'filter_user' );
		$specific_user = $params->get( 'filter_specific_user' );
		
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
		$dispatcher   =& JDispatcher::getInstance();
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
		
		$where_country = "";
		$more_where = "";
		
		//new in 1.5
		if($params->get( 'unique' ) == 2){ $more_where .= " AND st.user_id != 0 "; }
		
		if($params->get('country')){ 
			$where_country = " AND st.country = '".$params->get('country')."' ";
		}
		elseif($params->get('region') != "world"){ 
			$regions = modContentStatisticsMapHelper::getCountriesByRegion($params->get('region')) ;
			$where_country = " AND st.country IN (".$regions.") ";
		}
		
		$where = $where_country  . ' AND st.country != "" AND st.component = "'.$component.'" ' . $where . $more_where ;
		//echo $where_country ;die;
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
		
		$query = str_replace("AS howmuch", "AS howmuch, st.country, st.state, st.city", $query);
		
		switch($params->get( 'datagroup' )){
			case 'countries':
				
				$query = $query . ' GROUP BY st.country ORDER BY howmuch ' ;
				$_db->setQuery( $query );
				$return = $_db->loadObjectList();
				
			
			break;
			case 'states':
		
				$query = $query . ' GROUP BY st.state, st.country ORDER BY howmuch ' ;
				$_db->setQuery( $query );
				$return = $_db->loadObjectList();
			
			break;
			case 'cities':
		
				$query = $query . ' GROUP BY st.city, st.country ORDER BY howmuch ' ;
				$_db->setQuery( $query );
				$return = $_db->loadObjectList();
			
			break;
		}
		
		return $return;

	}
	
	function getCountriesByRegion($region){
		
		$regions = array();
		//002 - Africa
		$regions["015"] = array( 	"DZ", "EG", "EH", "LY", "MA", "SD", "TN");
		$regions["011"] = array( 	"BF", "BJ", "CI", "CV", "GH", "GM", "GN", "GW", "LR", "ML", "MR", "NE", "NG", "SH", "SL", "SN", "TG");
		$regions["017"] = array( 	"AO", "CD", "ZR", "CF", "CG", "CM", "GA", "GQ", "ST", "TD");
		$regions["014"] = array( 	"BI", "DJ", "ER", "ET", "KE", "KM", "MG", "MU", "MW", "MZ", "RE", "RW", "SC", "SO", "TZ", "UG", "YT", "ZM", "ZW");
		$regions["018"] = array( 	"BW", "LS", "NA", "SZ", "ZA");
		
		$regions["002"] = array_merge($regions["015"],$regions["011"],$regions["017"],$regions["014"],$regions["018"]) ;
		
		//150 - Europe
		$regions["154"] = array( 	"GG", "JE", "AX", "DK", "EE", "FI", "FO", "GB", "IE", "IM", "IS", "LT", "LV", "NO", "SE", "SJ");
		$regions["155"] = array( 	"AT", "BE", "CH", "DE", "DD", "FR", "FX", "LI", "LU", "MC", "NL");
		$regions["151"] = array( 	"BG", "BY", "CZ", "HU", "MD", "PL", "RO", "RU", "SU", "SK", "UA");
		$regions["039"] = array( 	"AD", "AL", "BA", "ES", "GI", "GR", "HR", "IT", "ME", "MK", "MT", "CS", "RS", "PT", "SI", "SM", "VA", "YU");
		
		$regions["150"] = array_merge($regions["154"],$regions["155"],$regions["151"],$regions["039"]) ;
		
		//019 - Americas
		$regions["021"] = array( 	"BM", "CA", "GL", "PM", "US");
		$regions["029"] = array(	"AG", "AI", "AN", "AW", "BB", "BL", "BS", "CU", "DM", "DO", "GD", "GP", "HT", "JM", "KN", "KY", "LC", "MF", "MQ", "MS", "PR", "TC", "TT", "VC", "VG", "VI");
		$regions["013"] = array( 	"BZ", "CR", "GT", "HN", "MX", "NI", "PA", "SV");
		$regions["005"] = array( 	"AR", "BO", "BR", "CL", "CO", "EC", "FK", "GF", "GY", "PE", "PY", "SR", "UY", "VE");
		
		$regions["019"] = array_merge($regions["021"],$regions["029"],$regions["013"],$regions["005"]) ;
		
		//142 - Asia
		$regions["143"] = array( 	"TM", "TJ", "KG", "KZ", "UZ");
		$regions["030"] = array( 	"CN", "HK", "JP", "KP", "KR", "MN", "MO", "TW");
		$regions["034"] = array( 	"AF", "BD", "BT", "IN", "IR", "LK", "MV", "NP", "PK");
		$regions["035"] = array( 	"BN", "ID", "KH", "LA", "MM", "BU", "MY", "PH", "SG", "TH", "TL", "TP", "VN");
		$regions["145"] = array( 	"AE", "AM", "AZ", "BH", "CY", "GE", "IL", "IQ", "JO", "KW", "LB", "OM", "PS", "QA", "SA", "NT", "SY", "TR", "YE", "YD");
		
		$regions["142"] = array_merge($regions["143"],$regions["030"],$regions["034"],$regions["035"],$regions["145"]) ;
		
		//009 - Oceania
		$regions["053"] = array( 	"AU", "NF", "NZ");
		$regions["054"] = array(	"FJ", "NC", "PG", "SB", "VU");
		$regions["057"] = array(	"FM", "GU", "KI", "MH", "MP", "NR", "PW");
		$regions["061"] = array(	"AS", "CK", "NU", "PF", "PN", "TK", "TO", "TV", "WF", "WS");
		
		$regions["009"] = array_merge($regions["053"],$regions["054"],$regions["057"],$regions["061"]) ;	
		
		$return = implode("','", $regions[$region] );
		
		return "'".$return."'";
	}

}