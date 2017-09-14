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

$document =& JFactory::getDocument();
$document->addScript('https://www.google.com/jsapi');

$rand = rand();

$js = "";

switch($params->get('datagroup')){
	case "countries":
	$display_name = "Country" ;
	$display_mode = "regions" ;
	break;
	case "states":
	$display_name = "State" ;
	$display_mode = "regions" ;
	break;
	case "cities":
	$display_name = "City" ;
	$display_mode = "markers" ;
	break;
}

$js .= "google.load('visualization', '1', {packages:['geochart']});

      google.setOnLoadCallback(drawChart".$rand.");
	  
	  var data".$rand." = null ;
	  
      function drawChart".$rand."() {
		  data".$rand." = google.visualization.arrayToDataTable([
		  [\"".$display_name."\", '".$params->get('dataname')."'],\n";


$thedata = array();

foreach($items as $item){
	
	$label = $item->country ;
	
	switch($params->get('datagroup')){
		case "countries":
		$label = $countries[$item->country] ;
		break;
		case "states":
		$label = $item->state ;
		break;
		case "cities":
		$label = $item->city ;
		break;
	}

	$label = ucwords(strtolower($label)) ;

	if(!$item->howmuch) $item->howmuch = 0 ;
	$thedata[] =  "[\"".$label."\", ".$item->howmuch."]" ;
	
}

$js .= implode(",\n", $thedata)."]);\n\n" ;

if($params->get('backgroundcolor')){
	 $backgroundcolor = "'#" . $params->get('backgroundcolor') ."'" ;
}
else{
	$backgroundcolor =  "{fill:'transparent'}" ;
}

if($params->get('color1')){
$colorAxis = "colorAxis: {colors: ['#".$params->get('color1')."', '#".$params->get('color2')."']}," ;
}
else $colorAxis = "";

$resolution = "";

if($params->get('country')){ // country is set
	$resolution = "resolution: 'provinces'," ;
}

switch($params->get('country')){
	case "US":
		$resolution = "resolution: '".$params->get('resolution')."'," ;
	break;
}

$chartype = "GeoChart" ;

$responsive = $params->get('responsive') ;

$js .= "var width".$rand." = document.getElementById('chart_div".$rand."').offsetWidth;" ;
$js .= "var height".$rand." = document.getElementById('chart_div".$rand."').offsetHeight;" ;

if($responsive) $thewidth = "width".$rand ;
else $thewidth = $params->get('width') ;

if($params->get('country')) $filter_region = $params->get('country') ;
else $filter_region = $params->get('region') ;
	
$js .= "var options".$rand." = {
				width: ".$thewidth.",
				height: ".$params->get('height').",
				region: '".$filter_region."',
				displayMode: '".$display_mode."',
				".$resolution."
				".$colorAxis."
				backgroundColor: ".$backgroundcolor."
			};";

$js .= "var chart".$rand." = new google.visualization.".$chartype."(document.getElementById('chart_div".$rand."'));\n
			chart".$rand.".draw(data".$rand.", options".$rand.");
			}" ;
			
if($responsive) $js .= "jQuery( window ).resize(function() { drawChart".$rand."(); })";	

$document->addScriptDeclaration($js);

echo $params->get('introtext');

if($params->get('debug')) echo "<pre>".$js."</pre>" ;
echo "<div id='chart_div".$rand."'></div>" ;
echo $params->get('introtext2');	

?>