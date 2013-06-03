<?php

/**
 * ShineISP Internal MessageBus
 * @author GUEST.it s.r.l. <assistenza@guest.it>
 * 
 * This is a publish/subscribe message queue. 
 * 
 * One or more listeners (plugins, hooks, ...) will subscribe for one or more event with "subscribe" method.
 * 
 * One or more publisher will then publish JSON encoded messages, bound to an event, with "publish" method.
 * 
 * regexp are supported for listeners. In this way, one listener can subscribe for multiple events
 *
 */
class Shineisp_MessageBus {
	private static $instance = null;
	protected $events        = array();
	
	// constructor must stay private. With this private, will not be possible to instantite MessageBus directly
	private function __construct() {
    
	}
	
	public static function getInstance() {
    	if(self::$instance == null) {   
        	$c = __CLASS__;
         	self::$instance = new $c;
      	}
      
      	return self::$instance;
   	}	
	
    public function subscribe($eventName, $object, $callback) {
    	Shineisp_Commons_Utilities::logs ( "subscribed event '".$eventName."' with callback '".$object."->".$callback."'", "messagebus.log" );
        if (!isset($this->events[$eventName])) {
        	$this->events[$eventName] = array();
        }
        $this->events[$eventName][] = array($object,$callback);
    }
	
    public function publish($eventName, $data = null) {
    	Shineisp_Commons_Utilities::logs ( "triggered event '".$eventName."' with data '".serialize($data)."'", "messagebus.log" );
		foreach ( $this->events as $eventKey => $events ) {
			$regExp = '#'.$eventKey.'#';
			Shineisp_Commons_Utilities::logs ( "   trying regexp '".$regExp."' for event '".$eventName."'", "messagebus.log" );
			
			if ( preg_match($regExp, $eventName) ) {
				Shineisp_Commons_Utilities::logs ( "   event '".$eventName."' match regexp '".$eventKey."'", "messagebus.log" );

		        foreach ($events as $callableEvent) {
		        	list($object, $callback) = $callableEvent;
		        	Shineisp_Commons_Utilities::logs ( "   found callback '".$object."->".$callback."' for event '".$eventName."'", "messagebus.log" );
					
					$callableObject = new $object;
		        	$callableObject->$callback($eventName, $data);
		        }
			}	
		}
    }
}
