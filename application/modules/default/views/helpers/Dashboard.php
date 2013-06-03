<?php
/**
 *
 * @version 
 */
/**
 * Dashboard helper
 *
 * @uses viewHelper Zend_View_Helper
 */
class Zend_View_Helper_Dashboard extends Zend_View_Helper_Abstract {
	
	public function dashboard() {
		return $this;
	}
	
	/**
	 * GetButtons
	 * List of all buttons
	 * @return array
	 */
	public function GetButtons($module) {
		$translator = Zend_Registry::getInstance ()->Zend_Translate;		

		// get all the buttons from the navigation table
		$buttons = Navigation::getNavItems($module);

		// get the active isp configuration
		$isp = ISP::getCurrentISP();
		
		// Get the URL of the Hosting Control Panel set to add it in the dashboard 
		$panelsettings = SettingsParameters::getParameterbyGroupNameAndVar($isp['isppanel'], $isp['isppanel'] . "_url");
		if(!empty($panelsettings['Settings'][0]['value'])){
			$buttons[] = array('label' => $translator->translate('Hosting Panel'), 'desc' => $translator->translate('Click here to login into your hosting control panel'), 'url' => $panelsettings['Settings'][0]['value']);
		}
		
		// attach the buttons to the view
		$this->view->buttons = $buttons;
		
		return $this->view->render ( 'partials/buttons.phtml' );
	}
	
	/**
	 * DomainsExpiration
	 * List of all the domains near the expiring date
	 * @return array
	 */
	public function DomainsExpiration() {
		$ns = new Zend_Session_Namespace ();
		
		if (!empty($ns->customer)) {
			$data = $ns->customer;
			return array ('records' => Domains::getExpiringDomains($data ['customer_id']), 'actions' => array ('/domains/edit/id/' => 'Show' ), 'pager' => true );
		} else {
			return null;
		}
	}
	
	/**
	 * ServicesExpiration
	 * List of all the services near the expiring date
	 * @return array
	 */
	public function ServicesExpiration() {
		$ns = new Zend_Session_Namespace ();
		
		if (!empty($ns->customer)) {
			$data = $ns->customer;
			return array ('records' => Products::getExpiringProducts($data['customer_id'], $ns->langid), 'actions' => array ('/services/edit/id/' => 'Show' ) );
		} else {
			return null;
		}
	}
	
	/**
	 * ServicesExpiration
	 * List of all the services near the expiring date
	 * @return array
	 */
	public function Last() {
		$ns = new Zend_Session_Namespace ();
		
		if (!empty($ns->customer)) {
			$data = $ns->customer;
			return array ('records' => Tickets::Last($data['customer_id']), 'actions' => array ('/tickets/edit/id/' => 'Show' ) );
		} else {
			return null;
		}
	}
	
}
