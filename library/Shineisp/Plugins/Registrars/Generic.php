<?php

/**
 * Shineisp_Plugins_Registrars_Generic
 * @author Shine Software
 *
 */

class Shineisp_Plugins_Registrars_Generic extends Shineisp_Plugins_Registrars_Base implements Shineisp_Plugins_Registrars_Interface {
	
	/**
	 * Enumerate all the registrar actions 
	 * 
	 * @return     array       An associative array containing the list of the actions allowed by the registrar's class 
	 * @access     public
	 */
	public Function getActions(){
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
	 * @see Shineisp_Plugins_Registrars_Interface::registerDomain()
	 */
	public function registerDomain($domainID, $nameServers = null, $regLock = true) {
	
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
	 * @param      array       $nameServers    If not set, Service's Default name servers will be used instead.
	 * @param      bool        $regLock        A flag that specifies if the domain should be locked or not. Default is true.
	 * @return     mixed       True, or throw an Exception if failed.
	 * @access     public
	 * @see        renewDomain
	 * @see        registerDomain
	 */
	public function transferDomain($domainID, $nameServers = null, $regLock = true) {
	
	}
	
	/**
	 * Renew a domain name that belongs to your Registrar account
	 * 
	 * Executes the 'Extend' command on Registrar's servers to renew a domain name which was previously registered or transfered to your Registrar account.
	 * Note that this command to not fail, it must meet the following requirements:
	 * - Your registrar account must have enough credits to cover the order amount.
	 * - The domain name must be valid and active and belongs to your registrar account.
	 * - The new expiration date cannot be more than 10 years in the future.
	 * 
	 * @param      string      $domainName     Must be a valid and active domain name.
	 * @param      int         $numYears       The new expiration date cannot be more than 10 years in the future.
	 * @return     long        Renewal Order ID, or false if failed.
	 * @access     public
	 * @see        registerDomain
	 * @see        transferDomain
	 */
	public Function renewDomain($domainID) {
	
	}
	
	/**
	 * Check domain availability
	 * 
	 * Executes the 'Check' command on Enom's servers to check domain availability.
	 * 
	 * @param      integer     $domainID   Domain code identifier
	 * @return     array       An associative array containing the domain name as a key and a bool 
	 * (true if domain is available, false otherwise) as a value. On error, it returns false
	 * @access     public
	 */
	public Function checkDomain($domainID) {
	
	}
	
	/**
	 * Set registrar lock status for a domain name
	 * 
	 * Executes the 'SetRegLock' command on Registrar's servers.
	 * 
	 * @param      integer     $domainID   Domain code identifier
	 * @return     bool        True if not locked, false otherwise. You should check for $this->isError if returned false, to make sure it's not an error flag not the registrar lock status.
	 * @access     public
	 * @see        unlockDomain
	 */
	public Function lockDomain($domainID) {
	
	}
	
	/**
	 * Set registrar unlock status for a domain name
	 * 
	 * Executes the 'SetRegUnlock' command on Registrar's servers.
	 * 
	 * @param      integer     $domainID   Domain code identifier
	 * @return     bool        True if not locked, false otherwise. You should check for $this->isError if returned false, to make sure it's not an error flag not the registrar lock status.
	 * @access     public
	 * @see        lockDomain
	 */
	public Function unlockDomain($domainID) {
	
	}
	
	/**
	 * Set name servers for a domain name.
	 * 
	 * Executes the 'ModifyNS' command on Registrar's servers, to set the name servers
	 * for a domain name that is active and belongs to your Registrar account.
	 * 
	 * @param      integer     $domainID   Domain code identifier
	 * @param      array       $nameservers        Array containing name servers. If not set, default Registrar name servers will be used.
	 * @return     bool        True if succeed and false if failed.
	 * @access     public
	 * @see        getNameServers
	 */
	function setNameServers($domainID, $nameServers = null){
		
	}
	
	/**
	 * Get name servers for a domain name.
	 * 
	 * Executes the 'GetDNS' command on Enom's servers, to retrive the name servers
	 * for a domain name that is active and belongs to your Registrar account.
	 * 
	 * @param      integer     $domainID   Domain code identifier
	 * @return     array       An array containing name servers. If using Registrar's name servers, the array will be empty.
	 * @access     public
	 * @see        setNameServers
	 */
	function getNameServers($domainID){
		
	}
	
	/**
	 * Set domain hosts (records) for a domain name.
	 * 
	 * Executes the '...' command on Registrar's servers, to set domain hosts (records)
	 * for a domain name that is active and belongs to your Ascio / OVH account.
	 * 
	 * @param      integer     $domainID   Domain code identifier
	 * @return     bool        True if succeed and False if failed.
	 * @access     public
	 * @see        getDomainHosts
	 */
	function setDomainHosts($domainID){	
		
	}
	
		
	/**
	 * Get domain hosts (records) for a domain name.
	 * 
	 * Executes the '...' command on Registrar's servers, to get domain hosts (records)
	 * for a domain name that is active and belongs to your Ascio / OVH account.
	 * 
	 * @param      integer     $domainID   Domain code identifier
	 * @return     bool        True if succeed and False if failed.
	 * @access     public
	 * @see        getDomainHosts
	 */
	function getDomainHosts($domainID){
		
	}	

}