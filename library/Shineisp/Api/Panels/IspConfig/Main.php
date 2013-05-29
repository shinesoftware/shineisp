<?php

/**
 * 
 * ISPConfig Management Module for ShineISP 
 * @author shinesoftware
 *
 */

class Shineisp_Api_Panels_Ispconfig_Main extends Shineisp_Api_Panels_Base implements Shineisp_Api_Panels_Interface {
	

	/**
	 * Class constructor
	 */
	public function __construct() {
		$this->setName ( "IspConfig" );
		$this->setPath ( PUBLIC_PATH . "/../library/Shineisp/Api/Panels/IspConfig" );
	}

	/**
	 * Create a new email account 
	 * 
	 * Executes the creation of new email account in the IspConfig control panel
	 * Note in order to not fail this command, it must meet the following requirements:
	 * 
	 * - The customer must be registered in the db.
	 * - The Web domain must be registered in IspConfig 
	 * 
	 * @param      array       $task     Must be a valid task 
	 * @return     mixed       True, or throw an Exception if failed.
	 * @access     public
	 */
	public function create_mail(array $task) {
		$emailUserID = false;
		
		$clientId = self::get_client_id($task);
		
		// Connection to the SOAP system
		$client = $this->connect ();
				
		// Get the service details
		$service = OrdersItems::getAllInfo($task['orderitem_id']);
		
		if(!empty($service)){
			$server_group_id = (isset($service['Products']) && isset($service['Products']['server_group_id'])) ? intval($service['Products']['server_group_id']) : 0;
			
			// Get the Json encoded parameters in the task
			$parameters = json_decode ( $task ['parameters'], true );
			
			if(empty($parameters)){
				throw new Exception("No parameters found in the service", 3501);
			}
			
			// Get the mail server setup
			$server = Servers::getServerFromGroup($server_group_id, 'mail');

			// Get the server id
			if(!empty($server['server_id']) && is_numeric($server['server_id'])){

				// Get the remote server ID set in the servers profile in ShineISP
				$customAttribute = CustomAttributes::getAttribute($server['server_id'], "remote_server_id");

				// Get the remote server id set in ShineISP
				if(is_numeric($customAttribute['value'])){
					$ServerId = $customAttribute['value'];
					
					// Get the domain set in the orderitems bought from the customer
					$domains = OrdersItemsDomains::get_domains($task['orderitem_id']);
					
					// For each domain bought in the service ...
					foreach ($domains as $domain) {
						
						$email = 'info@' . $domain['domain'];
						
						// Create a random password string
						$password = Shineisp_Commons_Utilities::GenerateRandomString();
						
						$params = array(
								'server_id' => $ServerId,
								'domain' => $domain['domain'],
								'active' => 'y');
						
						try{
							// Get the domain name ID
							$record = $client->mail_domain_get_by_domain($this->getSession(), $domain['domain']);

							// Create the mail domain
							if(empty($record['domain_id'])){
								$domainId = $client->mail_domain_add($this->getSession(), $clientId, $params);
							}else{
								$domainId = $record['domain_id'];
							}
							
						} catch ( SoapFault $e ) {
							throw new Exception("There was a problem with the Mail Domain creation: " . $e->getMessage() . " - " . __METHOD__ . " - Paramenters: " . json_encode($params) , "3506");
						}
						
						$params = array(
								'server_id' => $ServerId,
								'email' => $email,
								'login' => $email,
								'password' => $password,
								'uid' => 5000,
								'gid' => 5000,
								'maildir' => '/var/vmail/' . $domain['domain'] . "/info",
								'quota' => !empty($parameters['mailquota']) && is_numeric($parameters['mailquota']) && ($parameters['mailquota'] > 0) ? $parameters['mailquota'] : "-1",
								'cc' => '',
								'homedir' => '',
								'autoresponder' => 'n',
								'autoresponder_start_date' => '',
								'autoresponder_end_date' => '',
								'autoresponder_text' => '',
								'move_junk' => 'n',
								'custom_mailfilter' => '',
								'postfix' => 'y',
								'access' => 'n',
								'disableimap' => 'n',
								'disablepop3' => 'n',
								'disabledeliver' => 'n',
								'disablesmtp' => 'n');

						
						try{
							// Create the mailbox
							$emailUserID = $client->mail_user_add($this->getSession(), $clientId, $params);
						
						} catch ( SoapFault $e ) {
							throw new Exception("There was a problem with Email account creation: " . $e->getMessage() . " - " . __METHOD__ . " - Paramenters: " . json_encode($params) , "3506");
						}
						
						// Save the setup in the service setup field
						OrdersItems::set_setup($task ['orderitem_id'], array('username'=>$email, 'password'=>$password), "emails");
					
						// Add relation between order_item and server
						OrdersItemsServers::addServer($task['orderitem_id'], $server['server_id']);
					
						// Create the log message
						Shineisp_Commons_Utilities::logs ("ID: " . $task ['action_id'] .  " - " . __METHOD__ . " - Paramenters: " . json_encode($params), "ispconfig.log" );
					}
					
				}else{
					throw new Exception("No remote mail server id set in the ShineISP server profile.", "3502");
				}
			}else{
				throw new Exception("Mail Server has not been found in IspConfig server settings.", "3501");
			}
		}
	
		// Logout from the IspConfig Remote System
		$client->logout($this->getSession ());
		
		return $emailUserID;
		
	}
	
	/**
	 * Create a new database 
	 * 
	 * Executes the creation of new mysql database in the IspConfig control panel
	 * Note in order to not fail this command, it must meet the following requirements:
	 * 
	 * - The customer must be registered in the db.
	 * - The Web domain must be registered in IspConfig 
	 * 
	 * @param      array       $task     Must be a valid task 
	 * @return     mixed       True, or throw an Exception if failed.
	 * @access     public
	 */
	public function create_database(array $task) {
		
		$clientId = self::get_client_id($task);
		
		// Connection to the SOAP system
		$client = $this->connect ();
		
		// Get the Json encoded parameters in the task
		$parameters = json_decode ( $task ['parameters'], true );
		if(empty($parameters)){
			throw new Exception("No parameters found in the service", 3501);
		}

		// Get the service details
		$service = OrdersItems::getAllInfo($task['orderitem_id']);
		if ( !$service ) {
			return false;
		}
		$server_group_id = (isset($service['Products']) && isset($service['Products']['server_group_id'])) ? intval($service['Products']['server_group_id']) : 0;
		$server = Servers::getServerFromGroup($server_group_id, 'database');		
				

		// Get the server id
		if(!empty($server['server_id']) && is_numeric($server['server_id'])){

			// Get the remote server ID set in the servers profile in ShineISP
			$customAttribute = CustomAttributes::getAttribute($server['server_id'], "remote_server_id");

			// Get the remote server id set in ShineISP
			if(is_numeric($customAttribute['value'])){
				$ServerId = $customAttribute['value'];
				
				// Get all the customer information
				$customer = Customers::getAllInfo ( $task ['customer_id'] );
				$id = rand(1, 100);
				
				// Create the username string for instance from John Doe to jdoe
				$username = strtolower(substr($customer ['firstname'], 0, 1) . preg_replace("#[^a-zA-Z0-9]*#", "", $customer ['lastname']));
				$dbuser = $username . "_u$id";
				$dbname = $username . "_db$id";
				
				// Create a random password string
				$password = Shineisp_Commons_Utilities::GenerateRandomString();

				// Create a database user
				$params = array(
						'server_id' => $ServerId,
						'database_user' => $dbuser,
						'database_password' => $password
				);
				
				$dbUserId = $client->sites_database_user_add($this->getSession(), $clientId, $params);
				if($dbUserId){
					// Create the database
					$params = array(
							'server_id' => $ServerId,
							'type' => 'mysql',
							'database_name' => $dbname,
							'database_user_id' => $dbUserId,
							'database_ro_user_id' => '0',
							'database_charset' => 'utf8',
							'remote_access' => 'y',
							'remote_ips' => '',
							'backup_interval' => 'none',
							'backup_copies' => 1,
							'active' => 'y'
					);
						
					$databaseID = $client->sites_database_add($this->getSession(), $clientId, $params);
					
					// Save the setup in the service setup field
					OrdersItems::set_setup($task ['orderitem_id'], array('db'=>$dbname, 'username'=>$dbuser, 'password'=>$password), "database");
					
					// Add relation between order_item and server
					OrdersItemsServers::addServer($task['orderitem_id'], $server['server_id']);
					
					// Create the log message
					Shineisp_Commons_Utilities::logs ("ID: " . $task ['action_id'] .  " - " . __METHOD__ . " - Paramenters: " . json_encode($params), "ispconfig.log" );
				}else{
					throw new Exception("Database User ID has not been found.", "3503");
				}
			}else{
				throw new Exception("Database Server has not been found in IspConfig server settings.", "3501");
			}
		}else{
			throw new Exception("No remote database server id set in the ShineISP server profile.", "3502");
		}
		
		// Logout from the IspConfig Remote System
		$client->logout($this->getSession ());
		
		return $databaseID;
		
	}
	
	/**
	 * Create a new ftp account
	 * 
	 * Executes the creation of new ftp account in the IspConfig control panel
	 * Note in order to not fail this command, it must meet the following requirements:
	 * 
	 * - The customer must be registered in the db.
	 * - The Web domain must be registered in IspConfig 
	 * 
	 * @param      array       $task     Must be a valid task 
	 * @param      integer     $websiteID     Must be a valid websiteID 
	 * @return     mixed       True, or throw an Exception if failed.
	 * @access     public
	 */
	public function create_ftp(array $task, $websiteID) {
		$clientId = self::get_client_id($task);
		
		// Connection to the SOAP system
		$client = $this->connect ();

		// Get the service details
		$service = OrdersItems::getAllInfo($task['orderitem_id']);
		if ( !$service ) {
			return false;
		}
		$server_group_id = (isset($service['Products']) && isset($service['Products']['server_group_id'])) ? intval($service['Products']['server_group_id']) : 0;
		$server = Servers::getServerFromGroup($server_group_id, 'web');		

		// Get the server id
		if(!empty($server['server_id']) && is_numeric($server['server_id'])){

			// Get the remote server ID set in the servers profile in ShineISP
			$customAttribute = CustomAttributes::getAttribute($server['server_id'], "remote_server_id");

			// Get the remote server id set in ShineISP
			if(is_numeric($customAttribute['value'])){
				$ServerId = $customAttribute['value'];
		
				// Get the Json encoded parameters in the task
				$parameters = json_decode ( $task ['parameters'], true );
	
				if(empty($parameters)){
					throw new Exception("No parameters found in the service", 3501);
				}
				
				// Get all the customer information
				$customer = Customers::getAllInfo ( $task ['customer_id'] );
				
				$id = rand(1, 100);
				
				// Create the username string for instance from John Doe to jdoe
				$username = strtolower(substr($customer ['firstname'], 0, 1) . preg_replace("#[^a-zA-Z0-9]*#", "", $customer ['lastname']));;
				$username .= "_ftp$id"; 
				
				// Create a random password string
				$password = Shineisp_Commons_Utilities::GenerateRandomPassword();
				
				$params = array(
						'server_id'        => 1,
						'parent_domain_id' => $websiteID,
						'username'         => $username,
						'password'         => $password,
						'quota_size'       => $parameters['webspace'],
						'active'           => 'y',
						'uid'              => 'web' . $websiteID,
						'gid'              => 'client' . $clientId,
						'dir'              => '/var/www/clients/client' .$clientId. '/web' . $websiteID,
						'quota_files'      => -1,
						'ul_ratio'         => -1,
						'dl_ratio'         => 200,
						'ul_bandwidth'     => -1,
						'dl_bandwidth'     => 100);
					
				try{
					$ftpUserID = $client->sites_ftp_user_add($this->getSession(), $clientId, $params);
				
				} catch ( SoapFault $e ) {
					throw new Exception("There was a problem with FTP account creation: " . $e->getMessage() , "3506");
				}
				
				// Save the setup in the service setup field
				OrdersItems::set_setup($task ['orderitem_id'], array('host'=>$server['ip'], 'username'=>$username, 'password'=>$password), "ftp");
				
				// Create the log message
				Shineisp_Commons_Utilities::logs ("ID: " . $task ['action_id'] .  " - " . __METHOD__ . " - Paramenters: " . json_encode($params), "ispconfig.log" );
				
			}else{
				throw new Exception("No remote web server id set in the ShineISP server profile.", "3502");
			}
		}else{
			throw new Exception("Web Server has not been found in IspConfig server settings.", "3501");
		}
		
		// Logout from the IspConfig Remote System
		$client->logout($this->getSession ());
		
		return $ftpUserID;
		
	}
	
	/**
	 * Create a new website
	 * 
	 * Executes the creation of new website in the IspConfig control panel
	 * Note in order to not fail this command, it must meet the following requirements:
	 * 
	 * - The customer must be registered in the db.
	 * - The parameters must be saved in the service detail (orderitems table)
	 * 
	 * @param      array      $task     Must be a valid task 
	 * @return     mixed       True, or throw an Exception if failed.
	 * @access     public
	 */
	public function create_website(array $task) {
		$params = array();
		
		$clientId = self::get_client_id($task);
		
		// Connection to the SOAP system
		$client = $this->connect ();
		
		// Get the service details
		$service = OrdersItems::getAllInfo($task['orderitem_id']);
		if ( !$service ) {
			return false;
		}
		$server_group_id = (isset($service['Products']) && isset($service['Products']['server_group_id'])) ? intval($service['Products']['server_group_id']) : 0;
		$server = Servers::getServerFromGroup($server_group_id, 'web');		


		// Get the server id
		if(!empty($server['server_id']) && is_numeric($server['server_id'])){

			// Get the remote server ID set in the servers profile in ShineISP
			$customAttribute = CustomAttributes::getAttribute($server['server_id'], "remote_server_id");

			// Get the remote server id set in ShineISP
			if(is_numeric($customAttribute['value'])){
				$ServerId = $customAttribute['value'];
			
				// Get the Json encoded parameters in the task
				$parameters = json_decode ( $task ['parameters'], true );
	
				// Get the domain
				$domains = OrdersItemsDomains::get_domains($task['orderitem_id']);
				
				if(!empty($domains[0]['domain'])){
	
					$params = array(
							'server_id' => $ServerId,
							'ip_address' => '*',
							'domain' => $domains[0]['domain'],
							'type' => 'vhost',
							'parent_domain_id' => 0,
							'vhost_type' => 'name',
							'hd_quota' => $parameters['webspace'],
							'traffic_quota' => $parameters['bandwidth'],
							'cgi' => 'n',
							'ssi' => 'n',
							'suexec' => 'y',
							'errordocs' => 1,
							'is_subdomainwww' => 1,
							'subdomain' => 'www',
							'php' => 'fast-cgi',
							'ruby' => 'n',
							'redirect_type' => '',
							'redirect_path' => '',
							'ssl' => 'n',
							'ssl_state' => '',
							'ssl_locality' => '',
							'ssl_organisation' => '',
							'ssl_organisation_unit' => '',
							'ssl_country' => '',
							'ssl_domain' => '',
							'ssl_request' => '',
							'ssl_cert' => '',
							'ssl_bundle' => '',
							'ssl_action' => '',
							'stats_password' => '',
							'stats_type' => 'webalizer',
							'allow_override' =>'All',
							'apache_directives' => '',
							'php_open_basedir' => '/',
					 		'custom_php_ini' =>'',
							'backup_interval' => '',
							'backup_copies' => 1,
							'active' => 'y',
							'traffic_quota_lock' => 'n',
							'pm_process_idle_timeout' => '10',
							'pm_max_requests' => '0',
						);
					
					try{
						
						$websiteId = $client->sites_web_domain_add($this->getSession(), $clientId, $params, $readonly = false);
						
						if(!is_numeric($websiteId)){
							throw new Exception("There was a problem with website creation: sites_web_domain_add doesn't return the websiteID identifier", "3505");
						}
						
					} catch ( SoapFault $e ) {
						throw new Exception("There was a problem with " . $domains[0]['domain'] . " website creation: " . $e->getMessage() . " - Paramenters: " . json_encode($params) , "3504");
					}
					
					// Add relation between order_item and server
					OrdersItemsServers::addServer($task['orderitem_id'], $server['server_id']);
					
					// Create the log message
					Shineisp_Commons_Utilities::logs ("ID: " . $task ['action_id'] .  " - " . __METHOD__ . " - Paramenters: " . json_encode($params), "ispconfig.log" );
				}else{
					throw new Exception("No domain set for the selected service in the ShineISP service profile detail ID #: " . $task ['orderitem_id'], "3503");
				}
			}else{
				throw new Exception("No remote web server id set in the ShineISP server profile.", "3502");
			}
		}else{
			throw new Exception("Web Server has not been found in IspConfig server settings.", "3501");
		}
		
		// Logout from the IspConfig Remote System
		$client->logout($this->getSession ());
		
		return $websiteId;
		
	}
	
	/**
	 * Create a new client
	 * 
	 * Executes the creation of new client in the IspConfig control panel
	 * Note in order to not fail this command, it must meet the following requirements:
	 * 
	 * - The customer must be registered in the db.
	 * - The customer has bought a hosting plan
	 * 
	 * @param      array      $task     Must be a valid task 
	 * @return     integer    RemoteClientId
	 * @access     public
	 */
	public function create_client(array $task) {
		
		if(empty($task)){
			throw new Exception('Task empty.', '3000');
		}
				
		// Connection to the SOAP system
		$client = $this->connect ();

		if(!$client){
			throw new Exception("There is no way to connect the client with the IspConfig Panel.", "3010");
		}
		
		// Get all the customer information
		$customer = Customers::getAllInfo ( $task ['customer_id'] );

		// Get the client id saved previously in the customer information page
		$customAttribute = CustomAttributes::getAttribute($task['customer_id'], 'client_id');
		
		// Get the custom ISPConfig attribute set in the customer control panel 
		if (is_numeric($customAttribute['value'])) {

			/**
			 * Client_id (IspConfig Attribute Set in ShineISP database in the setup of the panel)
			 * @see Shineisp_Controller_Plugin_SetupcPanelsModules
			 */ 
			$clientId = $customAttribute['value'];
			$record = $client->client_get ( $this->getSession (), $clientId );
			if ($record == false) {
				$clientId = "";
			}
		}
		
		
		// Customer Profile
		$record ['company_name'] = $customer ['company'];
		$record ['contact_name'] = $customer ['firstname'] . " " . $customer ['lastname'];
		$record ['customer_no']  = $customer ['customer_id'];
		$record ['vat_id']       = $customer ['vat'];
		$record ['email']        = $customer ['email'];
		$record ['street']       = ! empty ( $customer ['Addresses'] [0] ['address'] ) ? $customer ['Addresses'] [0] ['address'] : "";
		$record ['zip']          = ! empty ( $customer ['Addresses'] [0] ['code'] ) ? $customer ['Addresses'] [0] ['code'] : "";
		$record ['city']         = ! empty ( $customer ['Addresses'] [0] ['city'] ) ? $customer ['Addresses'] [0] ['city'] : "";
		$record ['state']        = ! empty ( $customer ['Addresses'] [0] ['area'] ) ? $customer ['Addresses'] [0] ['area'] : "";
		$record ['country']      = ! empty ( $customer ['Addresses'] [0] ['Countries'] ['code'] ) ? $customer ['Addresses'] [0] ['Countries'] ['code'] : "";
		$record ['mobile']       = Contacts::getContact ( $customer ['customer_id'], "Mobile" );
		$record ['fax']          = Contacts::getContact ( $customer ['customer_id'], "Fax" );
		$record ['telephone']    = Contacts::getContact ( $customer ['customer_id'] );
		
		// System Configuration
		$languagecode = Languages::get_code($customer ['language_id']);
		$record ['language']            = $languagecode;
		$record ['usertheme']           = "default";
		$record ['template_master']     = 0;
		$record ['template_additional'] = "";
		$record ['created_at']          = 0;
		
		// Get the Json encoded parameters in the task
		$parameters = json_decode ( $task ['parameters'], true );
		
		// Match all the ShineISP product system attribute and IspConfig attributes (see below about info)
		$retval = self::matchFieldsValues ( $parameters, $record );

		if (is_array ( $retval )) {
			$record = array_merge ( $record, $retval );
		}

		// Execute the SOAP action
		if (! empty ( $clientId ) && is_numeric($clientId)) {
			$client->client_update ( $this->getSession (), $clientId, 1, $record );
		} else {
			
			// Get the service details
			$service = OrdersItems::getAllInfo($task['orderitem_id']);
			if ( !$service ) {
				return false;
			}
			$server_group_id = (isset($service['Products']) && isset($service['Products']['server_group_id'])) ? intval($service['Products']['server_group_id']) : 0;
			$server = Servers::getServerFromGroup($server_group_id, 'web');		
			
			$arrUsernames = array();
			$arrUsernames = self::generateUsernames($customer);
			
			// Check if username is available
			foreach ( $arrUsernames as $username ) {
				if ( ! $client->client_get_by_username($this->getSession (), $username) ) {
					break;	
				}	
			}
			
			// Create a random password string
			$password = Shineisp_Commons_Utilities::GenerateRandomString();
			
			$record ['username'] = $username;
			$record ['password'] = $password;
			
			// Save the setup in the service setup field
			OrdersItems::set_setup($task ['orderitem_id'], array('url' => 'http://' . $server['ip'] . ':8080', 'username'=>$username, 'password'=>$password), "webpanel");
			
			// Adding the client in ISPConfig
			$clientId = $client->client_add ( $this->getSession (), 0, $record );
			
			// Update the custom customer attribute client_id
			CustomAttributes::saveElementsValues ( array (array ('client_id' => $clientId ) ), $task ['customer_id'], "customers" );
			
		}
		
		// Create the log message
		Shineisp_Commons_Utilities::logs ("ID: " . $task ['action_id'] .  " - " . __METHOD__ . " - Paramenters: " . json_encode($record), "ispconfig.log" );
		
		// Logout from the IspConfig Remote System
		$client->logout($this->getSession ());
	
		return $clientId;
			
	}
	
	############################################## ISPCONFIG STANDARD METHODS ##############################################
	

	/**
	 * Connect into the remote ISPCONFIG webservice 
	 * 
	 * Executes the 'login' command on ISPCONFIG's servers, to retrive the session variable
	 * for execute the commands.
	 * 
	 * @return     string       Session variable
	 * @access     private
	 */
	private function connect() {
		
		// Get parameters saved in the database
		$endpointlocation = Settings::findbyParam ( "ispconfig_endpointlocation", "admin", Isp::getActiveISPID () );
		$endpointuri = Settings::findbyParam ( "ispconfig_endpointuri", "admin", Isp::getActiveISPID () );
		$username = Settings::findbyParam ( "ispconfig_username", "admin", Isp::getActiveISPID () );
		$password = Settings::findbyParam ( "ispconfig_password", "admin", Isp::getActiveISPID () );
		
		try {
			if (! empty ( $endpointlocation ) && ! empty ( $endpointuri ) && ! empty ( $username ) && ! empty ( $password )) {
				$client = new SoapClient ( null, array ('location' => $endpointlocation, 'uri' => $endpointuri, 'trace' => 1, 'exceptions' => 1 ) );
				$this->setSession ( $client->login ( $username, $password ) );
				
				return $client;
			} else {
				throw new Exception ( "ISPConfig: " . __FUNCTION__ . " - Connection error. Check the credentials" );
			}
		} catch ( Exception $e ) {
			throw new Exception ( "ISPConfig: " . __FUNCTION__ . " - Connection error. " . $e->getMessage () . " - Check the credentials");
		}
		
		return false;
	}
	
	
	/**
	 * Match all the product attribute fields and IspConfig fields
	 * 
	 * 
	 * Match all the system product attribute from ShineISP  
	 * and the IspConfig fields set in the configuration file
	 * located in the /library/Shineisp/Api/Panels/IspConfig/config.xml
	 * 
	 * @param array $attributes --> ShineISP System Product Attribute
	 * @param array $record 	--> IspConfig Client Record 
	 * @return ArrayObject
	 */
	private function matchFieldsValues(array $attributes, array $record = array()) {
		$fields = array ();

		// Loop of system product attributes
		foreach ( $attributes as $attribute => $value ) {
			// Get the saved system attribute
			$sysAttribute = ProductsAttributes::getAttributebyCode($attribute);
			
			if(!empty($sysAttribute[0]['system_var'])){
				$sysVariable = $sysAttribute[0]['system_var'];
				// Get the system product attribute
				$modAttribute = Panels::getXmlFieldbyAttribute ( "IspConfig", $sysVariable );

				if(!empty($modAttribute ['field'])){
					// Sum the old resource value with the new ones
					if(!empty($record[$modAttribute ['field']])){
						
						/**
						 * Now we have to sum the resource previously added
						 * in the client IspConfig profile with the new one 
						 */ 
						if($modAttribute ['type'] == "integer"){
							if($record[$modAttribute ['field']] == "-1"){
								$value = "-1";
							}else{
								$value += $record[$modAttribute ['field']];
							}
						} 
					}
								
					if (! empty ( $value )) {
						$fields [$modAttribute ['field']] = $value;
					} else {
						$fields [$modAttribute ['field']] = $modAttribute ['default'];
					}
				}
			}
		}

		return $fields;
	}

	/**
	 * Check the client and register it
	 * @param unknown_type $task
	 */
	private function get_client_id(array $task) {
		// Connection to the SOAP system
		$client = $this->connect ();
		
		if(!$client){
			throw new Exception("There is no way to connect the client with the IspConfig Panel.", "3010");
		}
		
		// Get the client id saved previously in the customer information page
		$customAttribute = CustomAttributes::getAttribute($task['customer_id'], 'client_id');
		
		// Get the custom ISPConfig attribute set in the customer control panel 
		if (is_numeric($customAttribute['value'])) {

			/**
			 * Client_id (IspConfig Attribute Set in ShineISP database in the setup of the panel)
			 * @see Shineisp_Controller_Plugin_SetupcPanelsModules
			 */ 
			$clientId = $customAttribute['value'];
			
			// Check the existence of the clientId in the IspConfig panel
			$record = $client->client_get ( $this->getSession (), $clientId );
			
			if ($record == false) {
				
				// If it is not present create the client first
				return self::create_client($task);

			}else{
				return $clientId;
			}
			
		}elseif (empty($customAttribute['value'])){
			
			// If it is not present create the client first
			return self::create_client($task);
			
		}	
		
		// Logout from the IspConfig Remote System
		$client->logout($this->getSession ());
		
		// Get the client id saved previously in the customer information page
		$customAttribute = CustomAttributes::getAttribute($task['customer_id'], 'client_id');
		
		if(empty($customAttribute['value'])){
			throw new Exception("There is no way to add the client in IspConfig Panel.", "3006");
		}
		
		return $customAttribute['value'];
		
	}	
	
}
