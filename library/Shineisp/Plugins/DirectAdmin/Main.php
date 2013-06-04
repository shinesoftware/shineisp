<?php

/**
 * DirectAdmin Test Plugin
 * @author GUEST.it s.r.l. <assistenza@guest.it>
 *
 */
class Shineisp_Plugins_DirectAdmin_Main implements Shineisp_Plugins_Interface {
	// onInit is used to subscribe for events through MessageBus
	public function events() {
		// Announce availability for each item types supported by this plugin
// 		Shineisp_MessageBus::getInstance()->publish('panels.advertise', json_encode('hosting'));
// 		Shineisp_MessageBus::getInstance()->publish('panels.advertise', json_encode('email'));
// 		Shineisp_MessageBus::getInstance()->publish('panels.advertise', json_encode('domains'));
// 		Shineisp_MessageBus::getInstance()->publish('panels.advertise', json_encode('ftp'));


		// Listen for product activations	
// 		Shineisp_MessageBus::getInstance()->subscribe('products.activate', function() {
// 			list($eventType, $data) = func_get_args();
// 			Shineisp_Commons_Utilities::logs ( "   Sono dentro la Closure di ".__CLASS__.". Ho ricevuto l'evento ".$eventName." con questi dati: ".$data, "messagebus.log" );
// 		});
	}	
}

	