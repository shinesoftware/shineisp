<?php
/**
 * 
 * @author Shine Software
 *
 */
class Shineisp_Api_Registrars_Base {
	
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