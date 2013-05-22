<?php

/**
 * Settings
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 6820 2009-11-30 17:27:49Z jwage $
 */
class Settings extends BaseSettings {

	/**
	 * Save the setup data
	 * @param array $dbconfig
	 * @param array $preferences
	 * 
	 * <?xml version="1.0" encoding="UTF-8"?>
		<shineisp>
			<config>
				<database>
					<hostname>localhost</hostname>
					<db>shineisp</db>
					<username>shineisp</username>
					<password>shineisp2013</password>
				</database>
			</config>
		</shineisp>
	 */
	public static function saveConfig($dbconfig, $version=null) {
		
		try{
			$xml = new SimpleXMLElement('<shineisp></shineisp>');
			
			if(!empty($version)){
				$xml->addAttribute('version', $version);
			}
			
			$xml->addAttribute('setupdate', date('Ymdhis'));
			$config = $xml->addChild('config');
			
			// Database Configuration
			$database = $config->addChild('database');
			$database->addChild('hostname', $dbconfig->hostname);
			$database->addChild('username', $dbconfig->username);
			$database->addChild('password', $dbconfig->password);
			$database->addChild('database', $dbconfig->database);

			// Get the xml string
			$xmlstring =$xml->asXML();
			
			// Prettify and save the xml configuration
			$dom = new DOMDocument();
			$dom->loadXML($xmlstring);
			$dom->formatOutput = true;
			$formattedXML = $dom->saveXML();
			
			// Save the config xml file
			if(@$dom->save(APPLICATION_PATH . "/configs/config.xml")){
				return true;
			}else{
				throw new Exception("Error on saving the xml file in " . APPLICATION_PATH . "/configs/config.xml <br/>Please check the folder permissions");
			}
		}catch (Exception $e){
			throw new Exception($e->getMessage());
		}
		return false;
	}
	
	/**
	 * get the setup date from the xml config file
	 */
	public static function getConfigSetupDate() {
		$configfile = APPLICATION_PATH . "/configs/config.xml";
		if(file_exists($configfile)){
			$xml = simplexml_load_file($configfile);
			$attributes = $xml->attributes();
			if(!empty($attributes['setupdate'])){
				return (string) $attributes['setupdate'];
			}else{
				throw new Exception('Setup date has been not set in the config xml file at the installation process');
			}
		}
		
	}
	
	/**
	 * Create the ShineISP Database
	 */
	public static function createDb($installsampledata=true) {
		
		try{
			$dbconfig = Shineisp_Main::databaseConfig();
			$dsn = Shineisp_Main::getDSN();
			$conn = Doctrine_Manager::connection($dsn, 'doctrine');
			$conn->execute('SHOW TABLES'); # Lazy loading of the connection. If I execute a simple command the connection to the database starts.
			$conn->setAttribute ( Doctrine::ATTR_USE_NATIVE_ENUM, true );
			$conn->setCharset ( 'UTF8' );
			$dbh = $conn->getDbh();
			$models = Doctrine::getLoadedModels();

			// Set the current connection
			$manager = Doctrine_Manager::getInstance()->setCurrentConnection('doctrine');
			
			if ($conn->isConnected()) {
				$migration = new Doctrine_Migration(APPLICATION_PATH . '/configs/migrations');
				
				// Get the latest version set in the migrations directory
				$latestversion = $migration->getLatestVersion();
				if(empty($latestversion)){
					$latestversion = 0;
				}

				// Clean the database
				$conn->execute('SET FOREIGN_KEY_CHECKS = 0');
				foreach ($models as $model) {
					$tablename = Doctrine::getTable($model)->getTableName();
					$dbh->query("DROP TABLE IF EXISTS $tablename");
				}
				
				// Create the migration_version table
				Doctrine_Manager::getInstance()->getCurrentConnection()->execute('DROP TABLE IF EXISTS `migration_version`;CREATE TABLE `migration_version` (`version` int(11) DEFAULT NULL) ENGINE=InnoDB DEFAULT CHARSET=latin1;INSERT INTO `migration_version` VALUES ('.$latestversion.')');

				// Create all the tables in the database
				Doctrine_Core::createTablesFromModels(APPLICATION_PATH . '/models');
				
				// Common resources
				Doctrine_Core::loadData(APPLICATION_PATH . '/configs/data/fixtures/commons/', true);
				
				// Sample data
				if($installsampledata){
					$import = new Doctrine_Data_Import(APPLICATION_PATH . '/configs/data/fixtures/');
					$import->setFormat('yml');
					$import->setModels($models);
					$import->doImport(true);
				}
				
				$conn->execute('SET FOREIGN_KEY_CHECKS = 1');
				
				// Update the version in the config.xml file previously created
				Settings::saveConfig($dbconfig, $latestversion);

			}else{
				echo "No Connection found";
			}
		}catch (Exception $e){
			die($e);
		}
		
		// return the latest version
		return $latestversion;
	}
	
	/**
	 * Get the currency by the locale
	 * @param unknown_type $locale
	 */
	public static function getCurrency($customlocale = null) {
		$locale = new Zend_Locale($customlocale);
		$currency = new Zend_Currency($locale);
		return !empty($currency->symbol) ? $currency->symbol : null; 
	}
	
	/**
	 * Get the currency list
	 */
	public static function getCurrencyList($customlocale = null) {
		$locale = new Zend_Locale();
		
		/**
		 * Generate a Currency select box with the localizes currency
		 * names based upon the current application wide locale.
		 */
		$currencies = ($locale->getTranslationList('NameToCurrency'));
		 
		asort($currencies, SORT_LOCALE_STRING);

		return $currencies;
	}

	/**
	 * Get the late fee types
	 */
	public static function getLateFeeTypes() {
		return array('fixed'=>'Fixed', 'percentage'=>'Percentage');
	}

	/**
	 * Get the auto invoice creation values
	 */
	public static function getAutoInvoiceGenerationValues() {
		return array('0'=>"Don't automatically create an invoice as soon the whole order is paid", '1'=>'Automatically create an invoice as soon the whole order is paid');
	}


	
	/**
     * findbyParam
     * Get a record by the Parameter
     * @param $parameter
     * @param $module
     * @param $isp
     * @return Doctrine Record
     */
    public static function findbyParam($parameter, $module = "", $isp = 1) {
    	$session = new Zend_Session_Namespace ( 'Default' );
    	
    	if(!empty($session->parameters[$parameter])){
    		return $session->parameters[$parameter];
    	}
    	return null;
    }
    
	/**
     * Get all the records by Group
     * 
     * 
     * @param $parameter
     * @param $module
     * @param $isp
     * @return Doctrine Record
     */
    public static function findbyGroup($groupname, $isp = 1) {
        $records = Doctrine_Query::create ()
                          ->from ( 'SettingsGroups g' )
                          ->leftJoin ( 'g.SettingsParameters sp' )
                          ->leftJoin ( 'sp.Settings s' )
                          ->where ( "g.name = ?", $groupname )
                          ->addWhere ( "s.isp_id = ?", $isp )
                          ->execute ( array (), Doctrine_Core::HYDRATE_ARRAY );
                          
        $records = !empty($records[0]) ? $records[0] : array(); 
        return $records;
    }
    
	/**
     * findbyModule
     * Get all the settings by the Module name
     * @param $module
     * @param $isp
     * @return Array Record
     */
    public static function findbyModule($module, $fields="*", $isp = 1) {
        $records = Doctrine_Query::create ()->select($fields)
                          ->from ( 'Settings s' )
                          ->leftJoin ( 's.SettingsParameters p' )
                          ->where ( "p.module = ?", $module )
                          ->addWhere ( "isp_id = ?", $isp )
                          ->execute ( array (), Doctrine_Core::HYDRATE_ARRAY );
        return $records;
    }
    
	/**
     * findRecord
     * Get a record by the Parameter
     * @param $parameter
     * @param $isp
     * @return Array 
     */
    public static function findRecord($parameter, $isp = 1) {
        $dq = Doctrine_Query::create ()
                          ->from ( 'Settings s' )
                          ->leftJoin ( 's.SettingsParameters p' )
                          ->where ( "p.var = ?", $parameter )
                          ->addWhere ( "s.isp_id = ?", $isp );
                          
        $records = $dq->execute ( array (), Doctrine_Core::HYDRATE_ARRAY );
        $records = isset($records[0]) ? $records[0] : null; 
        return $records;
    }
    
	/**
     * delete
     * Delete a setting parameter
     * @param $id
     * @param $isp
     * @return Boolean 
     */
    public static function deleteItem($id, $isp = 1) {
        Doctrine_Query::create ()->delete('Settings s')
                          ->where ( "s.setting_id = ?", $id )
                          ->andWhere ( "s.isp_id = ?", $isp )
                          ->execute ();
                          
        return true;
    }
    
    /**
     * findbyGroup
     * Get a record by the Parameter
     * @param $groupid
     * @param $module
     * @param $isp
     * @return Doctrine Record
     */
    public static function find_by_GroupId($groupid, $module = "", $isp = 1) {
        $dq = Doctrine_Query::create ()->select('setting_id, p.parameter_id as parameterid, g.group_id as groupid, p.var as variable, g.name as groupname, s.value as value')
                          ->from ( 'Settings s' )
                          ->leftJoin ( 's.SettingsParameters p' )
                          ->leftJoin ( 'p.SettingsGroups g' )
                          ->where ( "g.group_id = ?", $groupid )
                          ->andWhere ( "s.isp_id = ?", $isp );
         
        if(!empty($module)){
            $dq->andWhere ( "module = ?", $module );
         }
                          
        $records = $dq->execute ( array (), Doctrine_Core::HYDRATE_ARRAY );
        return $records;
    }
    

    /**
     * addSetting
     * Add a new setting
     * @param integer $parameter_id
     * @param integer $isp_id
     * @param string $value
     */
    public static function addSetting($var, $value, $isp_id=1) {
    	
    	$record = Doctrine_Query::create ()
                    ->from ( 'Settings s' )
                    ->leftJoin ( 's.SettingsParameters p' )
                    ->where ( "p.var = ?", $var )
                    ->andwhere ( "s.isp_id = ?", $isp_id )
                    ->limit ( 1 )->execute ( array (), Doctrine_Core::HYDRATE_ARRAY );
                    
    	if(!empty($record[0])){
    		return $record[0]['setting_id'];
    	}
    	
    	$setting = new Settings();
    	$parameter = SettingsParameters::getParameterbyVar($var);
    	
    	$setting['isp_id'] = $isp_id;
    	$setting['parameter_id'] = $parameter['parameter_id'];
    	$setting['value'] = $value;
    	$setting->save();
    	return $setting['setting_id'];
    }
    

    /**
     * Save setting
     * 
     * @param integer $parameter_id
     * @param integer $isp_id
     * @param string $value
     */
    public static function saveSetting($var, $value, $isp_id=1) {
    	
    	$setting = Doctrine_Query::create ()
                    ->from ( 'Settings s' )
                    ->leftJoin ( 's.SettingsParameters p' )
                    ->where ( "p.var = ?", $var )
                    ->andwhere ( "s.isp_id = ?", $isp_id )
                    ->limit ( 1 )->execute ();
    	
        if(!empty($setting)){
        	$parameter = SettingsParameters::getParameterbyVar($var);
	    	$setting[0]['isp_id'] = $isp_id;
	    	$setting[0]['parameter_id'] = $parameter['parameter_id'];
	    	$setting[0]['value'] = $value;
	    	$setting->save();
	    	
	    	return $setting['setting_id'];
        }
        
        return false;
    }
	
	/**
	 * saveRecord
	 * save the setting record group parameters
	 * @param integer $groupid
	 * @param integer $isp
	 */
	public static function saveRecord($groupid, $post, $isp = 1) {
		$i = 0;
		if (! empty ( $post )) {
			$records = new Doctrine_Collection ( 'Settings' );
			foreach ( $post as $field => $value ) {
				
				// Get the old setting parameter value
				$setting = self::findRecord ( $field, $isp );
				if (! empty ( $setting )) {
					// Delete the old record
					self::deleteItem ( $setting ['setting_id'] );
				}
				
				// Get the parameter record
				$paramenter = SettingsParameters::getParameterbyVar ( $field );
				
				// Create the collection of records
				$records [$i]->isp_id = $isp;
				$records [$i]->parameter_id = $paramenter ['parameter_id'];
				$records [$i]->value = $value;
				$i ++;
			}
			
			// Save the records
			$records->save ();

			// Refresh the parameters
			SettingsParameters::loadParams();
		}
		return true;
	}
	
	/**
     * getAllInfo
     * Get all data from the setting
     * @param $id
     * @return Doctrine Record / Array
     */
    public static function getAllInfo($id, $fields = "*", $retarray = false) {
        
        try {
            $dq = Doctrine_Query::create ()->select ( $fields )
                    ->from ( 'Settings s' )
                    ->where ( "s.setting_id = $id" )
                    ->limit ( 1 );
            
            $retarray = $retarray ? Doctrine_Core::HYDRATE_ARRAY : null;
            $item = $dq->execute ( array (), $retarray );
            
            return $item;   
        } catch (Exception $e) {
            die ( $e->getMessage () );
        }
    }   
    
    /**
     * find
     * Get a record by ID
     * @param $id
     * @return Doctrine Record
     */
    public static function find($id, $fields = "*", $retarray = false) {
    	try {
	        $dq = Doctrine_Query::create ()->select ( $fields )->from ( 'Settings s' )
	        ->leftJoin ( 's.SettingsParameters sp ' )
	        ->where ( "s.setting_id = $id" )
	        ->limit ( 1 );
	        
	        $retarray = $retarray ? Doctrine_Core::HYDRATE_ARRAY : null;
	        $record = $dq->execute ( array (), $retarray );
	        return $record;
    	} catch (Exception $e) {
            die ( $e->getMessage () );
        }
    }    
}