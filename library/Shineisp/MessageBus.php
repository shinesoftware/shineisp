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
	
    public function subscribe($eventName, $object, $callback = null) {
    	// Detect Closures
    	if ( $callback == null && ( !is_object($object) ) ) {
    		Shineisp_Commons_Utilities::logs ('Bad subscription. Not a Closure and not an Object. Skipping it.', "messagebus.log" );
			return false;	
    	}
		
		if ( $callback != null ) {
			Shineisp_Commons_Utilities::logs ( $object." has subscribed for event '".$eventName."' with callback '".$callback."'", "messagebus.log" );	
		} else {
			Shineisp_Commons_Utilities::logs ( "a Closure has subscribed for event '".$eventName."'", "messagebus.log" );
		}
    	
        if (!isset($this->events[$eventName])) {
        	$this->events[$eventName] = array();
        }
        $this->events[$eventName][] = array($object,$callback);
    }
	
    public function publish($eventName, $data = null) {
    	Shineisp_Commons_Utilities::logs ( "triggering event '".$eventName."' with data '".serialize($data)."'", "messagebus.log" );
		foreach ( $this->events as $eventKey => $events ) {
			$regExp = '#^'.$eventKey.'$#i';
			
			if ( !preg_match($regExp, $eventName) ) {
				Shineisp_Commons_Utilities::logs ( "Unknown event '".$eventName."'. regexp: ".$regExp, "messagebus.log" );
				continue;
			}	

	        foreach ($events as $callableEvent) {
	        	list($object, $callback) = $callableEvent;
				
				// Properly manage Closures
				if ( $callback == null && is_object($object) ) {
					Shineisp_Commons_Utilities::logs ( "calling Closure for event '".$eventName."'", "messagebus.log" );	
					
					// Closures must be called directly
					$object($eventName, $data);
					continue;
				}
				
				Shineisp_Commons_Utilities::logs ( "calling callback '".$object."->".$callback."' for event '".$eventName."'", "messagebus.log" );
				
				$callableObject = new $object;
	        	$callableObject->$callback($eventName, $data);
	        }


		}
    }
}
