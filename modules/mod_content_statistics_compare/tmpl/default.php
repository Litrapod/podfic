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
defined('_JEXEC') or die('Restricted access'); ?>
<?php
if($params->get( 'Itemid' )) $itemid = "&Itemid=".$params->get( 'Itemid' );
else $itemid = "";
$document =& JFactory::getDocument();
$document->addStyleSheet(JURI::base(true).'/modules/mod_content_statistics_compare/tmpl/statistics.css');

//print_r($items) ;

/*http://chart.apis.google.com/chart?cht=lc&chs=215x170&chdlp=b&chco=3D7930&chxt=y%2Cx&chxr=0%2C0%2C46&chd=e%3AYyAAQwOxsC2i..&chxs=1%2C676767%2C11.5%2C0%2Clt%2C676767&chxl=1%3A%7C1%7C2%7C3%7C4%7C5%7C6%7C7&chxp&chg=16.6666%2C-1%2C1%2C1&chls=2&chm=B%2CC5D4B5BB%2C0%2C0%2C0
*/

/*

http://chart.apis.google.com/chart?cht=bvs&chs=1000x300&chd=t:674,49,799,459,392,19,71,498,183,36,185,24,444,0,39,1407,886,0,441,0,227,34,0,0&chco=4d89f9&chds=0,1457&chxt=x,y,r&chxr=1,0,1457|2,0,1457&chxl= 0:|egluu|UA|UAB|UB|UdG|UIC|UJI|UdL|UMH|UP|UPC|UPV|UPF|URL|URV|UV|UVic|CESCA|CEU|CSCB|RedIris|AndorPA|Andorra|ISP UdA|&chbh=a&chm=N*f0*,000000,0,-1,11

*/

//label style
//$values['chxs'] = '1,'.$params->get('labelcolor','676767').',11.5,0,1,'.$params->get('labelcolor','676767');

$values['chxt'] = 'y,x';
$values['chs'] = $params->get('width').'x'.$params->get('height');

//chart type
$values['cht'] = $params->get('chart','lc');

$values['chdlp'] = 'b';


//legend
$legend = $params->get('legend') ;
if($legend){
	
	$search = array('artist_name','album_name','song_name');
	$replace = array($names->artist_name, $names->album_name, $names->song_name);
	$legend = str_replace($search, $replace, $legend) ;
	
	//$legend = str_replace('artistname', $names->artist_name, $legend) ;
	
	$values['chdl'] = $legend ;
	
	$values['chdlp'] = $params->get('legendposition','b');
}

if($params->get('linetype','s') == 's') $values['chls'] = $params->get( 'linewidth', '2');
elseif($params->get('linetype','s') == 'd') $values['chls'] = $params->get( 'linewidth', '2').','.$params->get( 'dashlength', '4').','.$params->get( 'spacelength', '2');
//$values['chm'] = 'B,C5D4B5BB,0,0,0' ;

//colors

$values['chco'] = $params->get('linecolor') ; // line color

if($params->get('backgroundcolor')){
	$backcolors[] = 'bg,s,'.$params->get('backgroundcolor') ; //chart background color
}
if($params->get('chartareacolor')){
	$backcolors[] = 'c,s,'.$params->get('chartareacolor') ; //chart area color
}

if(count($backcolors)){
	$values['chf'] = implode("|", $backcolors) ;
}

//values printed
//$values['chbh'] = 'a';
//$values['chm'] = 'N*f0*,000000,0,-1,11' ;
//$values['chm'] = 'B,'.$params->get('fillcolor','C5D4B5').$params->get('filltransparency').',0,0,0' ;

$days = $params->get( 'num_units_time', 7 ); 
$current = $days ;
$max = 0; 

$num_labels = $params->get( 'num_labels', 1 ); 
$i = 0 ;

$padding = ($days - 1) % ($num_labels) ;
//echo $padding ;

$date_format = $params->get('other_label_format') ? $params->get('other_label_format') : $params->get('label_format', '%a') ;

foreach($items as $item){
	$item2 =& $items2[$i] ;
	//$item2->howmuch += $item->howmuch ;
	
	$thedata[] = $item->howmuch ? $item->howmuch : '0';
	$thedata2[] = $item2->howmuch ? $item2->howmuch : '0';
	
	if($item->howmuch > $max) $max = $item->howmuch ;
	if($item2->howmuch > $max) $max = $item2->howmuch ;
	//$thelabels[] = JHTML::_('date', $item->date_event, '%D') ;
	
	if(!(($i - $padding) % $num_labels)) $thelabels[] = JHTML::_('date', strtotime('-'.($current-1). $params->get('time')), JText::_($date_format)) ;
	
	else $thelabels[] = '' ;
	
	$current-- ;
	$i++ ;
}

//the DATA
$values['chd'] = 't:' . implode(",", $thedata) . '|' . implode(",", $thedata2);
//print_r($thedata);
//the X axis labels
$values['chxl'] =  '1:|' . implode("|", $thelabels);


if($params->get('fillcolor')){
	 $values['chm'] = 'B,'.$params->get('fillcolor').$params->get('filltransparency').',0,0,0'  ;
	 
	 
	 $fills = explode(",",$params->get('fillcolor')) ;
	 if(count($fills) == 2){
		 $markers[] = 'B,'.$fills[0].$params->get('filltransparency').',0,1,0|B,'.$fills[1].$params->get('filltransparency').',1,2,0'  ;
	 }
	 else{
		 $markers[] = 'B,'.$params->get('fillcolor').$params->get('filltransparency').',0,0,0'  ;
	 }
	 
}

if($params->get('markers')){
	 $markers[] = $params->get('markers').','.$params->get('markercolor').',0,-'.$params->get('markerpoints','1').','.$params->get('markersize')  ;
}

if($params->get('show_values',1)){
	//numbers and backcolor	
	//http://code.google.com/apis/chart/docs/chart_params.html#gcharts_range_markers
	$cadena = 'N'.$params->get('symbol_before','').'*f'.$params->get('decimal',0).'*'.$params->get('symbol_after','').','.$params->get('numbercolor','000000').',0,-1,' .$params->get('numbersize','11') ;
	$markers[] = $cadena ;
	
}

if($params->get('filllines')){
	 $markers[] = 'v,'.$params->get('filllinescolor').',0,::'.$params->get('filllinespoints','1').','.$params->get('filllineswidth')  ;
}

$values['chm'] = implode("|", $markers) ;

//$values['chm'] = 'v,'.$params->get('fillcolor').',0,::.25,2' .'|B,'.$params->get('fillcolor').$params->get('filltransparency').',0,0,0'.'|o,0066FF,0,-.5,10'  ;


$extra_space = 0 ;

if($params->get('show_values',1)){
	//numbers and backcolor	
	//http://code.google.com/apis/chart/docs/chart_params.html#gcharts_range_markers
	$cadena = 'N'.$params->get('symbol_before','').'*f'.$params->get('decimal',0).'*'.$params->get('symbol_after','').','.$params->get('numbercolor','000000').',0,-1,11' ;
	if($params->get('fillcolor')) $values['chm'] .= "|".$cadena ;
	else $values['chm'] = $cadena ;
		//$extra_space = 20 ;
}


$values['chxr'] = '0,0,'. ($max + $extra_space)   ;
$values['chds'] = '0,' . ($max + $extra_space) ;

switch($params->get('chart','lc')){
	case 'bvg':
	//chxr=0,0,46&chxt=y&chbh=a&chs=700x200&cht=bvg&chco=49188F&chd=s:Xhiugtqi&chg=-1,-1,1,1&chm=B,76A4FB,0,0,0

	//bar width and spacing
	$values['chbh']= 'a' ;
	$values['chg'] = '-1,-1,1,1';
	$values['chxr'] = '0,0,'. ($max + $extra_space) .'|1,0,0'  ;
	
	//colors
	$values['chco'] = str_replace(",","|", $params->get('linecolor') );
	
	break;
	case 'lc':default:
		//GRID
		$horizontal_division = 100 / ($days - 1) ;
		
		if($params->get('gridtype','s') == 's') $values['chg'] = $horizontal_division . ',-1,1,0';
		elseif($params->get('gridtype','s') == 'd') $values['chg'] = $horizontal_division . ',-1,'.$params->get( 'griddashlength', '1').','.$params->get( 'gridspacelength', '1');
	break;
}




//constructing the URL
foreach($values as $key => $value){
	$string[] = $key."=".urlencode($value) ;
	$string_noencoded[] = $key."=".$value ;
}

$vars = implode("&", $string);

$img_url = "http://chart.apis.google.com/chart?" . $vars;

echo "http://chart.apis.google.com/chart?" . implode("&", $string_noencoded);
//echo $img_url;
echo JHTML::image($img_url, JText::_('Chart'), array("class" => "imageevolution", "width" => $params->get('width'), "height" => $params->get('height')));
		  
		  
?>