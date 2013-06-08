<?php

/*
 * Shineisp_Plugins_Registrars_Interface
* -------------------------------------------------------------
* Type:     Interface class
* Name:     Shineisp_Plugins_Registrars_Interface
* Purpose:  Registrars Interface Class
* -------------------------------------------------------------
*/

interface Shineisp_Plugins_Registrars_Interface {
	
	/**
	 * Enumerate all the registrar actions 
	 * 
	 * @return     array       An associative array containing the list of the actions allowed by the registrar's class 
	 * @access     public
	 */
	public Function getActions();
	
	/**
	 * Register a new domain name
	 * 
	 * Executes the '...' command on the service's servers to register a new domain.
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
	 */
	public Function registerDomain($domainID, $nameServers = null, $regLock = true);
	
	/**
	 * Transfer a domain name
	 * 
	 * Executes the '...' command on the service's servers to transfer the domain.
	 * Note in order to not fail this command, it must meet the following requirements:
	 * - Your account credencials must have enough credits to cover the order amount.
	 * - To transfer EPP names, the query must include the authorization key from the Registrar.
	 * - Name Servers must be valid and registered.
	 * 
	 * @param      integer     $domainID     Must be a valid domain id, that is currently available
	 * @param      array       $nameServers    If not set, Service's Default name servers will be used instead.
	 * @param      bool        $regLock        A flag that specifies if the domain should be locked or not. Default is true.
	 * @return     mixed       True, or throw an Exception if failed.
	 * @access     public
	 * @see        renewDomain
	 * @see        registerDomain
	 */
	public Function transferDomain($domainID, $nameServers = null, $regLock = true);
	
	/**
	 * Renew a domain name that belongs to your Registrar account
	 * 
	 * Executes the '...' command on Registrar's servers to renew a domain name which was previously registered or transfered to your Registrar account.
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
	public Function renewDomain($domainID);
	
	/**
	 * Check domain availability
	 * 
	 * Executes the '...' command on Enom's servers to check domain availability.
	 * 
	 * @param      string     $domain   Domain name
	 * @return     boolean    An associative array containing the domain name as a key and a bool 
	 * 						  (true if domain is available, false otherwise) as a value. On error, it returns false
	 * @access     public
	 */
	public Function checkDomain($domain);
	
	/**
	 * Set registrar lock status for a domain name
	 * 
	 * Executes the '...' command on Registrar's servers.
	 * 
	 * @param      integer     $domainID   Domain code identifier
	 * @return     bool        True if not locked, false otherwise. You should check for $this->isError if returned false, to make sure it's not an error flag not the registrar lock status.
	 * @access     public
	 * @see        unlockDomain
	 */
	public Function lockDomain($domainID);
	
	/**
	 * Set registrar unlock status for a domain name
	 * 
	 * Executes the '...' command on Registrar's servers.
	 * 
	 * @param      integer     $domainID   Domain code identifier
	 * @return     bool        True if not locked, false otherwise. You should check for $this->isError if returned false, to make sure it's not an error flag not the registrar lock status.
	 * @access     public
	 * @see        lockDomain
	 */
	public Function unlockDomain($domainID);
	
	/**
	 * Set name servers for a domain name.
	 * 
	 * Executes the '...' command on Registrar's servers, to set the name servers
	 * for a domain name that is active and belongs to your Registrar account.
	 * 
	 * @param      integer     $domainID   Domain code identifier
	 * @param      array       $nameservers        Array containing name servers. If not set, default Registrar name servers will be used.
	 * @return     bool        True if succeed and false if failed.
	 * @access     public
	 * @see        getNameServers
	 */
	function setNameServers($domainID, $nameServers = null);
	
	  /**
	  * Set domain hosts (records) for a domain name.
	  * 
	  * Executes the '...' command on Enom's servers, to set domain hosts (records)
	  * for a domain name that is active and belongs to your Enom account.
	  * 
	  * @param      string      $domainName         Must be active and belongs to your Enom account.
	  * @param      array       $domainHosts        Associative array containing all Hosts to set. Array keys are name, type, address and pref.
	  * @return     bool        True if succeed and False if failed.
	  * @access     public
	  * @see        getDomainHosts
	  */
	function getNameServers($domainID);
	
	/**
	 * Set domain hosts (records) for a domain name.
	 * 
	 * Executes the '...' command on Registrar's servers, to set domain hosts (records)
	 * for a domain name that is active and belongs to your Enom account.
	 * 
	 * @param      integer     $domainID   Domain code identifier
	 * @return     bool        True if succeed and False if failed.
	 * @access     public
	 * @see        getDomainHosts
	 */
	function setDomainHosts($domainID);	
	
	/**
	 * Get domain hosts (records) for a domain name.
	 * 
	 * Executes the '...' command on Registrar's servers, to set domain hosts (records)
	 * for a domain name that is active and belongs to your Enom account.
	 * 
	 * @param      integer     $domainID   Domain code identifier
	 * @return     bool        True if succeed and False if failed.
	 * @access     public
	 * @see        getDomainHosts
	 */
	function getDomainHosts($domainID);
		
}