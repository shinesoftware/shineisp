<?php
/**
 *
 * @author Shine Software
 *
 */
class Shineisp_Plugins_Newsletters_Base implements Shineisp_Plugins_Interface {
	
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
			$em->attach('newsletters_start', array(__CLASS__, 'listener_newsletters_start'), 100);
		}
		return $em;
	}
	
	// Event Callback
	public function listener_newsletters_start($event){
	
	}
}