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


$document = JFactory::getDocument();
$document->addStyleSheet(JURI::base(true).'/modules/mod_content_statistics_individual/tmpl/statistics.css');

$document->addScript('https://www.google.com/jsapi');

$rand = rand();

$js = "";

$backgroundcolor = $params->get('backgroundcolor') ;
if(substr($backgroundcolor,0,1) != "#") $backgroundcolor = "#".$backgroundcolor ;

$chartareacolor = $params->get('chartareacolor') ;
if(substr($chartareacolor,0,1) != "#") $chartareacolor = "#".$chartareacolor ;

if($backgroundcolor == "#000000") $backgroundcolor = "";
if($chartareacolor == "#000000") $chartareacolor = "";

if($backgroundcolor){
	$backcolors[] = 'bg,s,'.$backgroundcolor ; //chart background color
}
if($chartareacolor){
	$backcolors[] = 'c,s,'.$chartareacolor ; //chart area color
}

$backgroundcolor = "'".$backgroundcolor."'";
$chartareacolor = "'".$chartareacolor."'";

$days = $params->get( 'num_units_time', 7 ); 
$current = $days ;
$max = 0; 

$num_labels = $params->get( 'num_labels', 1 ); 
$i = 0 ;


$date_format = $params->get('other_label_format') ? $params->get('other_label_format') : $params->get('label_format', '%a') ;

$average = 0;

$js .= "google.load('visualization', '1', {packages:['corechart']});
      google.setOnLoadCallback(drawChart".$rand.");
      function drawChart".$rand."() {
		  var data".$rand." = new google.visualization.DataTable();
		  data".$rand.".addColumn('string', 'Time');
		  data".$rand.".addColumn('number', '".$params->get('dataname')."');
		  data".$rand.".addRows(".count($items).");";

$time = $params->get('time');

foreach($items as $item){
	
	if($item->howmuch > $max) $max = $item->howmuch ;
	
	$label = JHTML::_('date', $item->date_event, JText::_($date_format)) ;
	
	if(!$item->howmuch) $item->howmuch = 0 ;
	
	$average += $item->howmuch ;
	
	$js .= "data".$rand.".setValue(".$i.", 1, ".$item->howmuch.");\n" ;
	$js .= "data".$rand.".setValue(".$i.", 0, \"".$label."\");\n" ;
	
	$i++ ;
}

$average = $average / count($items) ;

if($params->get( 'average')){
	//$values['chd'] .= '|' . $average;	
}


if($params->get('markers')){
	 $markersize = $params->get('markersize')  ;
}
else{
	$markersize =  0 ;
}

if(!$params->get('backgroundcolor') || $params->get('backgroundcolor') == "#000000"){
	$backgroundcolor =  "{fill:'transparent'}" ;
}
else{
	$backgroundcolor = "'".$backgroundcolor."'";
}

$comment_colors = "";
$curveType = "";
$steps = "";
$stacked = "";

switch($params->get('chart','lc')){
	case 'bvg':
		$chartype = "ColumnChart" ;
	
	break;
	case 'smooth':
		$chartype = "LineChart" ;
		//if($params->get('fillcolor')) $chartype = "AreaChart" ;
		$curveType = "curveType: 'function'," ;
	break;
	case 'area':
		$chartype = "AreaChart" ;
	break;
	case 'stepped':
		$chartype = "SteppedAreaChart" ;
	break;
	case 'steppedunconnected':
		$chartype = "SteppedAreaChart" ;
		$steps = "connectSteps: false," ;
	break;
	case 'lc':default:
		$chartype = "LineChart" ;
		if($params->get('fillcolor')) $chartype = "AreaChart" ;
	break;
}

if($params->get('stacked')){
	$stacked = "isStacked: true," ;
}

$responsive = $params->get('responsive') ;

$js .= "var width".$rand." = document.getElementById('chart_div".$rand."').offsetWidth;" ;
$js .= "var height".$rand." = document.getElementById('chart_div".$rand."').offsetHeight;" ;

if($responsive) $thewidth = "width".$rand ;
else $thewidth = $params->get('width') ;

$linecolor = $params->get('linecolor') ;
if(substr($linecolor,0,1) != "#") $linecolor = "#".$linecolor ;

$labelcolor = $params->get('labelcolor','#999999') ;
if(substr($labelcolor,0,1) != "#") $labelcolor = "#".$labelcolor ;

$js .= "var chart".$rand." = new google.visualization.".$chartype."(document.getElementById('chart_div".$rand."'));
chart".$rand.".draw(data".$rand.", {
	width: ".$thewidth.", 
	height: ".$params->get('height').",
	colors: ['".$linecolor."'],
	lineWidth: ".$params->get( 'linewidth', '2').",
	".$curveType."
	".$stacked."
	".$steps."
	chartArea: {left: 30, top: 10, width: (width".$rand." - 50), height: ".($params->get('height')-40)."},
	legend: 'none',
	fontSize: 11,
	hAxis: {showTextEvery: ".$params->get('num_labels').",
			textStyle: {color: '".$labelcolor."'}},
	vAxis: {textStyle: {color: '".$labelcolor."'}},
	pointSize: ".$markersize.",
	backgroundColor: ".$backgroundcolor."
	});
}" ;

//$js .= "window.onresize = drawChart".$rand.";";
if($responsive) $js .= "jQuery( window ).resize(function() { drawChart".$rand."(); })";

$document->addScriptDeclaration($js);

echo $params->get('introtext');

if($params->get('debug')) echo "<pre>".$js."</pre>" ;
echo "<div id='chart_div".$rand."'></div>" ;
echo $params->get('introtext2');		  
		  
?>