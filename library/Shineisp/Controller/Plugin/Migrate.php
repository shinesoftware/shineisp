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
			
			// Check if the config file has been created
			$isReady = Shineisp_Main::isReady();
			
			if($isReady){
				$db = Doctrine_Manager::getInstance()->getCurrentConnection();
				
				// Read and execute all the sql files saved in the /application/configs/data/sql directory
				$path = PROJECT_PATH . "/application/configs/data/sql";
				if(is_dir($path)){
					$directory_iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path));
					try{
						foreach($directory_iterator as $filename => $path_object)
						{
							$info = pathinfo($filename);
							if(!empty($info['extension']) && $info['extension'] == "sql"){
								// read the sql 
								$sql = Shineisp_Commons_Utilities::readfile($info['dirname'] . "/" . $info['basename'] );

								// execute the sql strings
								$result = $db->execute($sql);

								// close the db connection
								$db->close();
								
								if($result){
									// rename the sql
									rename($info['dirname'] . "/" . $info['basename'], $info['dirname'] . "/" . $info['filename'] . ".sql.old");
								}
							}
						}
					}catch(Exception $e){
						die($e->getMessage());
					}
				}
				
			}
			
		}catch (Exception $e){
			Zend_Debug::dump($e->getMessage());
			die;
		}
	}
}