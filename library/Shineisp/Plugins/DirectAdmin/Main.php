<?php

/**
 * DirectAdmin Test Plugin
 * @author GUEST.it s.r.l. <assistenza@guest.it>
 *
 */
class Shineisp_Plugins_DirectAdmin_Main implements Shineisp_Plugins_Interface {
	// onInit is used to subscribe for events through MessageBus
	public function onInit() {
		Shineisp_MessageBus::getInstance()->subscribe('products.activate', get_class($this), 'onActivate');
		Shineisp_MessageBus::getInstance()->subscribe('products.*', get_class($this), 'onActivateProvaRegexp');
	}	
	
	public function onActivate($eventName, $data = null) {
		Shineisp_Commons_Utilities::logs ( "Sono dentro Shineisp_Plugins_DirectAdmin_Main->onActivate. Ho ricevuto l'evento ".$eventName." con questi dati: ".$data, "messagebus.log" );
	}
	
	public function onActivateProvaRegexp($eventName, $data = null) {
		Shineisp_Commons_Utilities::logs ( "Sono dentro Shineisp_Plugins_DirectAdmin_Main->onActivateProvaRegexp. Ho ricevuto l'evento ".$eventName." con questi dati: ".$data, "messagebus.log" );
	}
}

	