<?php

/**
	* This procedure is executed by cronjob every 5 minutes and it will check,
	* custom tasks set in the /application/modules/system/layout.xml file.
	*
	* CREATE A SYSTEM CRONJOB EACH 5 MINUTES
	*
	* @version 1.1
*/

class System_CronController extends Zend_Controller_Action {

	public function preDispatch() {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
	}
	
	/**
	 * Base index action
	 */
	public function indexAction() {
		$resources = Shineisp_Commons_Layout::getData ("system", null);
		
		// log the data
		Shineisp_Commons_Utilities::log("ShineISP Cron started", 'cron.log');

		// Get the cron default configuration
		$xmlobject = $resources->xpath ( "cron/execute" ) ;
		
		if (count ( $xmlobject )) {
			foreach ( $xmlobject as $cron ) {
				
				// get the crontab from the first cron 
				$crontime = (string)$cron['time'];

				// Check the crontab time set in the xml
				if($this->is_time_cron(time(), $crontime)){
					
					foreach ( $cron as $code ) {
						
						$class  = (string)$code['class'];
						$method = (string)$code['method'];
						$params = json_decode((string)$code['params']);
						$log    = (string)$code;
						
						Shineisp_Commons_Utilities::log($log, 'cron.log');

						// Check if the class exists
						if(class_exists($class)){
							
							// Check if the method exists
							if(method_exists($class, $method)){

								// Check the class
								$theclass  = new ReflectionClass($class);
								$themethod = $theclass->getMethod($method);
								$isStatic  = $themethod->isStatic();
								
								Shineisp_Commons_Utilities::log("$class::$method", 'cron.log');
								
								// Check if the method is static
								if($isStatic){
									call_user_func("$class::$method", $params);
								}else{
									$obj = new $class();
									call_user_func(array($obj, $method), $params);
								}
							}
						}
					}
				} else {
					//Shineisp_Commons_Utilities::log((string)$cron->script . " not executed", 'cron.log');
				}
			}
		}

		Shineisp_Commons_Utilities::log("ShineISP Cron ended", 'cron.log');
			
	}
	
	/**
	 * Match the crontime with the time
	 * 
	 * @param string $time
	 * @param string $crontime
	 * @return boolean
	 */
	private function is_time_cron($time , $cron) 
	{
	    $cron_parts = explode(' ' , $cron);
	    if(count($cron_parts) != 5)
	    {
	    	return false;
	    }
	    
	    list($min , $hour , $day , $mon , $week) = explode(' ' , $cron);
	    
	    $to_check = array('min' => 'i' , 'hour' => 'G' , 'day' => 'j' , 'mon' => 'n' , 'week' => 'w');
	    
	    $ranges = array(
	    	'min' => '0-59' ,
	    	'hour' => '0-23' ,
	    	'day' => '1-31' ,
	    	'mon' => '1-12' ,
	    	'week' => '0-6' ,
	    );
	    
	    foreach($to_check as $part => $c)
	    {
	    	$val = $$part;
	    	$values = array();
	    	
	    	/*
	    		For patters like 0-23/2
	    	*/
	    	if(strpos($val , '/') !== false)
	    	{
	    		//Get the range and step
	    		list($range , $steps) = explode('/' , $val);
	    		
	    		//Now get the start and stop
	    		if($range == '*')
	    		{
	    			$range = $ranges[$part];
	    		}
	    		list($start , $stop) = explode('-' , $range);
	    			
				for($i = $start ; $i <= $stop ; $i = $i + $steps)
				{
					$values[] = $i;
				}
	    	}
	    	/*
	    		For patters like :
	    		2
	    		2,5,8
	    		2-23
	    	*/
	    	else
	    	{
	    		$k = explode(',' , $val);
	    		
	    		foreach($k as $v)
	    		{
	    			if(strpos($v , '-') !== false)
	    			{
	    				list($start , $stop) = explode('-' , $v);
	    			
						for($i = $start ; $i <= $stop ; $i++)
						{
							$values[] = $i;
						}
	    			}
	    			else
	    			{
	    				$values[] = $v;
	    			}
	    		}
	    	}
	    	
	    	if ( !in_array( date($c , $time) , $values ) and (strval($val) != '*') ) 
			{
				return false;
			}
	    }
	    
	    return true;
	}
}