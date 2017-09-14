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

<table class="table table-striped">
  <thead>
    <tr>
      <th width="5" class="hidden-phone"> <?php echo JHTML::_( 'grid.sort', 'ID', 'st.id', $this->lists['order_Dir'], $this->lists['order']); ?> </th>
      <th width="20" class="hidden-phone"> <input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $this->items ); ?>);" />
      </th>
      <th class="hidden-phone"> <?php echo JHTML::_( 'grid.sort', 'COMPONENT', 'st.component', $this->lists['order_Dir'], $this->lists['order']); ?> </th>
      <th class="hidden-phone"> <?php echo JHTML::_( 'grid.sort', 'USER', 'u.name', $this->lists['order_Dir'], $this->lists['order']); ?> </th>
      <th> <?php echo JHTML::_( 'grid.sort', 'TYPE_OF_ACTION', 'st.type', $this->lists['order_Dir'], $this->lists['order']); ?> </th>
      <th> <?php echo JHTML::_( 'grid.sort', 'ITEM', 'st.reference_id', $this->lists['order_Dir'], $this->lists['order']); ?> </th>
      <th class="hidden-phone"> <?php echo JHTML::_( 'grid.sort', 'DATE', 'st.date_event', $this->lists['order_Dir'], $this->lists['order']); ?> </th>
      <th class="visible-phone"> <?php echo JHTML::_( 'grid.sort', 'DATE', 'st.date_event', $this->lists['order_Dir'], $this->lists['order']); ?> </th>
      <th class="hidden-phone"> <?php echo JHTML::_( 'grid.sort', 'IP', 'st.ip', $this->lists['order_Dir'], $this->lists['order']); ?> </th>
      
      <th class="hidden-phone"> <?php echo JHTML::_( 'grid.sort', 'Country', 'st.country', $this->lists['order_Dir'], $this->lists['order']); ?> </th>
      <th class="hidden-phone"> <?php echo JHTML::_( 'grid.sort', 'State', 'st.state', $this->lists['order_Dir'], $this->lists['order']); ?> </th>
      <th class="hidden-phone"> <?php echo JHTML::_( 'grid.sort', 'City', 'st.city', $this->lists['order_Dir'], $this->lists['order']); ?> </th>
    </tr>
  </thead>
  <?php
$k = 0;
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

?>
  <tr class="<?php echo "row$k"; ?>">
    <td class="hidden-phone"><?php echo $row->id; ?></td>
    <td class="hidden-phone"><?php echo $checked; ?></td>
    <td class="hidden-phone"><a rel="tooltip" title="<?php echo JText::_('CLICK_TO_APPLY_COMPONENT_FILTER'); ?>" href="<?php echo $link_component; ?>"><?php echo $row->component; ?></a></td>
    <td class="hidden-phone"><?php if($row->user_id){ ?>
      <a rel="tooltip" title="<?php echo JText::_('CLICK_TO_APPLY_FILTER_USER'); ?>" href="<?php echo $link_user; ?>"><?php echo $row->username; ?></a> [<?php echo $row->user_id; ?>]
      <?php } else { echo JText::_('UNREGISTERED'); } ?></td>
    <td><a rel="tooltip" title="<?php echo JText::_('CLICK_TO_APPLY_FILTER_ACTION'); ?>" href="<?php echo $link_type; ?>"><?php echo $this->types[$row->component][$row->type] ? $this->types[$row->component][$row->type] : $row->type ; ?></a> [<?php echo $row->type; ?>]</td>
    <td><a rel="tooltip" title="<?php echo JText::_('CLICK_TO_APPLY_FILTER_ITEM'); ?>" href="<?php echo $link_item; ?>"><?php echo $this->item_name($row->component, $row->type, $row->reference_id, $row->id); ?></a> [<?php echo $row->reference_id; ?>]</td>
    <td class="hidden-phone"><?php echo JHTML::_('date', $row->date_event, JText::_('DATE_FORMAT_LC2')); ?></td>
    <td class="visible-phone"><?php echo JHTML::_('date', $row->date_event, JText::_('DATE_FORMAT_LC4')); ?></td>
    <td class="hidden-phone"><?php if($row->ip == "::1" || $row->ip == "127:0:0:1") echo JText::_('localhost'); else echo $row->ip; ?></td>
    
    <td class="hidden-phone"><?php if($row->country) echo JHTML::image('administrator/components/com_contentstats/assets/images/flags/'.strtolower($row->country).'.png', $row->country); ?> <a rel="tooltip" title="<?php echo JText::_('CLICK_TO_APPLY_COUNTRY_FILTER'); ?>" href="<?php echo $link_country; ?>"><?php echo ucwords(strtolower($countryname)); ?></a></td>
    <td class="hidden-phone"><?php echo ucwords(strtolower($row->state)); ?></td>
    <td class="hidden-phone"><?php echo ucwords(strtolower($row->city)); ?></td>
  </tr>
  <?php
$k = 1 - $k;
}
?>
  
</table>