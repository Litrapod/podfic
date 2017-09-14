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
defined('_JEXEC') or die('Restricted access'); ?>

<table class="table table-striped table-hover">
  <thead>
    <tr>
      <th width="5" class="hidden-phone"> <?php echo JHTML::_( 'grid.sort', 'ID', 'st.id', $this->lists['order_Dir'], $this->lists['order']); ?> </th>
      <th width="20" class="hidden-phone"> <input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" />
      </th>
      <th class="hidden-phone"> <?php echo JHTML::_( 'grid.sort', 'COMPONENT', 'st.component', $this->lists['order_Dir'], $this->lists['order']); ?> </th>
      <th > <?php echo JHTML::_( 'grid.sort', 'USER', 'u.name', $this->lists['order_Dir'], $this->lists['order']); ?> | 
      <?php echo JHTML::_( 'grid.sort', 'TYPE_OF_ACTION', 'st.type', $this->lists['order_Dir'], $this->lists['order']); ?> | 
      <?php echo JHTML::_( 'grid.sort', 'ITEM', 'st.reference_id', $this->lists['order_Dir'], $this->lists['order']); ?> </th>
      <th class=""> <?php echo JHTML::_( 'grid.sort', 'DATE', 'st.date_event', $this->lists['order_Dir'], $this->lists['order']); ?> </th>
      
      <th class="hidden-phone"> <?php echo JHTML::_( 'grid.sort', 'IP', 'st.ip', $this->lists['order_Dir'], $this->lists['order']); ?> </th>
      
      <th class="hidden-phone"> <?php echo JHTML::_( 'grid.sort', 'Country', 'st.country', $this->lists['order_Dir'], $this->lists['order']); ?> </th>
      <th class="hidden-phone"> <?php echo JHTML::_( 'grid.sort', 'State', 'st.state', $this->lists['order_Dir'], $this->lists['order']); ?> </th>
      <th class="hidden-phone"> <?php echo JHTML::_( 'grid.sort', 'City', 'st.city', $this->lists['order_Dir'], $this->lists['order']); ?> </th>
    </tr>
  </thead>
  <?php
$k = 0;
$hours = $this->params->get('hours', '+00:00');

for ($i=0, $n=count( $this->items ); $i < $n; $i++)	{
$row = &$this->items[$i];
$checked 	= JHTML::_('grid.id',   $i, $row->id );
$link_component 		= JRoute::_( 'index.php?option=com_contentstats&component_id='. $row->component .'&type_id=' . '&item_id=');
$link_type 		= JRoute::_( 'index.php?option=com_contentstats&component_id='. $row->component . '&type_id='. $row->type . '&item_id=');
$link_item 		= JRoute::_( 'index.php?option=com_contentstats&component_id='. $row->component . '&type_id='. $row->type . '&item_id='. $row->reference_id );
$link_user 		= JRoute::_( 'index.php?option=com_contentstats&user_id='. $row->user_id );

$link_country 		= JRoute::_( 'index.php?option=com_contentstats&country_id='. $row->country );

if(isset($this->countrynames[$row->country])) $countryname = $this->countrynames[$row->country] ;
else $countryname = "";

$date = JFactory::getDate($row->date_event);
$date_format = $date->toISO8601();
$date_format = substr($date_format, 0, strpos($date_format, "+")) . $hours;

?>
  <tr class="<?php echo "row$k"; ?>">
    <td class="hidden-phone"><?php echo $row->id; ?></td>
    <td class="hidden-phone"><?php echo $checked; ?></td>
    <td class="hidden-phone"><a rel="tooltip" title="<?php echo JText::_('CLICK_TO_APPLY_COMPONENT_FILTER'); ?>" href="<?php echo $link_component; ?>"><?php echo $row->component; ?></a></td>
    <td class="" ><?php if($row->user_id){ ?>
        <a rel="tooltip" title="<?php echo JText::_('CLICK_TO_APPLY_FILTER_USER'); ?>" href="<?php echo $link_user; ?>"><strong><?php echo $row->username; ?></strong></a> 
        <?php } else { echo JText::_('UNREGISTERED'); } ?> 
      <a class="muted" rel="tooltip" title="<?php echo JText::_('CLICK_TO_APPLY_FILTER_ACTION'); ?>" href="<?php echo $link_type; ?>"><?php echo $this->types[$row->component][$row->type] ? $this->types[$row->component][$row->type] : $row->type ; ?></a> 
      <a rel="tooltip" title="<?php echo JText::_('CLICK_TO_APPLY_FILTER_ITEM'); ?>" href="<?php echo $link_item; ?>"><?php echo $this->item_name($row->component, $row->type, $row->reference_id, $row->id); ?></a> 
      
      </td>
      <td align="right" class="text-right">
      <span class=""><time class="timeago" datetime="<?php echo $date_format; ?>"><?php echo JHTML::_('date', $row->date_event, JText::_('DATE_FORMAT_LC2')); ?></time></span>
      </td>

    <td class="hidden-phone muted"><?php if($row->ip == "::1" || $row->ip == "127:0:0:1") echo JText::_('localhost'); else echo $row->ip; ?></td>
    
    <td class="hidden-phone"><?php if($row->country) echo JHTML::image('administrator/components/com_contentstats/assets/images/flags/'.strtolower($row->country).'.png', $row->country); ?> <a rel="tooltip" title="<?php echo JText::_('CLICK_TO_APPLY_COUNTRY_FILTER'); ?>" href="<?php echo $link_country; ?>"><?php echo ucwords(strtolower($countryname)); ?></a></td>
    <td class="hidden-phone"><?php echo ucwords(strtolower($row->state)); ?></td>
    <td class="hidden-phone"><?php echo ucwords(strtolower($row->city)); ?></td>
  </tr>
  <?php
$k = 1 - $k;
}
?>
  
</table>