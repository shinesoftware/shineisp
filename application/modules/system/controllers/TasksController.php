<?php


/**
	* This procedure is executed by cronjob every 5 minutes and it will register,
	* transfer a domain name using the default registrant
	* Remember to set the points value for each domain name.
	*
	* CREATE A SYSTEM CRONJOB EACH 5 MINUTES
	*
	* Execute all the panel tasks
	* @version 1.5
*/

class System_TasksController extends Zend_Controller_Action {
	
	protected $translations;
	
	public function preDispatch() {
		$registry = Zend_Registry::getInstance ();
		$this->translations = $registry->Zend_Translate;
		
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
	}
	
	public function indexAction() {
		// Execute the Panel active tasks
		$this->panelTask();
		
		// Execute all the Domain active tasks
		$this->domainsTask();
		
		die ( 'Done' );
	}
	
	/**
	 * Execute all the registered tasks for the panel
	 */
	private function panelTask() {
		// Get 20 Active tasks items
		$tasks = PanelsActions::getTasks ( Statuses::id("active", "domains_tasks"), Statuses::id('active', 'domains') );
		try {
			// Check all the tasks saved within the Panels_Actions table. 
			foreach ( $tasks as $task ) {
				self::doPanelsTask($task);
				
	            // Check if all orderitem of order is complete and if is ok set order to complete
	            $OrderItem = OrdersItems::getDetail($task['orderitem_id']);
	            if( is_numeric($OrderItem['order_id']) && Orders::checkIfOrderItemsAreCompleted( $OrderItem['order_id'] ) ) {
	            	Shineisp_Commons_Utilities::logs ('Order #'.$OrderItem['order_id'].' has all items completed. Set order status to complete.', "tasks.log" );
	                Orders::set_status($OrderItem['order_id'], Statuses::id("complete", "orders"));
	            }
			}
		}catch (SoapFault $e){
			
		}
	}
	
	
	/**
	 * Execute the panel tasks
	 */
	private function doPanelsTask($task) {
		$ISP = ISP::getByCustomerId($task['customer_id']);
		
		if ( !$ISP || !isset($ISP['isp_id']) || !is_numeric($ISP['isp_id']) ) {		
			PanelsActions::UpdateTaskLog ( $task ['action_id'], $this->translations->translate ( 'isp_id not found' ) );
			Shineisp_Commons_Utilities::SendEmail ( $ISP['email'], $ISP['email'], null, "Task error panel message", $e->getMessage () );
			return false;
		}
		
		try {
			$customer_id = (isset($task['customer_id'])) ? $task['customer_id'] : 0;
			$ISPpanel    = ISP::getPanel($ISP['isp_id']);
			$class       = "Shineisp_Api_Panels_".$ISPpanel."_Main";
			
			// Create the class registrar object 
			$ISPclass = new $class ();
			$action   = $task ['action'];
			
			if($action == "createClient"){
				
				// Create the website plan
				$clientId = $ISPclass->create_client($task);

			}elseif($action == "createWebsite"){
				
				// Create the website plan
				$websiteID = $ISPclass->create_website($task);

				// Create the main ftp account
				$ftpID = $ISPclass->create_ftp($task, $websiteID);
				
				// Send the configuration email
				$ISPclass->sendMail($task); 
				
			}elseif($action == "createMail"){
				
				// Create the email account
				$emailID = $ISPclass->create_mail($task);
				
				// Send the configuration email
				$ISPclass->sendMail($task); 
				
			}elseif($action == "createDatabase"){
				
				// Create the database 
				$databaseID = $ISPclass->create_database($task);
				
				// Send the configuration email
				$ISPclass->sendMail($task); 
				
			}elseif($action == "fullProfile"){
				$websiteID  = $ISPclass->create_website($task);  // Create the website plan
				$ftpID      = $ISPclass->create_ftp($task, $websiteID);  // Create the main ftp account
				$emailID    = $ISPclass->create_mail($task);  // Create the email account
				$databaseID = $ISPclass->create_database($task);  // Create the database

				// Send the configuration email
				$ISPclass->sendMail($task); 
			}
			
			// Update the log description of the panel action
			PanelsActions::UpdateTaskLog ( $task ['action_id'], $this->translations->translate ( "The request has been executed correctly." ) );
			
			// Update the status of the task
			PanelsActions::UpdateTaskStatus ( $task ['action_id'], Statuses::id('complete', 'domains_tasks') ); // Set the task as "Complete"
			
		} catch (Exception $e) {
			PanelsActions::UpdateTaskLog ( $task ['action_id'], $this->translations->translate ( $e->getMessage () ) );
			Shineisp_Commons_Utilities::SendEmail ( $ISP['email'], $ISP['email'], null, "Task error panel message", $e->getMessage () );
		}
	}
	
	
	/**
	 * Execute all the registered tasks for the domain
	 */
	private function domainsTask() {

		// Get 20 Active tasks items
		$tasks = DomainsTasks::getTasks ( Statuses::id('active', 'domains_tasks'), 20 );
		
		// Check if an active registrar is active
		$registrant = Registrars::findActiveRegistrars ();
		
		// If exist a registrant set in the database
		if (isset ( $registrant [0] )) {
			
			// Check all the tasks saved within the Domains_Tasks table. 
			foreach ( $tasks as $task ) {
				
				Shineisp_Commons_Utilities::logs ( $task ['action'] . " - " . $task ['domain'], "tasks.log" );
				try {
					self::doDomainTask($task);
				} catch ( SoapFault $e ) {
					Shineisp_Commons_Utilities::logs ( $e->faultstring, "tasks.log" );
				}
			}
		}
		return true;
	}
	
	/*
	 * doTask
	 * Execute the task
	 */
	private function doDomainTask($task) {
		if ( !isset($task['Domains']) || !isset($task['Domains']['Customers']) || !isset($task['Domains']['Customers']['customer_id']) ) {
			PanelsActions::UpdateTaskLog ( $task ['action_id'], $this->translations->translate ( 'customer_id not found' ) );
			continue;
		}
		$customer_id = intval($task['Domains']['Customers']['customer_id']);
		$ISP         = ISP::getByCustomerId($customer_id);
		
		if ( !$ISP || !isset($ISP['isp_id']) || !is_numeric($ISP['isp_id']) ) {		
			PanelsActions::UpdateTaskLog ( $task ['action_id'], $this->translations->translate ( 'isp_id not found' ) );
			return false;
		}
		
		try {
			
			// Getting domains details 
			$domain = Domains::find ( $task ['domain_id'], null, true );
			
			if (! empty ( $domain [0] )) {
				
				// Get the associated registrar for the domain selected 
				$registrar = Registrars::getRegistrantId ( $task ['registrars_id'] );
				
				if (! empty ( $registrar ['class'] )) {
					
					// Create the class registrar object 
					$class    = $registrar ['class'];
					$regclass = new $class ();
					$action   = $task ['action'];
									
					// Check if the task is REGISTER or TRANSFER the domain name
					if ($action == "registerDomain") {
						
						$regclass->registerDomain ( $task ['domain_id'] );
						
						// Set the DNS ZONES
						DomainsTasks::AddTask($task ['domain'], "setDomainHosts");
												
						// Update the domain information
						DomainsTasks::AddTask($task ['domain'], "updateDomain");
					
					} elseif ($action == "transferDomain") {
						
						$regclass->transferDomain ( $task ['domain_id'] );
					
					} elseif ($action == "renewDomain") {
						
						$regclass->renewDomain ( $task ['domain_id'] );
						
						// Update the domain information
						DomainsTasks::AddTask($task ['domain'], "updateDomain");
						
					} elseif ($action == "lockDomain") {
						
						$regclass->lockDomain ( $task ['domain_id'] );
						
					} elseif ($action == "unlockDomain") {
						
						$regclass->unlockDomain ( $task ['domain_id'] );
						
						// Update the domain information
						DomainsTasks::AddTask($task ['domain'], "updateDomain");
						
					} elseif ($action == "setNameServers") {
						
						$regclass->setNameServers ( $task ['domain_id'] );
						
					} elseif ($action == "setDomainHosts") {
						
						$regclass->setDomainHosts ( $task ['domain_id'] );
						
					}else{
						$regclass->$action ( $task ['domain_id'] );
					}
					
					// Update the log description of the task
					DomainsTasks::UpdateTaskLog ( $task ['task_id'], $this->translations->translate ( "The request has been executed correctly." ) );
					
					// Update the status of the task
					DomainsTasks::UpdateTaskStatus ( $task ['task_id'], Statuses::id('complete', 'domains_tasks') ); // Set the task as "Complete"
					
					// Increment the task counter number
					DomainsTasks::UpdateTaskCounter ( $task ['task_id'] );
					
					// Set the status as Active
					Domains::setStatus ( $task ['domain_id'], Statuses::id('active', 'domains_tasks') );
				
				}
			}
		} catch ( Exception $e ) {
			DomainsTasks::UpdateTaskLog ( $task ['task_id'], $this->translations->translate ( $e->getMessage () ) );
			
			Shineisp_Commons_Utilities::SendEmail ( $ISP['email'], $ISP['email'], null, "Task error message: " . $task['domain'], $e->getMessage () );
			Shineisp_Commons_Utilities::logs ( "Task error message: " . $task['domain'] . ":" . $e->getMessage (), "tasks.log" );
		}
		
		return true;
	}
}