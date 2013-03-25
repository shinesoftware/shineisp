<?php

/**
 * Shineisp_Api_Registrars_Ovh
 * @author Shine Software
 *
 */

class Shineisp_Api_Registrars_Ovh extends Shineisp_Api_Registrars_Base implements Shineisp_Api_Registrars_Interface {
	
	/**
	 * Enumerate all the registrar actions 
	 * 
	 * @return     array       An associative array containing the list of the actions allowed by the OVH's class 
	 * @access     public
	 */
	public Function getActions() {
		// This action has been added as custom action 
		$this->actions['updateDomain'] = "Update Domain";
		return $this->actions;
	}
	
	/**
	 * Register a new domain name
	 * 
	 * Executes the 'Purchase' command on the service's servers to register a new domain.
	 * Note in order to not fail this command, it must meet the following requirements:
	 * - Your account credencials must have enough credits to cover the order amount.
	 * - The domain name must be valid and available.
	 * - Name Servers must be valid and registered.
	 * 
	 * @param      integer     $domainID     Must be a valid domain id, that is currently available
	 * @param      array       $nameServers    If not set, Service's Default name servers will be used instead.
	 * @param      bool        $regLock        A flag that specifies if the domain should be locked or not. Default is true.
	 * @return     mixed       True, or throw an Exception if failed.
	 * @access     public
	 * @see        renewDomain
	 * @see        transferDomain
	 * @see Shineisp_Api_Registrars_Interface::registerDomain()
	 */
	public function registerDomain($domainID, $nameServers = null, $regLock = true) {
		
		// Connection to the SOAP system
		$soap = $this->Connect();		
											
		if(empty($this->session)){
			throw new Exception('SOAP connection system error');
		}
		
		// Get the registrar information
		$registrar = Registrars::getActiveRegistrantbyClass(__CLASS__);		
		if(empty($registrar)){
			throw new Exception("Registrar __CLASS__ not found in database.");
		}
		
		// Get the domain information
		$domain	= Domains::find($domainID);
		  								
		if(!empty($domain[0])){
			
			$customerID = $domain[0]['customer_id'];
			
			// Get the customer information
			$customer = Customers::getAllInfo ( $customerID );
			if(empty($customer)){
				throw new Exception("Customer has been not found.");
			}
			
			// Create the OVH nic-Handle
			$nicHandle = $this->createNicHandle($customerID);	
			
			// Check if the nichandle has been created
			if(!empty($nicHandle)){
				
				// Save the nic-Handle in the database
				CustomersDomainsRegistrars::addNicHandle($customerID, $domainID, $registrar['registrars_id'], $nicHandle);
				
				$domain_name = $domain[0]['domain'] . "." . $domain[0]['DomainsTlds']['WhoisServers']['tld'];
				
				// Get the main DNS servers set in the configuration
				$dns = $this->getDnsServers();
				
				$locale = Zend_Registry::get('Zend_Locale');
				$birthdate = new Zend_Date($customer ['birthdate'], "yyyy-MM-dd HH:mm:ss", $locale);
				
				// OVH handle two kind of registration for the domains it and the others
				if ($domain[0]['DomainsTlds']['WhoisServers']['tld'] == "it") {
					
					$soap->resellerDomainCreateIT ( $this->session['id'], 
													$domain_name, 
													'none', 
													'gold', 
													'agent', 
													'yes', 
													$nicHandle, 
													$registrar ['username'], 
													$registrar ['username'], 
													$registrar ['username'], 
													!empty($dns[0]) ? $dns[0] : null, 
													!empty($dns[1]) ? $dns[1] : null,
													!empty($dns[2]) ? $dns[2] : null,
													!empty($dns[3]) ? $dns[3] : null,
													null, 
													$customer ['firstname'], 
													$customer ['lastname'], 
													$customer ['taxpayernumber'], 
													$customer ['vat'], 
													$birthdate->get('dd/MM/yyyy'), 
													$customer ['birthplace'], 
													$customer ['birthdistrict'], 
													$customer ['birthcountry'], 
													$customer ['birthnationality'], 
													$registrar ['testmode'] );			
						
				} elseif($domain[0]['DomainsTlds']['WhoisServers']['tld'] == "fr") {
					/*
						stringsession : the session id
					    stringdomain : the domain name
					    stringhosting : the hosting type (none|start1m|perso|pro|business|premium)
					    stringoffer : the domain offer (deprecated, please leave empty)
					    stringprofile : the reseller profile (none | whiteLabel | agent)
					    stringowo : activate OwO for .com, .net, .org, .info and .biz (yes | no)
					    stringowner : the owner nichandle
					    stringadmin : the admin nichandle
					    stringtech : the tech nichandle
					    stringbilling : the billing nichandle
					    stringdns1 : the primary dns hostname (if hosting, default OVH dns will be installed)
					    stringdns2 : the secondary dns hostname
					    stringdns3 : the third dns hostname
					    stringdns4 : the fourth dns hostname
					    stringdns5 : the fifth dns hostname
					    stringmethod : only for .fr (AFNIC) : identification method (siren | inpi | birthPlace | afnicIdent)
					    stringlegalName : only for .fr (AFNIC) : corporation name /trademark owner
					    stringlegalNumber : only for .fr (AFNIC) : SIREN/SIRET/INPI number
					    stringafnicIdent : only for .fr (AFNIC) : afnic ident code
					    stringbirthDate : only for .fr (AFNIC) : owner birth date
					    stringbirthCity : only for .fr (AFNIC) : owner birth city
					    stringbirthDepartement : only for .fr (AFNIC) : owner birth french departement
					    stringbirthCountry : only for .fr (AFNIC) : owner bith country
					    booleandryRun : enable the TEST MODE when enabled (true), will not debit your account
					*/
					
					$soap->resellerDomainCreate ( $this->session['id'],
													$domain_name,
													'none',
													'gold',
													'agent',
													'yes',
													$nicHandle,
													$registrar ['username'],
													$registrar ['username'],
													$registrar ['username'],
													!empty($dns[0]) ? $dns[0] : null,
													!empty($dns[1]) ? $dns[1] : null,
													!empty($dns[2]) ? $dns[2] : null,
													!empty($dns[3]) ? $dns[3] : null,
													null, 
													'birthPlace', 
													$customer ['firstname'] . " " . $customer ['lastname'], 
													$customer ['vat'], 
													null, 
													$birthdate->get('dd/MM/yyyy'), 
													$customer ['birthplace'], 
													$customer ['birthdistrict'], 
													$customer ['birthcountry'], 
													$customer ['birthnationality'], 

													$registrar ['testmode'] );
				} else {
	
					$soap->resellerDomainCreate ( $this->session['id'], 
												  $domain_name, 
												  'none', 
												  'gold', 
												  'agent', 
												  'yes', 
												  $nicHandle, 
												  $registrar ['username'], 
												  $registrar ['username'], 
												  $registrar ['username'], 
												  !empty($dns[0]) ? $dns[0] : null, 
												  !empty($dns[1]) ? $dns[1] : null,
												  !empty($dns[2]) ? $dns[2] : null,
												  !empty($dns[3]) ? $dns[3] : null, 
												  null, null, null, null, null, null, null, null, null, 
												  $registrar ['testmode'] );
				}
				
				return true;
			}
		}
		return false;
	}
	
	/**
	 * Transfer a domain name
	 * 
	 * Executes the 'Purchase' command on the service's servers to transfer the domain.
	 * Note in order to not fail this command, it must meet the following requirements:
	 * - Your account credencials must have enough credits to cover the order amount.
	 * - To transfer EPP names, the query must include the authorization key from the Registrar.
	 * - Name Servers must be valid and registered.
	 * 
	 * @param      integer     $domainID     Must be a valid domain id, that is currently available
	 * @param      integer     $customerID   Customer Identify Code.
	 * @param      array       $nameServers    If not set, Service's Default name servers will be used instead.
	 * @param      bool        $regLock        A flag that specifies if the domain should be locked or not. Default is true.
	 * @return     mixed       True, or throw an Exception if failed.
	 * @access     public
	 * @see        renewDomain
	 * @see        registerDomain
	 */
	public function transferDomain($domainID, $nameServers = null, $regLock = true) {
	
		// Connection to the SOAP system
		$soap = $this->Connect();											
		if(empty($this->session)){
			throw new Exception('SOAP connection system error');
		}
		
		// Get the registrar information
		$registrar = Registrars::getActiveRegistrantbyClass(__CLASS__);		
		if(empty($registrar)){
			throw new Exception("Registrar __CLASS__ not found in database.");
		}
		
		// Get the domain information
		$domain	= Domains::find($domainID);
		  								
		if(!empty($domain[0])){
			
			$customerID = $domain[0]['customer_id'];
			
			// Get the customer information
			$customer = Customers::getAllInfo ( $domain[0]['customer_id'] );
			if(empty($customer)){
				throw new Exception("Customer has been not found.");
			}
						
			// Create the OVH nic-Handle
			$nicHandle = $this->createNicHandle($customerID);	
			
			// Check if the nichandle has been created
			if(!empty($nicHandle)){
				
				// Save the nic-Handle in the database
				CustomersDomainsRegistrars::addNicHandle($customerID, $domainID, $registrar['registrars_id'], $nicHandle);
				
				$domain_name = $domain[0]['domain'] . "." . $domain[0]['DomainsTlds']['WhoisServers']['tld'];
	
				// Get the main DNS servers set in the configuration
				$dns = $this->getDnsServers();
				
				$locale = Zend_Registry::get('Zend_Locale');
				$birthdate = new Zend_Date($customer ['birthdate'], "yyyy-MM-dd HH:mm:ss", $locale);
				
				// OVH handle two kind of registration for the domains it and the others
				if ($domain[0]['DomainsTlds']['WhoisServers']['tld'] == "it") {
					$soap->resellerDomainTransferIT ( $this->session['id'], 
													$domain_name, 
													$domain[0] ['authinfocode'], 
													'none', 
													'gold', 
													'agent', 
													true, 
													$nicHandle, 
												    $registrar ['username'], 
												    $registrar ['username'], 
												    $registrar ['username'],
													!empty($dns[0]) ? $dns[0] : null, 
													!empty($dns[1]) ? $dns[1] : null,
													!empty($dns[2]) ? $dns[2] : null,
													!empty($dns[3]) ? $dns[3] : null, 
													null, 
													$customer ['firstname'], 
													$customer ['lastname'], 
													$customer ['taxpayernumber'], 
													$customer ['vat'], 
													$customer->get('dd/MM/yyyy'), 
													$customer ['birthplace'], 
													$customer ['birthdistrict'], 
													$customer ['birthcountry'], 
													$customer ['birthnationality'], 													
													$registrar ['testmode'] );					
				}else{
					$soap->resellerDomainTransfer ( $this->session['id'], 
													$domain_name, 
													$domain[0] ['authinfocode'], 
													'none', 
													'gold', 
													'agent', 
													true, 
													$nicHandle, 
												    $registrar ['username'], 
												    $registrar ['username'], 
												    $registrar ['username'],
													!empty($dns[0]) ? $dns[0] : null, 
													!empty($dns[1]) ? $dns[1] : null,
													!empty($dns[2]) ? $dns[2] : null,
													!empty($dns[3]) ? $dns[3] : null, 
													null, 
													null, 
													null, 
													null, 
													null, 
													null, 
													null, 
													null, 
													null, 
													$registrar ['testmode'] );
													
				}
				
				return true;
			}
		}
		return false;
	}
	
	/**
	 * Renew a domain name that belongs to your Registrar account
	 * 
	 * Executes the 'Extend' command on OVH's servers to renew a domain name which was previously registered or transfered to your Registrar account.
	 * Note that this command to not fail, it must meet the following requirements:
	 * - Your registrar account must have enough credits to cover the order amount.
	 * - The domain name must be valid and active and belongs to your registrar account.
	 * - The new expiration date cannot be more than 10 years in the future.
	 * 
	 * @param      integer      $domainID   Domain code identifier
	 * @return     long        Renewal Order ID, or false if failed.
	 * @access     public
	 * @see        registerDomain
	 * @see        transferDomain
	 */
	public Function renewDomain($domainID) {
		
		// Connection to the SOAP system
		$soap = $this->Connect();											
		if(empty($this->session)){
			throw new Exception('SOAP connection system error');
		}
		
		// Get the registrar information
		$registrar = Registrars::getActiveRegistrantbyClass(__CLASS__);		
		if(empty($registrar)){
			throw new Exception("Registrar __CLASS__ not found in database.");
		}
		
		// Get the domain information
		$domain	= Domains::find($domainID);

		if(!empty($domain[0])){
			$domain_name = $domain[0]['domain'] . "." . $domain[0]['DomainsTlds']['WhoisServers']['tld'];
			$soap->resellerDomainRenew ( $this->session['id'], $domain_name, $registrar ['testmode'] );
			return true;
		}
		
		return false;
	}
	
	/**
	 * Check domain availability
	 * 
	 * Executes the 'Check' command on OVH's servers to check domain availability.
	 * 
	 * @param      string     $domain   Domain name
	 * @return     boolean    An associative array containing the domain name as a key and a bool 
	 * 						  (true if domain is available, false otherwise) as a value. On error, it returns false
	 * @access     public
	 */
	public Function checkDomain($domain) {
		// Connection to the SOAP system
		$soap = $this->Connect();											
		if(empty($this->session)){
			throw new Exception('SOAP connection system error');
		}
		
		if(!empty($domain)){
			$result = $soap->domainCheck ( $this->session['id'], $domain);
			if($result[0]->value == 1){
				return true;
			}
		}
		
		return false;
	}
	
	/**
	 * Set registrar lock status for a domain name
	 * 
	 * Executes the 'SetRegLock' command on OVH's servers.
	 * 
	 * @param      integer     $domainID   Domain code identifier
	 * @return     bool        True if not locked, false otherwise. You should check for $this->isError if returned false, to make sure it's not an error flag not the registrar lock status.
	 * @access     public
	 * @see        unlockDomain
	 */
	public Function lockDomain($domainID) {

		// Connection to the SOAP system
		$soap = $this->Connect();											
		if(empty($this->session)){
			throw new Exception('SOAP connection system error');
		}
		
		// Get the domain information
		$domain	= Domains::find($domainID);
		  								
		if(!empty($domain[0])){
			$domain_name = $domain[0]['domain'] . "." . $domain[0]['DomainsTlds']['WhoisServers']['tld'];
			$soap->domainLock ( $this->session['id'], $domain_name);
			return true;
		}
		
		return false;	
	}
	
	/**
	 * Set registrar unlock status for a domain name
	 * 
	 * Executes the 'SetRegUnlock' command on OVH's servers.
	 * 
	 * @param      integer     $domainID   Domain code identifier
	 * @return     bool        True if not locked, false otherwise. You should check for $this->isError if returned false, to make sure it's not an error flag not the registrar lock status.
	 * @access     public
	 * @see        lockDomain
	 */
	public Function unlockDomain($domainID) {
		
		// Connection to the SOAP system
		$soap = $this->Connect();											
		if(empty($this->session)){
			throw new Exception('SOAP connection system error');
		}
		
		// Get the domain information
		$domain	= Domains::find($domainID);
		  								
		if(!empty($domain[0])){
			$domain_name = $domain[0]['domain'] . "." . $domain[0]['DomainsTlds']['WhoisServers']['tld'];
			$soap->domainUnlock ( $this->session['id'], $domain_name);
			return true;
		}
		
		return false;		
	}
	
	/**
	 * Set name servers for a domain name.
	 * 
	 * Executes the 'ModifyNS' command on OVH's servers, to set the name servers
	 * for a domain name that is active and belongs to your Registrar account.
	 * 
	 * @param      integer     $domainID   Domain code identifier
	 * @param      array       $nameservers        Array containing name servers. If not set, default Registrar name servers will be used.
	 * @return     bool        True if succeed and false if failed.
	 * @access     public
	 * @see        getNameServers
	 */
	function setNameServers($domainID, $nameServers = null) {
	
	}
	
	/**
	 * Get name servers for a domain name.
	 * 
	 * Executes the 'GetDNS' command on OVH's servers, to retrive the name servers
	 * for a domain name that is active and belongs to your Registrar account.
	 * 
	 * @param      integer     $domainID   Domain code identifier
	 * @return     array       An array containing name servers. If using OVH's name servers, the array will be empty.
	 * @access     public
	 * @see        setNameServers
	 */
	function getNameServers($domainID) {
	
	}
	
	
	/**
	 * Set domain hosts (records) for a domain name.
	 * 
	 * Executes the '...' command on Registrar's servers, to set domain hosts (records)
	 * for a domain name that is active and belongs to your OVH account.
	 * 
	 * @param      integer     $domainID   Domain code identifier
	 * @return     bool        True if succeed and False if failed.
	 * @access     public
	 * @see        getDomainHosts
	 */
	function setDomainHosts($domainID){
		$zonesrows = "";
		$nserver = "";
		
		// Connection to the SOAP system
		$soap = $this->Connect();											
		if(empty($this->session)){
			throw new Exception('SOAP connection system error');
		}

		// Get the domain information
		$domain	= Domains::find($domainID);
		
		if(!empty($domain[0])){
			$domain_name = $domain[0]['domain'] . "." . $domain[0]['DomainsTlds']['WhoisServers']['tld'];
			$customer = Customers::find($domain[0]['customer_id']);
		}else{
		    throw new Exception('Domain information has not found.');
		}
		
		// Get the customer dns set in his control panel
		$NS = Dns_Zones::getCustomNameServers($domainID);

		// Get the default domain set
		$NSDefault = $soap->domainInfo ( $this->session['id'], $domain_name );
		
		// START NAMESERVER MANAGEMENT
		if(!empty($NS)){
		    // Get the client nameserver set in the shineisp control panel
			foreach ($NS as $ns){
				$nameservers[] = $ns['target'];
			}  
		}else{
		    // Get the domain nameservers set in OVH
			if(!empty($NSDefault->dns[0]) && !empty($NSDefault->dns[1])){
			    $nameservers[] = $NSDefault->dns[0]->name . ".";
			    $nameservers[] = $NSDefault->dns[1]->name . ".";
			}else{
			    // Get the common domain dns zones information set in shineisp preferences
			    $NSDefault = Servers::getDnsserver();
			    foreach ($NSDefault as $ns){
					$nameservers[] = $ns['host'] . "." . $ns['domain'] . ".";
				}
			}
		}
		
		// Create the NS records
	    foreach ($nameservers as $ns){
            $nameserver = array();
            $nameserver[] = "";
            $nameserver[] = " IN ";
            $nameserver[] = " NS "; 
            $nameserver[] = $ns;
            $nserver .= implode("\t", $nameserver) . "\n";
        }
        
		// END NAMESERVER MANAGEMENT
		
		if(!empty($nameservers[0])){
			$zoneTemplate = "\$TTL 86400\n@   IN SOA " . $nameservers[0] . " tech.ovh.net. (2011022503 86400 3600 3600000 86400)\n";
			
			// Get the domain dns zones information
			$dnsZones = Dns_Zones::getZones($domainID);

			// Create the DNS Zones records
			if(!empty($dnsZones)){
				foreach ($dnsZones as $zone){
					if($zone['fieldtype'] != "NS"){ // Exclude the NS because already included above
						$zones = array();
						$zones[] = $zone['subdomain'];
						$zones[] = " IN ";
						$zones[] = $zone['fieldtype']; 
						$zones[] = $zone['target'];
						$zonesrows .= implode("\t", $zones) . "\n";
					}
				}
				$zones = array();
				$zone = $zoneTemplate . $nserver . $zonesrows;
			}else{
				$webservers = Servers::getWebserver ();
				$mailservers = Servers::getMailserver ();
				  
				// Set Web server zone
				$zones[] = "www\tIN\tCNAME\t" . $domain_name . ".";
				if (! empty ( $webservers ['ip'] )) {
					$zones[] = "\tIN\tA\t" . $webservers['ip'];  
				}
				
				// Set mail zone
				$zones[] = "\tIN\tMX 1\tmail." . $domain_name . ".";
				if (isset ( $mailservers  )) {
					$zones[] = "mail\tIN\tA\t" . $mailservers['ip'];
				} else {
					$zones[] = "mail\tIN\tA\t" . $webservers['ip'];
				}
				
				$zonesrows = implode("\n", $zones) . "\n";
				$zone = $zoneTemplate . $nserver . $zonesrows;
			}
			
			// Reset of the Zone dns
			$soap->dnsReset ( $this->session['id'], $domain_name, 'REDIRECT', true );
			
			// Import the DNS Custom Zone
			$soap->zoneImport ( $this->session['id'], $domain_name, $zone );
			
			// Send the confirmation email
			$isp = Isp::getActiveISP ();
			$retval = Shineisp_Commons_Utilities::getEmailTemplate ( 'dns_changed' );
			if ($retval) {
				$subject = str_replace ( "[domain]", $domain_name, $retval ['subject'] );
				$body = str_replace ( "[domain]", $domain_name, $retval ['template'] );
				if (!empty($customer ['parent_id'])) {
					$reseller = Customers::get_by_customerid($customer ['parent_id']);
					if(!empty($reseller[0])){
						$email = $reseller[0]['email'];
					}else{
						$email = $customer[0]['email'];
					}
				}else{
					$email = $customer['email'];
				}
				Shineisp_Commons_Utilities::SendEmail ( $isp ['email'], $email, $isp ['email'], $subject, $body );
			}
			
		}	
		return false;
	}	
	
	/**
	 * Get domain hosts (records) for a domain name.
	 * 
	 * Executes the '...' command on Registrar's servers, to get domain hosts (records)
	 * for a domain name that is active and belongs to your OVH account.
	 * 
	 * @param      integer     $domainID   Domain code identifier
	 * @return     bool        True if succeed and False if failed.
	 * @access     public
	 * @see        getDomainHosts
	 */
	function getDomainHosts($domainID){
		try {
			// Connection to the SOAP system
			$soap = $this->Connect();											
			if(empty($this->session)){
				throw new Exception('SOAP connection system error');
			}
	
			// Get the domain information
			$domain	= Domains::find($domainID);
			if(!empty($domain[0])){
				$domain_name = $domain[0]['domain'] . "." . $domain[0]['DomainsTlds']['WhoisServers']['tld'];
			}
			
			$dnszone = array();
			
			$zones = $soap->zoneEntryList ( $this->session['id'], $domain_name );
			
			if (is_array ( $zones )) {
				$i = 0;
				foreach ( $zones as $zone ) {
					$dnszone [$i] ['subdomain'] = $zone->subdomain;
					$dnszone [$i] ['fieldtype'] = $zone->fieldtype;
					$dnszone [$i] ['target'] = $zone->target;
					$i ++;
				}
				
				return Dns_Zones::saveZone($domainID, $dnszone);
			}
			
			return false;
			
		} catch (Exception $e) {
			throw new Exception($e->getMessage());
		}
	}		

	############################################## OVH Custom Functions ##############################################

	
	/**
	 * Connect into the remote OVH webservice 
	 * 
	 * Executes the 'login' command on OVH's servers, to retrive the session variable
	 * for execute the commands.
	 * 
	 * @return     string       Session variable
	 * @access     private
	 */	
	private function Connect(){
		$registrar = Registrars::getActiveRegistrantbyClass(__CLASS__);
		try {
			if(!empty($registrar)){
				$soap = new SoapClient ( $registrar ['wsdl'] );
				$this->session['id'] = $soap->login ($registrar ['username'], $registrar ['password'], "en", false );
				return $soap;
			}
		} catch (Exception $e) {

			throw new Exception($e->getMessage());

		}
	}


	/**
	 * updateDomain
	 * Update the domain information
	 * @param unknown_type $customerID
	 */
	public function updateDomain($domainID){

		// Connection to the SOAP system
		$soap = $this->Connect();											
		if(empty($this->session)){
			throw new Exception('SOAP connection system error');
		}
		
		// Get the domain information
		$domain	= Domains::find($domainID);
		if(!empty($domain[0])){
			$domain_name = $domain[0]['domain'] . "." . $domain[0]['DomainsTlds']['WhoisServers']['tld'];
			try {
	
				$retval = $soap->domainInfo ( $this->session['id'], $domain_name );
				
				$info ['domain'] = $retval->domain;
				$info ['creation'] = $retval->creation;
				$info ['modification'] = $retval->modification;
				$info ['expiration'] = $retval->expiration;
				$info ['nichandle'] = $retval->nicowner;
				$info ['authinfocode'] = $retval->authinfo;
				
				return Domains::updateDomain($domainID, $info);
				
			} catch (Exception $e) {
				throw new Exception($e->getMessage());
			}
		}
		
		throw new Exception('Domain ID not found.');
	}
	
	
	/**
	 * Create a Nic-Handle for the client 
	 * 
	 * Executes the 'login' command on OVH's servers, to retrive the session variable
	 * for execute the commands.
	 * 
	 *   Parameters
	 *   ===========
	 *   string session : the session id
	 *   string name : the contact name
	 *   string firstname : the contact firstname
	 *   string sex : the contact sex (M/F)
	 *   string password : the contact password
	 *   string email : the contact email
	 *   string phone : the contact phone number (international format, ex: +33.899701761)
	 *   string fax : the contact fax number
	 *   string address : the contact address
	 *   string city : the contact city
	 *   string area : the contact area
	 *   string zip : the contact zip code
	 *   string country : the contact country (be|fr|pl|es|lu|ch|de|...)
	 *   string language : the contact language (fr|en|pl|es|de)
	 *   boolean isOwner : is it an owner nic ? default false
	 *   string legalform : the contact legalform (corporation|individual|association|other)
	 *   string organisation : organisation name
	 *   string legalName : the contact legalname
	 *   string legalNumber : the contact legalnumber (SIRET/SIREN/...)
	 *   string vat : the contact vat
	 *   string birthDay : the contact birthDay
	 *   string birthCity : the contact birth city
	 *   string nationalIdentificationNumber : the contact fiscal code or company vat
	 *   string companyNationalIdentificationNumber : the contact vat
	 *   string corporationType : the contact vat (s.a.s.|s.n.c.|s.r.l.|s.p.a.|s.a.p.a.|s.c.a.r.l.|individuale)
	 * 
	 * 
	 * @param      integer      $customerID		Code identifier
	 * @return     string       $nicHandle		the new contact handle id
	 * @access     private
	 */		
	private function createNicHandle($customerID){
		$soap = $this->Connect();
		
		if(!empty($this->session)){
			$fields = "c.customer_id as customer_id, c.company as company, c.firstname as firstname, c.lastname as lastname, c.sex as sex, c.email as email, c.password as password, c.birthdate as birthdate, c.birthplace as birthplace, c.taxpayernumber as taxpayernumber, c.vat as vat, c.note as note,  a.address as address, a.code as code, a.city as city, a.area as area, ct.name as country, ct.code as countrycode, cts.type_id as type_id, cts.name as companytype, l.legalform_id as legalform_id, l.name as legalform, s.status_id as status_id, s.status as status, cn.contact as contact";
			$customer = Customers::getAllInfo($customerID, $fields);

			return $soap->nicCreateIT ( 
							$this->session['id'], // Session
							$customer ['lastname'], // Lastname
							$customer ['firstname'], // Firstname 
							$customer ['sex'], // Sex
							Shineisp_Commons_Utilities::GenerateRandomString(), // Password
							$customer ['email'], // Email
							$customer ['contact'], // Phone
							null, // Fax
							$customer ['address'], // Address
							$customer ['city'], // City
							$customer ['area'], // Area
							$customer ['code'], // Zip
							strtolower ( $customer ['countrycode'] ), // Country Code
							"en", // Language 
							true, // isOwner
							$customer ['legalform'], // Legalform
							$customer ['company'], // Organisation
							$customer ['firstname'] . " " . $customer ['lastname'], // Legal name
							null, // Legal Number
							$customer ['vat'], // VAT or IVA
							Shineisp_Commons_Utilities::formatDateOut ( $customer ['birthdate'] ), // Birthday
							$customer ['birthplace'], // Birthcity
							$customer ['taxpayernumber'], // Contact fiscal code or company vat
							$customer ['vat'], // Company National Identification Number
							$customer ['companytype'] ); // Corporation Type
		}
		
		return false;
	}
	
	
}