<?php 

/*------------------------------------------------------------------------
# com_contentstats - Content Statistics for Joomla
# ------------------------------------------------------------------------
# author        Germinal Camps
# copyright       Copyright (C) 2013 JoomlaContentStatistics.com. All Rights Reserved.
# @license        http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites:       http://www.JoomlaContentStatistics.com
# Technical Support:  Forum - http://www.JoomlaContentStatistics.com/forum
-------------------------------------------------------------------------*/

defined('_JEXEC') or die('Restricted access'); 

$params = JComponentHelper::getParams( 'com_contentstats' );

$sep = $params->get("csvsep",";");
$rowsep = "\n";
?>
<?php echo JText::_('ID'); ?><?php echo $sep; ?>
<?php echo JText::_('USER'); ?><?php echo $sep; ?>
<?php echo JText::_('USER_ID'); ?><?php echo $sep; ?>
<?php echo JText::_('TYPE_OF_ACTION'); ?><?php echo $sep; ?>
<?php echo JText::_('ITEM'); ?><?php echo $sep; ?>
<?php echo JText::_('ITEM_ID'); ?><?php echo $sep; ?>
<?php echo JText::_('DATE'); ?><?php echo $sep; ?>
<?php echo JText::_('IP'); ?><?php echo $sep; ?>
<?php echo JText::_('COUNTRY'); ?><?php echo $sep; ?>
<?php echo JText::_('STATE'); ?><?php echo $sep; ?>
<?php echo JText::_('CITY'); ?><?php echo $sep; ?><?php echo $rowsep; ?>
<?php
  $k = 0;
  for ($i=0, $n=count( $this->items ); $i < $n; $i++) {
    $row = &$this->items[$i];

    if(isset($this->countrynames[$row->country])) $countryname = $this->countrynames[$row->country] ;
    else $countryname = "";
    
    ?><?php echo $row->id; ?><?php echo $sep; ?><?php if($row->user_id){ ?><?php echo $row->username; ?><?php } else { echo JText::_('UNREGISTERED'); } ?><?php echo $sep; ?><?php echo $row->user_id; ?><?php echo $sep; ?><?php echo $this->types[$row->component][$row->type] ? $this->types[$row->component][$row->type] : $row->type ; ?><?php echo $sep; ?><?php echo $this->item_name($row->component, $row->type, $row->reference_id, $row->id); ?><?php echo $sep; ?><?php echo $row->reference_id; ?><?php echo $sep; ?><?php echo $row->date_event; ?><?php echo $sep; ?><?php if($row->ip == "::1" || $row->ip == "127:0:0:1") echo JText::_('localhost'); else echo $row->ip; ?><?php echo $sep; ?><?php echo ucwords(strtolower($countryname)); ?><?php echo $sep; ?><?php echo ucwords(strtolower($row->state)); ?><?php echo $sep; ?><?php echo ucwords(strtolower($row->city)); ?><?php echo $rowsep; ?><?php
    $k = 1 - $k;
  }
  ?>