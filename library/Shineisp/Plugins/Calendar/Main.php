<?php
/**
 * Calendar Sync
 *
 * @author Michelangelo Turillo
 * @author Shine Software Italy [http://www.shinesoftware.com]
 * @version 1.0 
 */

require_once 'src/Google_Client.php';
require_once 'src/contrib/Google_CalendarService.php';

class Shineisp_Plugins_Calendar_Main implements Shineisp_Plugins_Interface  {

	const CACERT_SOURCE_SYSTEM = 0;
	const CACERT_SOURCE_FILE = 1;
	const CACERT_SOURCE_DIR = 2;
	
	protected static $calendar = false;
	protected static $loggedIn = false;
	protected static $cookies = array ();
	
	public $events;

	
	/**
	 * Events Registration
	 * 
	 * (non-PHPdoc)
	 * @see Shineisp_Plugins_Interface::events()
	 */

	public function events()

	{
		$em = Shineisp_Registry::get('em');

		if (!$this->events && is_object($em)) {
// 			$em->attach('invoices_pdf_created', array(__CLASS__, 'listener_invoice_upload'), 100);
// 			$em->attach('orders_pdf_created', array(__CLASS__, 'listener_order_upload'), 100);
		}

		return $em;

	}
	
	/**
	 * Constructor of the class
	 *
	 * @param $email string       	
	 * @param $password string|null       	
	 * @throws Exception
	 */
	public function __construct() {
		
	}
	
	/**
	 * Event Listener
	 * This event is triggered when the Invoice PDF is created 
	 */
// 	public static function listener_invoice_upload($event) {
// 		$invoice = $event->getParam('invoice');
// 		$file = $event->getParam('file');
		
// 		if(is_numeric($invoice['invoice_id'])){

// 			if(self::isReady()){
				
// 				// get the destination path
// 				$destinationPath = Settings::findbyParam('calendar_invoicesdestinationpath');
				
// 				self::execute($file, $destinationPath, $invoice['invoice_date'] . " - " . $invoice['number'] . ".pdf", $invoice['invoice_date']);
				
// 				Shineisp_Commons_Utilities::log("Event triggered: invoices_pdf_created", "plugin_calendar.log");
             
// 			}

// 		}

// 		return false;
// 	}
	
	/**
	 * Event Listener
	 * This event is triggered when the Orders PDF is created 
	 */
// 	public static function listener_order_upload($event) {
// 		$file = $event->getParam('file');

// 		if(self::isReady()){
				
// 			// get the destination path
// 			$destinationPath = Settings::findbyParam('calendar_ordersdestinationpath');
			
// 			self::execute($file, $destinationPath);
			
// 			Shineisp_Commons_Utilities::log("Event triggered: orders_pdf_created", "plugin_calendar.log");

// 		}

// 		return false;
// 	}
	
	/**
	 * Execute the upload of the file to the calendar service
	 * 
	 * @param string $sourcefile
	 * @return boolean
	 */
	public static function newEvent($summary, $location, $description=null, $datestart=null, $dateend=null){
        
        try{
            if(empty($datestart)){
                $datestart = date('Y-m-d');
            }
            
            if(empty($dateend)){
                $dateend = date('Y-m-d');
            }
            
            $calendar = Settings::findbyParam ( 'calendar_calendarid' );
            
            if($calendar){
                $cal = self::$calendar;
                 
                if(is_object($cal) && !empty($cal)){
                    $event = new Google_Event();
                    $event->setSummary($summary);          // Event name
                    $event->setLocation($location);            // Event location
                    $start = new Google_EventDateTime();
                    $start->setDate($datestart);
                    $event->setStart($start);
                    $end = new Google_EventDateTime();
                    $end->setDate($dateend);
                    $event->setEnd($end);
                    $event->setDescription($description);
                    $createdEvent = $cal->events->insert($calendar, $event);
                    Shineisp_Commons_Utilities::log("The Event has been created!", "plugin_calendar.log");
                    return true;
                }
            }
            
            return false;
            
        }catch(Exception $e){
            return $e->getMessage();
        }
	}
	
	/**
	 * Check if the user has set the credentials in the administration panel
	 */
	public static function isReady() {
		$clientid = Settings::findbyParam ( 'calendar_clientid' );
		$clientsecret = Settings::findbyParam ( 'calendar_clientsecret' );
		$redirecturi = Settings::findbyParam ( 'calendar_redirecturi' );
		$developerkey = Settings::findbyParam ( 'calendar_developerkey' );
		$developerkey = Settings::findbyParam ( 'calendar_calendarid' );
		
		if (! empty ( $clientid ) && ! empty ( $clientsecret ) && ! empty ( $redirecturi ) && ! empty ( $developerkey )) {
			return true;
		}
		
		Shineisp_Commons_Utilities::log("Calendar module: No credentials found", "plugin_calendar.log");
		return false;
	}
	
	/**
	 * Login in the calendar account
	 * 
	 * @return boolean
	 * @throws Exception
	 */
	protected static function login() {
	    try{
            
            $client = new Google_Client();
            $client->setApplicationName("ShineISP Google Calendar Application");
            
            $clientid = Settings::findbyParam ( 'calendar_clientid' );
            $clientsecret = Settings::findbyParam ( 'calendar_clientsecret' );
            $redirecturi = Settings::findbyParam ( 'calendar_redirecturi' );
            $developerkey = Settings::findbyParam ( 'calendar_developerkey' );
            
            $client->setClientId($clientid);
            $client->setClientSecret($clientsecret);
            $client->setRedirectUri($redirecturi);
            $client->setDeveloperKey($developerkey);
            
            $cal = new Google_CalendarService($client);
            if (isset($_GET['logout'])) {
                unset($_SESSION['token']);
            }
            
            if (isset($_GET['code'])) {
                $client->authenticate($_GET['code']);
                $_SESSION['token'] = $client->getAccessToken();
                header('Location: http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF']);
            }
            
            if (isset($_SESSION['token'])) {
                $client->setAccessToken($_SESSION['token']);
            }
            
            if ($client->getAccessToken()) {
                self::$loggedIn = true;
                self::$calendar = $cal;
                return $cal;
            } else {
              $authUrl = $client->createAuthUrl();
              return $authUrl;
            }
        
		}catch(Exception $e){
            Shineisp_Commons_Utilities::log($e->getMessage(), "plugin_calendar.log");
        }
	}
	
	/**
	 * Get the google calendars from the google calendar identity
	 */
	public static function getCalendars(){
	    $data = array();
	    
	    $cal = self::login();
	    if(is_string($cal)){
	        echo "<a class='btn btn-info' href='$cal'>Connect Me!</a>";
	    }
	     
	    if(is_object($cal) && !empty($cal)){
    	    $calList = $cal->calendarList->listCalendarList();
    	   
    	    foreach ($calList['items'] as $calendar){
    	        $data[$calendar['id']] = $calendar['summary'];
    	    }
	    }
	    return $data;
	}

}
