<?php
/**
 * 
 * @author Shine Software
 *
 */
class Shineisp_Plugins_Registrars_Base implements Shineisp_Plugins_Interface  {
	
	protected $isLive;
	protected $session;
	protected $actions = array(
							'registerDomain' 		=>  'Register',
							'transferDomain' 		=>  'Transfer',
							'renewDomain' 			=>  'Renew',
							'checkDomain' 			=>  'Check',
							'lockDomain' 			=>  'Lock',
							'unlockDomain'	 		=>  'Unlock',
							'setDomainHosts' 		=>  'Set Domain Host',
							'getDomainHosts' 		=>  'Get Domain Host',
							'setNameServers' 		=>  'Set Name Server',
							'getNameServers' 		=>  'Get Name Server'
						);

	
	public $events;
	
	/**
	 * Events Registration
	 *
	 * (non-PHPdoc)
	 * @see Shineisp_Plugins_Interface::events()
	 */
	public function events()
	{
		$em = Zend_Registry::get('em');
		if (!$this->events && is_object($em)) {
			$em->attach('registrars_start', array(__CLASS__, 'listener_registrars_starts'), 100);
		}
		return $em;
	}
	
	// Event Callback
	public function listener_registrars_starts($event){
		
	}
	
	/**
	 * Get the registrars list
	 * 
	 * @return ArrayObject
	 */
	public function getList($emptyitem = false) {
		$translator = Zend_Registry::getInstance ()->Zend_Translate;
		$registrars = array();
		$path = PROJECT_PATH . "/library/Shineisp/Plugins/Registrars/";
		
		if($emptyitem){
			$registrars[] = $translator->translate("Select ...");
		}
		
		foreach(glob("$path/*", GLOB_ONLYDIR) as $dir) {
		    $dir = str_replace("$path/", '', $dir);
		    $class = "Shineisp_Plugins_Registrars_" . $dir . "_Main";
		    if (class_exists($class)) {
		    	$registrars[$dir] = $dir;
		    }
		}
		return $registrars;
	}
	
	/**
	 * @return the $isLive
	 */
	public function getIsLive() {
		return $this->isLive;
	}

	/**
	 * @param field_type $isLive
	 */
	public function setIsLive($isLive) {
		$this->isLive = $isLive;
	}
	/**
	 * @return the $actions
	 */
	public function getActions() {
		return $this->actions;
	}

	/**
	 * @param field_type $actions
	 */
	public function setActions($actions) {
		$this->actions = $actions;
	}

    /**
	 * Get the DNS Servers configured for the Active ISP
	 * 
	 * @return     array       Dns Servers
	 * @access     private
	 */		
	public function getDnsServers(){
		$items = array();
		$dns = Servers::getDnsserver();
		
		foreach ($dns as $item){
			$items[] = $item['host'] . "." . $item['domain'];
		}
		
		return $items;
	}
	
}