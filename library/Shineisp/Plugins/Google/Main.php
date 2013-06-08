<?php

/**
 * 
 * @link http://ga-dev-tools.appspot.com/explorer/
 * @author shinesoftware
 *
 */
class Shineisp_Plugins_Google_Main implements Shineisp_Plugins_Interface {

	protected static $service;
	public $events;
	
	/**
	 * Do the login
	 * @param string $email
	 * @param string $password
	 */
	private static function login($email, $password){
		try{
			$client = Zend_Gdata_ClientLogin::getHttpClient($email, $password, Zend_Gdata_Analytics::AUTH_SERVICE_NAME);
			self::$service = new Zend_Gdata_Analytics($client, "ShineSoftware-ShineISP-1.0");
			return true;
		}catch(Exception $e){
			Shineisp_Commons_Utilities::logs ($e->getMessage(), "shineisp.log" );
		}
	}
	
	/**
	 * Events Registration
	 *
	 * (non-PHPdoc)
	 * @see Shineisp_Plugins_Interface::events()
	 */
	public function events()
	{
		$em = Zend_Registry::get('em');
		return $em;
	}	
	
	/**
	 * get the New and Return Visitors
	 * 
	 * @param string $profileId
	 */
	public static function getNewReturnVisitors($fromdate, $todate){
		try{

			$email = Settings::findbyParam('ganalytics_email');
			$password = Settings::findbyParam('ganalytics_password');
			$profileId = Settings::findbyParam('ganalytics_profileid');

			// Check the mandatory parameters
			if(empty($email) || empty($password) || empty($profileId)){
				return array();
			}
			
			// Login to the google analytics service
			if(empty(self::$service)){
				self::login(Settings::findbyParam('ganalytics_email'), Settings::findbyParam('ganalytics_password'));
			}
			
			if(!Shineisp_Commons_Utilities::isDate($fromdate)){
				throw new Exception('Start date is not a correct date');
			}
			
			if (!Shineisp_Commons_Utilities::isDate($todate)){
				throw new Exception('End date is not a correct date');
			}
			
			if(self::$service){
				$query = self::$service->newDataQuery()
							  ->setProfileId($profileId)
							  ->addMetric(Zend_Gdata_Analytics_DataQuery::METRIC_VISITORS) 
							  ->addDimension(Zend_Gdata_Analytics_DataQuery::DIMENSION_VISITOR_TYPE) 
							  ->addSort(Zend_Gdata_Analytics_DataQuery::METRIC_VISITORS, true)
							  ->setStartDate($fromdate) 
							  ->setEndDate($todate)
							  ->setMaxResults(10); 
				
				$result = self::$service->getDataFeed($query);
				
				$i = 0;
				foreach($result as $row){
					list($code, $title) = explode("=", $row->getTitleValue());
					$data[$i]['title'] = $title;
					$data[$i]['visitors'] = (string)$row->getMetric('ga:visitors');
					$i++;
				}
				
				return $data;
			}else{
				Shineisp_Commons_Utilities::logs ("Google Analytics service not available", "shineisp.log" );
				return array();
			}
		}catch(Exception $e){
			Shineisp_Commons_Utilities::logs ($e->getMessage(), "shineisp.log" );
		}
	}
}
