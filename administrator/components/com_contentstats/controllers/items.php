<?php

/*------------------------------------------------------------------------
# com_contentstats - Invoices for Joomla
# ------------------------------------------------------------------------
# author				Germinal Camps
# copyright 			Copyright (C) 2012 JoomlaContentStatistics.com. All Rights Reserved.
# @license				http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: 			http://www.JoomlaContentStatistics.com
# Technical Support:	Forum - http://www.JoomlaContentStatistics.com/forum
-------------------------------------------------------------------------*/

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class StatsControllerItems extends StatsController
{
	
	function __construct()
	{
		JRequest::setVar('view', 'items');
		parent::__construct();

	}
	
	function remove()
	{
        $user = JFactory::getUser();
        if ($user->authorise('core.delete', 'com_contentstats')) {
            $model = $this->getModel('items');
            if(!$model->delete_items()) {
                $msg = JText::_( 'ERROR_DELETING_ENTRIES' );
            } else {
                $msg = JText::_( 'ENTRIES_DELETED' );
            }

            $this->setRedirect( 'index.php?option=com_contentstats', $msg );
        } else {
            $link = 'index.php?option=com_contentstats';
            $msg = JText::_('YOU_CANNOT_DELETE');

            $this->setRedirect($link, $msg);
        }

	}
  
}
