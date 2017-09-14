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

jimport('joomla.application.component.controller');


class StatsController extends JControllerLegacy{

	function display( $cachable = false, $urlparams = array())
	{
		parent::display($cachable,$urlparams);
	}

	function delete(){
		
		$confirm = JRequest::getInt('confirm');
		
		if($confirm){
            $user = JFactory::getUser();
            if ($user->authorise('core.delete', 'com_contentstats')) {

                $model = $this->getModel('items');
                $model->delete();

                $link = 'index.php?option=com_contentstats';
                $msg = JText::_('ENTRIES_DELETED');

                $this->setRedirect($link, $msg);
            } else {
                $link = 'index.php?option=com_contentstats';
                $msg = JText::_('YOU_CANNOT_DELETE');

                $this->setRedirect($link, $msg);
            }
			
		}
		else{
		
			JRequest::setVar('view', 'items');
			JRequest::setVar('layout', 'delete');
			
			parent::display();
		}
	}

	function export(){
		
		require_once(JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_contentstats'.DS.'models'.DS.'items.php');
		require_once(JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_contentstats'.DS.'views'.DS.'items'.DS.'view.html.php');
		
		$model = new StatsModelItems();
		$view = new StatsViewItems();
		
		$params = JComponentHelper::getParams( 'com_contentstats' );

		$max = $params->get('maxrows', 5000);
		
		$total = $model->getTotal();
		if($total > $max){
			$link = 'index.php?option=com_contentstats';
			$msg = JText::sprintf('YOU_CANNOT_EXPORT', $max);
			
			$this->setRedirect($link, $msg);
		}
		else{
		
		// filename for download
		  $filename = "export_" . date('Y_m_d') . ".csv";
		
		  header("Content-Disposition: attachment; filename=\"$filename\"");
		  //header("Content-Type: application/vnd.ms-excel");
		  header("Content-Type: text/csv");

			$view->setModel($model);
			
			$view->pagination = $model->getPagination();
			$view->keywords = $model->getkeywords();
			$view->components =  $model->getComponents();
			$view->component_id =  $model->getComponentId();
			$view->items = $model->getData();	
			$view->types = $model->getTypes();	
			$view->item_id =  $model->getItemId();
			$view->type_id =  $model->getTypeId();
			$view->user_id =  $model->getUserId();
			$view->date_in =  $model->getDateIn();
			$view->date_out =  $model->getDateOut();
			$view->country_id =  $model->getCountryId();
			$view->countrynames =  $view->countries();
			
			$content = $view->loadTemplate( 'csv' );
			
			echo $content;
			
			die;
		
		}
		
	}
}
