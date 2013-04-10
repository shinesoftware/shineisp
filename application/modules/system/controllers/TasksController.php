<?php

/**
 * BatchController
 * Manage the isp profile
 * @version 1.0
 */

class System_TasksController extends Zend_Controller_Action {
	
	protected $translations;
	
	public function preDispatch() {
		$registry = Zend_Registry::getInstance ();
		$this->translations = $registry->Zend_Translate;
		$this->getHelper ( 'layout' )->setLayout ( 'system' );
	}
	
	/*
	 * This procedure is executed by cronjob every 10 minutes and it will register, transfer a domain name using the default registrant
	 * Remember to set the points value for each domain name.
	 * 
	 * Execute all the panel tasks
	 */
	public function indexAction() {
		// Execute the Panel active tasks
		$this->panelTask();
		
		// Execute all the Domain active tasks
		//$this->domainsTask();
		
		die ( 'Done' );
	}
	
	/**
	 * Execute all the registered tasks for the panel
	 */
	private function panelTask() {
		// Get 20 Active tasks items
		$tasks = PanelsActions::getTasks ( Statuses::id("active", "domains_tasks"), Statuses::id('active', 'domains') );
		try {
			// Check all the tasks saved within the Domains_Tasks table. 
			foreach ( $tasks as $task ) {
				self::doPanelsTask($task);
			}
		}catch (SoapFault $e){
			
		}
	}
	
	
	/**
	 * Execute the panel tasks
	 */
	private function doPanelsTask($task) {
		
		try {
			$isp = Isp::getActiveISP ();
			$ISPpanel = Isp::getPanel();
			$class = "Shineisp_Api_Panels_".$ISPpanel."_Main";
			
			// Create the class registrar object 
			$ISPclass = new $class ();
			$action = $task ['action'];
			
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
				
				$websiteID = $ISPclass->create_website($task);  // Create the website plan
				$ftpID = $ISPclass->create_ftp($task, $websiteID);  // Create the main ftp account
				$emailID = $ISPclass->create_mail($task);  // Create the email account
				$databaseID = $ISPclass->create_database($task);  // Create the database

				// Send the configuration email
				$ISPclass->sendMail($task); 
			}
			
			// Update the log description of the panel action
			PanelsActions::UpdateTaskLog ( $task ['action_id'], $this->translations->translate ( "The request has been executed correctly." ) );
			
			// Update the status of the task
			PanelsActions::UpdateTaskStatus ( $task ['action_id'], Statuses::id('complete', 'domains_tasks') ); // Set the task as "Complete"
			
		}catch (Exception $e){
			PanelsActions::UpdateTaskLog ( $task ['action_id'], $this->translations->translate ( $e->getMessage () ) );
			
			$isp = Isp::getActiveISP ();
			Shineisp_Commons_Utilities::SendEmail ( $isp ['email'], $isp ['email'], null, "Task error panel message", $e->getMessage () );
		}
	}
	
	
	/**
	 * Execute all the registered tasks for the domain
	 */
	private function domainsTask() {

		// Get 20 Active tasks items
		$tasks = DomainsTasks::getTasks ( Statuses::id('active', 'domains_tasks'), 20 );
		
		// Get the active ISP in order to register/transfer the domains
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
		
		try {
			
			// Getting domains details 
			$domain = Domains::find ( $task ['domain_id'], null, true );
			
			if (! empty ( $domain [0] )) {
				
				// Get the associated registrar for the domain selected 
				$registrar = Registrars::getRegistrantId ( $task ['registrars_id'] );
				
				if (! empty ( $registrar ['class'] )) {
					
					// Create the class registrar object 
					$class = $registrar ['class'];
					$regclass = new $class ();
					$action = $task ['action'];
					
					// Check if the task is REGISTER or TRANSFER the domain name
					if ($action == "registerDomain") {
						
						$regclass->registerDomain ( $task ['domain_id'] );
						
						// Set the DNS ZONES
						DomainsTasks::AddTask($task ['domain'], "setDomainHosts");
												
						// Update the domain information
						DomainsTasks::AddTask($task ['domain'], "updateDomain");
					
					} elseif ($action == "transferDomain") {
						
						$regclass->transferDomain ( $task ['domain_id'] );
						
						// Update the domain information
						DomainsTasks::AddTask($task ['domain'], "updateDomain");
					
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
			
			$isp = Isp::getActiveISP ();
			Shineisp_Commons_Utilities::SendEmail ( $isp ['email'], $isp ['email'], null, "Task error message: " . $task['domain'], $e->getMessage () );
			Shineisp_Commons_Utilities::logs ( "Task error message: " . $task['domain'] . ":" . $e->getMessage (), "tasks.log" );
		}
		
		return true;
	}
}