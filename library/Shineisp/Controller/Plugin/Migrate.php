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
		$dayssincefirstsetup = 0;
		$migration = new Doctrine_Migration(APPLICATION_PATH . '/configs/migrations');
		
		$LatestVersion = $migration->getLatestVersion();
		$CurrentVersion = $migration->getCurrentVersion();
		
		try{

			// Check if the config file has been created
			$isReady = Shineisp_Main::isReady();
			
			if($isReady){
				$db = Doctrine_Manager::getInstance()->getCurrentConnection();
				
				// Read and execute all the sql files saved in the /application/configs/data/sql directory
				$path = PROJECT_PATH . "/application/configs/data/sql";
				if(is_dir($path)){
					$directory_iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path));
					try{
						// get the first setup date 
						$setupdate = Settings::getConfigSetupDate();
						
						if(empty($setupdate)){
							throw new Exception('Setup date is not set in the config.xml file');
						}
						
						// for each sql file do ...
						foreach($directory_iterator as $filename => $path_object){
							
							// get the sql file information
							$info = pathinfo($filename);
							
							if(!empty($info['extension']) && $info['extension'] == "sql"){
									
								$name = $info['filename'];
								
								// get the first part of the name with the filename that contains the date: YYYYMMddHis-NAME.sql
								$arrName = explode("-", $name);
									
								// if the string is a valid date get the days betweeen the sql file name and the day of the setup of shineisp
// 								if(!empty($arrName[0]) && Zend_Date::isdate($arrName[0], 'YYYYMMddHis')){
// 									$sqldate = new Zend_Date($arrName[0], 'YYYYMMddHis');
// 									$mysetupdate = new Zend_Date($setupdate, 'YYYYMMddHis');
								
// 									// get the difference of the two dates
// 									$diff = $sqldate->sub($mysetupdate)->toValue();
// 									$dayssincefirstsetup = floor($diff/60/60/24);
									
// 									unset($sqldate);
// 									unset($mysetupdate);
// 								}
								
								// read the sql 
								$sql = Shineisp_Commons_Utilities::readfile($info['dirname'] . "/" . $info['basename'] );

								if(!empty($sql)){
									
									// execute the sql strings
									$result = $db->execute($sql);
	
									// close the db connection
									$db->close();
									
									if($result){
										// write a log message
										Shineisp_Commons_Utilities::log($info['filename'] . ".sql has been executed.");
										
										// rename the sql
										rename($info['dirname'] . "/" . $info['basename'], $info['dirname'] . "/" . $info['filename'] . ".sql.old");
									}
								}
							}
						}
					}catch(Exception $e){
						die($e->getMessage());
					}
				}
				
			}
			
			// Execute the migration 
			if($CurrentVersion < $LatestVersion){
			
				$dbconfig = Shineisp_Main::databaseConfig();
			
				// Update the version in the config.xml file previously created
				Settings::saveConfig($dbconfig, $LatestVersion);
			
				if($CurrentVersion > 0){
					Shineisp_Commons_Utilities::log("Migrate ShineISP version from $CurrentVersion to $LatestVersion");
					$migration->migrate();
				}
			}
				
			
		}catch (Exception $e){
			Zend_Debug::dump($e->getMessage());
			die;
		}
	}
}