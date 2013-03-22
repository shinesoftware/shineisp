<?php

/**
 * Check the version of the software and 
 * update the database with the last editings
 * 
 * 
 * @author shinesoftware
 */
class Shineisp_Controller_Plugin_Migrate extends Zend_Controller_Plugin_Abstract {
	
	/**
	 * (non-PHPdoc)
	 * @see Zend_Controller_Plugin_Abstract::preDispatch()
	 */
	public function preDispatch(Zend_Controller_Request_Abstract $request) {
		
		$migration = new Doctrine_Migration(APPLICATION_PATH . '/configs/migrations');
		
		$LatestVersion = $migration->getLatestVersion();
		$CurrentVersion = $migration->getCurrentVersion();
		
		try{
			if($CurrentVersion < $LatestVersion){
				
				$dbconfig = Shineisp_Main::databaseConfig();
				
				// Update the version in the config.xml file previously created
				Settings::saveConfig($dbconfig, $LatestVersion);
				
				if($CurrentVersion > 0){
					$migration->migrate();
				}
			}
		}catch (Exception $e){
			Zend_Debug::dump($e->getMessage());
			die;
		}
	}
}