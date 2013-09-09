<?php

/**
 * 
 * Main ShineISP Class
 * @author shinesoftware
 *
 */

class Shineisp_Main {

	/**
	 * Check the existence of the config file
	 * @return boolean
	 */
	static public function isReady(){
		try{
			$config = Shineisp_Commons_Utilities::readfile(APPLICATION_PATH . "/configs/config.xml");
			if(!empty($config)){

				// Get the xml information
				$xml = self::loadConfig();
				$attributes =$xml->attributes();
				
				// If the version of the application has been written, ShineISP has been well configured
				if(!empty($attributes['version'])){
					return true;
				}else{
					return false;
				}
			}else{
				return false;
			}
		}catch (Exception $e){
			echo $e->getMessage();
		}
	}
    
	
	/**
	 * Get the DSN string to connect the database
	 * @throws Exception
	 */
	static public function getDSN(){
		$xml = self::loadConfig();
		if(empty($xml->config->database->hostname)){
			throw new Exception("hostname is not set in the config file");
		}
	
		if(empty($xml->config->database->database)){
			throw new Exception("database is not set in the config file");
		}
	
		if(empty($xml->config->database->username)){
			throw new Exception("username is not set in the config file");
		}
	
		if(empty($xml->config->database->password)){
			throw new Exception("password is not set in the config file");
		}
	
		$host = (string)$xml->config->database->hostname;
		$database = (string)$xml->config->database->database;
		$username = (string)$xml->config->database->username;
		$password = (string)$xml->config->database->password;
	
		return "mysql://$username:$password@$host/$database";
	}
	
	
	/**
	 * Load the db config parameters
	 * @return SimpleXMLElement
	 */
	static public function databaseConfig(){
		try{
			$data = self::loadConfig();
			if(!empty($data->config->database)){
				return $data->config->database;
			}
		}catch (Exception $e){
			echo $e->getMessage();
		}
	}
	
	/**
	 * Load of the configuration file
	 * @return SimpleXMLElement
	 */
	static public function loadConfig(){
		$confFile = APPLICATION_PATH . "/configs/config.xml";
		try{
			if(file_exists($confFile) && is_readable($confFile)){
				$config = Shineisp_Commons_Utilities::readfile($confFile);
				if(!empty($config)){
					$config = new Zend_Config_Xml($confFile);
					if(simplexml_load_file ( $confFile )){
						Shineisp_Registry::set('config', $config);
						return simplexml_load_file ( $confFile );
					}else{
						throw new Exception("XML Config file is not readable or not well-formed");
					}
				}
			}
		} catch (Exception $e) {
			echo $e->getMessage();
			echo "<xmp>";
			echo $e->getTraceAsString();
			echo "</xmp>";
		}
	}
	
}

?>