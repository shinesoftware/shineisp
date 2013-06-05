<?php

/**
 * DirectAdmin Plugin
 *
 */
class Shineisp_Plugins_DirectAdmin_Main implements Shineisp_Plugins_Interface {
	
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
			$em->attach('products_activate', array(__CLASS__, 'activate'), 100);
		}
		return $em;
	}
	
	/**
	 * Activate Method 
	 * 
	 * @param unknown_type $event
	 */
	public static function activate($event){
		Shineisp_Commons_Utilities::log('Events triggered: products_activate');
	}

}

	