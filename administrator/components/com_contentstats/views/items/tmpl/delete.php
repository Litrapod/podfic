<?php 

/*------------------------------------------------------------------------
# com_contentstats - Invoices for Joomla
# ------------------------------------------------------------------------
# author        Germinal Camps
# copyright       Copyright (C) 2012 JoomlaContentStatistics.com. All Rights Reserved.
# @license        http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites:       http://www.JoomlaContentStatistics.com
# Technical Support:  Forum - http://www.JoomlaContentStatistics.com/forum
-------------------------------------------------------------------------*/
defined('_JEXEC') or die('Restricted access'); ?>
<div class="row-fluid">
<div class="well span4 offset4"  >
<h1><?php echo JText::_( 'PLEASE_CONFIRM_DELETE' ); ?></h1>
<?php echo JText::_( 'SURE_DELETE' ); ?><br />

<dl class="dl-horizontal">
<dt><?php echo JText::_( 'COMPONENT' ); ?></dt> <dd><?php echo $this->component_name ? $this->component_name : "<span class='muted'>".JText::_( 'NO_FILTER_APPLIED' ) ."</span>";?></dd>

<dt><?php echo JText::_( 'TYPE' ); ?></dt> <dd><?php echo $this->type_id ? $this->type_id : "<span class='muted'>".JText::_( 'NO_FILTER_APPLIED' ) ."</span>";?></dd>

<dt><?php echo JText::_( 'ITEM' ); ?></dt> <dd><?php echo $this->item_id ? $this->item_id : "<span class='muted'>".JText::_( 'NO_FILTER_APPLIED' ) ."</span>";?></dd>

<dt><?php echo JText::_( 'USER' ); ?></dt> <dd><?php echo $this->user_id ? $this->user_id : "<span class='muted'>".JText::_( 'NO_FILTER_APPLIED' ) ."</span>";?></dd>

<dt><?php echo JText::_( 'FROM' ); ?></dt> <dd><?php echo $this->date_in ? JHTML::_('date', $this->date_in, JText::_('DATE_FORMAT_LC2')) : "<span class='muted'>".JText::_( 'NO_FILTER_APPLIED' ) ."</span>"; ?></dd>

<dt><?php echo JText::_( 'TO' ); ?></dt> <dd><?php echo $this->date_out ? JHTML::_('date', $this->date_out, JText::_('DATE_FORMAT_LC2')) : "<span class='muted'>".JText::_( 'NO_FILTER_APPLIED' ) ."</span>"; ?></dd>

<dt><?php echo JText::_( 'COUNTRY' ); ?></dt> <dd><?php echo $this->country_name ? ucwords(strtolower($this->country_name)) : "<span class='muted'>".JText::_( 'NO_FILTER_APPLIED' ) ."</span>";?></dd>
</dl>

<?php echo JText::sprintf( 'TOTAL_DELETED' , $this->pagination->total); ?> <br /><br />

<a class="btn button btn-danger" href="<?php echo JRoute::_('index.php?option=com_contentstats&task=delete&confirm=1'); ?>"><i class="icon-trash"></i> <?php echo JText::sprintf( 'YES_DELETE' , $this->pagination->total); ?></a>
 
<a class="btn button" href="<?php echo JRoute::_('index.php?option=com_contentstats'); ?>"><?php echo JText::_( 'NO_DELETE' ); ?></a>
</div>
</div>
