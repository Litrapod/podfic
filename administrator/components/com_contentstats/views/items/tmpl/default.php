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

<?php    
    $modules = JModuleHelper::getModules("contentstats_backend_1");
    $document = JFactory::getDocument();
    $renderer = $document->loadRenderer('module');
    $attribs  = array();
    $attribs['style'] = 'xhtml';
    foreach ( @$modules as $mod ) 
    {
      echo $renderer->render($mod, $attribs);
    }
    ?>

<form action="index.php" method="post" name="adminForm" id="adminForm" class="form-horizontal">
  <div id="j-main-container" >

    <div class="navbar filter-bar">
    <div class="navbar-inner">

        <input type="text" name="keywords" id="keywords" value="<?php echo $this->keywords;?>" class="text_area"  placeholder="<?php echo JText::_( 'TYPE_TO_SEARCH' ); ?>" />
        
        <?php echo $this->lists['component_id']; ?>
       
        <input type="text" name="type_id" id="type_id" value="<?php echo $this->type_id;?>" class="text_area smallinput" size="4" placeholder="<?php echo JText::_( 'TYPE' ); ?>" />
       
        <input type="text" name="item_id" id="item_id" value="<?php echo $this->item_id;?>" class="text_area smallinput" size="4" placeholder="<?php echo JText::_( 'ITEM' ); ?>" />
       
        <input type="text" name="user_id" id="user_id" value="<?php echo $this->user_id;?>" class="text_area smallinput" size="4" placeholder="<?php echo JText::_( 'USER' ); ?>" />
       
        <?php echo JHTML::calendar($this->date_in, "date_in", "date_in", "%Y-%m-%d", array("class" => "text_area mediuminput", "placeholder" => JText::_( 'FROM' ) )); ?>
    
        <?php echo JHTML::calendar($this->date_out, "date_out", "date_out", "%Y-%m-%d", array("class" => "text_area mediuminput", "placeholder" => JText::_( 'TO' ) )); ?>
      
        <?php echo $this->lists['country_id']; ?>
        
      <div class="btn-group ">
        <button class="btn tip hasTooltip btn-inverse" type="submit" onclick="this.form.submit();" title="<?php echo JText::_('GO'); ?>"><i class="icon-search"></i></button>
        <button class="btn tip hasTooltip" type="button" onclick="document.getElementById('keywords').value='';this.form.getElementById('type_id').value='';this.form.getElementById('item_id').value='';this.form.getElementById('user_id').value='';this.form.getElementById('component_id').selectedIndex = 0;this.form.getElementById('country_id').selectedIndex = 0;this.form.getElementById('date_in').value='';this.form.getElementById('date_out').value='';this.form.submit();" title="<?php echo JText::_('RESET'); ?>"><i class="icon-remove"></i></button>
      </div>
      <?php $user = JFactory::getUser();
      if ($user->authorise('core.delete', 'com_contentstats')) { ?>
      <a class="btn button pull-right btn-danger leftmargin" href="<?php echo JRoute::_('index.php?option=com_contentstats&task=delete'); ?>"><i class="icon-trash"></i> <?php echo JText::sprintf('DELETE_ENTRIES',$this->pagination->total); ?></a>
      <?php } ?>
      <a class="btn button pull-right btn-warning leftmargin" href="<?php echo JRoute::_('index.php?option=com_contentstats&task=export'); ?>"><i class="icon-share"></i> <?php echo JText::sprintf('EXPORT_ROWS',$this->pagination->total); ?></a>

      <div class="pull-right totalstats"><?php echo JText::_( 'TOTAL' ); ?>: <?php echo $this->pagination->total; ?> </div>
    </div></div>

    <?php 
    switch($this->params->get('viewformat')){
      case "stream";
      $template = "stream";
      break;
      default:
      $template = "normal";
      break;
    }
    
    echo $this->loadTemplate($template); ?>

    <div align="center" class="pagination"><?php echo $this->pagination->getLimitBox(); ?><br><br> <?php echo $this->pagination->getListFooter();?></div>
  </div>
  <input type="hidden" name="option" value="com_contentstats" />
  <input type="hidden" name="task" value="" />
  <input type="hidden" name="boxchecked" value="0" />
  <input type="hidden" name="controller" value="items" />
  <input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
  <input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
</form>

<?php     
    $modules = JModuleHelper::getModules("contentstats_backend_2");
    $document = JFactory::getDocument();
    $renderer = $document->loadRenderer('module');
    $attribs  = array();
    $attribs['style'] = 'xhtml';
    foreach ( @$modules as $mod ) 
    {
      echo $renderer->render($mod, $attribs);
    }
    ?>
