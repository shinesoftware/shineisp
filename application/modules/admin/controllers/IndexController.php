<?php

/**
 * IndexController
 * 
 * @author 
 * @version 
 */

class Admin_IndexController extends Shineisp_Controller_Admin {
	
    
    public function preDispatch() {
        $this->getHelper ( 'layout' )->setLayout ( 'blank' );
    }	
    
	/**
	 * The default action - show the home page
	 */
	public function indexAction() {
		$auth = Zend_Auth::getInstance ();
		$auth->setStorage ( new Zend_Auth_Storage_Session ( 'admin' ) );
		$translator = Shineisp_Registry::getInstance ()->Zend_Translate;
		
		if ($auth->hasIdentity ()) {
			$this->view->show_dashboard = true;
			$this->view->user = $auth->getIdentity();
			$this->getHelper ( 'layout' )->setLayout ( '1column' );
			
			$graph = new Shineisp_Commons_Morris();

			// Get the total of the revenues per year
			$graphdata = $graph->setType('Area')
							    ->setData(Orders::prepareGraphData(array(), 'year'))
								->setElement('yeargraph')
								->setXkey('xdata')
								->setLabels(array($translator->translate('Net Revenue (Taxable Income less Costs)')))
								->setOptions(array('lineColors' => array('#428BCA'), 'preUnits' => Settings::findbyParam('currency') . " "))
								->plot();
			
			$this->view->placeholder ( "admin_endbody" )->append ($graphdata);

			// Get the total of the revenues per quarter of year
			$graphdata = $graph->setType('Area')
							    ->setData(Orders::prepareGraphData(array(), 'quarter'))
								->setElement('quartergraph')
								->setXkey('xdata')
								->setLabels(array($translator->translate('Net Revenue (Taxable Income less Costs)')))
								->setOptions(array('lineColors' => array('#428BCA'), 'preUnits' => Settings::findbyParam('currency') . " "))
								->plot();
			
			$this->view->placeholder ( "admin_endbody" )->append ($graphdata);

			// Get the total of the revenues per quarter of year
			$graphdata = $graph->setType('Bar')
							    ->setData(Orders::prepareGraphData(array(), 'month'))
								->setElement('monthgraph')
								->setXkey('xdata')
								->setLabels(array($translator->translate('Net Revenue (Taxable Income less Costs)')))
								->setOptions(array('barColors' => array('#428BCA'), 'preUnits' => Settings::findbyParam('currency') . " "))
								->plot();
			
			$this->view->placeholder ( "admin_endbody" )->append ($graphdata);
			
		} else {
			$this->_helper->redirector ( 'index', 'login', 'admin' ); // back to login page
		}
	}
	
	
}
