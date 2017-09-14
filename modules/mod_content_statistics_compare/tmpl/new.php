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
$document->addStyleSheet(JURI::base(true).'/modules/mod_content_statistics_compare/tmpl/statistics.css');

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
		  data".$rand.".addColumn('number', '".$params->get('dataname2')."');";
if($params->get('component3')) $js .= "data".$rand.".addColumn('number', '".$params->get('dataname3')."');";
if($params->get('component4')) $js .= "data".$rand.".addColumn('number', '".$params->get('dataname4')."');";

$js .= "data".$rand.".addRows(".count($items).");";


foreach($items as $item){
	
	$item2 =& $items2[$i] ;
	$item3 =& $items3[$i] ;
	$item4 =& $items4[$i] ;
	
	if(!is_object($item2)) {$item2 = new stdClass(); $item2->howmuch = 0 ;}
	if(!is_object($item3)) {$item3 = new stdClass(); $item3->howmuch = 0 ;}
	if(!is_object($item4)) {$item4 = new stdClass(); $item4->howmuch = 0 ;}

	if($item->howmuch > $max) $max = $item->howmuch ;
	if($item2->howmuch > $max) $max = $item2->howmuch ;
	if($item3->howmuch > $max) $max = $item3->howmuch ;
	if($item4->howmuch > $max) $max = $item4->howmuch ;
	
	$label = JHTML::_('date', $item->date_event, JText::_($date_format)) ;
	
	if(!$item->howmuch) $item->howmuch = 0 ;
	if(!$item2->howmuch) $item2->howmuch = 0 ;
	if(!$item3->howmuch) $item3->howmuch = 0 ;
	if(!$item4->howmuch) $item4->howmuch = 0 ;
	
	$average += $item->howmuch ;
	
	$js .= "data".$rand.".setValue(".$i.", 1, ".$item->howmuch.");\n" ;
	$js .= "data".$rand.".setValue(".$i.", 2, ".$item2->howmuch.");\n" ;
	if($params->get('component3')) $js .= "data".$rand.".setValue(".$i.", 3, ".$item3->howmuch.");\n" ;
	if($params->get('component4')) $js .= "data".$rand.".setValue(".$i.", 4, ".$item4->howmuch.");\n" ;
	$js .= "data".$rand.".setValue(".$i.", 0, \"".$label."\");\n" ;
	
	$current-- ;
	$i++ ;
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
	case 'area':
		$chartype = "AreaChart" ;
	break;
	case 'smooth':
		$chartype = "LineChart" ;
		$curveType = "curveType: 'function'," ;
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
	break;
}

if($params->get('stacked')){
	$stacked = "isStacked: true," ;
}

$colors = array();

$linecolor = $params->get('linecolor') ;
if(substr($linecolor,0,1) != "#") $linecolor = "#".$linecolor ;
$linecolor2 = $params->get('linecolor2') ;
if(substr($linecolor2,0,1) != "#") $linecolor2 = "#".$linecolor2 ;
$linecolor3 = $params->get('linecolor3') ;
if(substr($linecolor3,0,1) != "#") $linecolor3 = "#".$linecolor3 ;
$linecolor4 = $params->get('linecolor4') ;
if(substr($linecolor4,0,1) != "#") $linecolor4 = "#".$linecolor4 ;

if($params->get('linecolor')) $colors[] =	"'".$linecolor."'" ;
if($params->get('linecolor2')) $colors[] =	"'".$linecolor2."'" ;
if($params->get('linecolor3')) $colors[] =	"'".$linecolor3."'" ;
if($params->get('linecolor4')) $colors[] =	"'".$linecolor4."'" ;

if(!$params->get('linecolor')) $comment_colors = "//" ;

$colorstring = implode(",", $colors) ;

$responsive = $params->get('responsive') ;

$js .= "var width".$rand." = document.getElementById('chart_div".$rand."').offsetWidth;" ;
$js .= "var height".$rand." = document.getElementById('chart_div".$rand."').offsetHeight;" ;

if($responsive) $thewidth = "width".$rand ;
else $thewidth = $params->get('width') ;

$labelcolor = $params->get('labelcolor','#999999') ;
if(substr($labelcolor,0,1) != "#") $labelcolor = "#".$labelcolor ;

$js .= "var chart".$rand." = new google.visualization.".$chartype."(document.getElementById('chart_div".$rand."'));
chart".$rand.".draw(data".$rand.", {
	width: ".$thewidth.", 
	height: ".$params->get('height').",
	".$comment_colors."colors: [".$colorstring."],
	lineWidth: ".$params->get( 'linewidth', '2').",
	".$curveType."
	".$stacked."
	".$steps."
	chartArea: {left: ".$params->get('space_left').", top: ".$params->get('space_top').", width: (width".$rand." - ".$params->get('space_right')."), height: ".($params->get('height')-$params->get('space_bottom'))."},
	legend: '".$params->get('legendposition', 'none')."',
	fontSize: 11,
	hAxis: {showTextEvery: ".$params->get('num_labels').",
			textStyle: {color: '".$labelcolor."'}},
	vAxis: {textStyle: {color: '".$labelcolor."'}},
	pointSize: ".$markersize.",
	backgroundColor: ".$backgroundcolor."
	});
}" ;

if($responsive) $js .= "jQuery( window ).resize(function() { drawChart".$rand."(); })";

$document->addScriptDeclaration($js);

echo $params->get('introtext');

if($params->get('debug')) echo "<pre>".$js."</pre>" ;
echo "<div id='chart_div".$rand."'></div>" ;
echo $params->get('introtext2');		  
		  
?>