<?php

class System_IndexController extends Zend_Controller_Action {
		
    public function indexAction() {
        die();
    }
    
	/**
	 * Export the sample data
	 */	
    public function dataexportAction() {
    	$dsn = Shineisp_Main::getDSN();
    	$conn = Doctrine_Manager::connection($dsn, 'doctrine');
    	$conn->execute('SHOW TABLES'); # Lazy loading of the connection. If I execute a simple command the connection to the database starts.
    	$conn->setAttribute ( Doctrine::ATTR_USE_NATIVE_ENUM, true );
    	$conn->setCharset ( 'UTF8' );

    	// clean up the fixture directory
    	Shineisp_Commons_Utilities::delTree(APPLICATION_PATH . "/configs/data/fixtures/");
    	@mkdir(APPLICATION_PATH . "/configs/data/fixtures/");
    	
    	// Set the current connection
    	$manager = Doctrine_Manager::getInstance()->setCurrentConnection('doctrine');
    		
    	if ($conn->isConnected()) {
    		#Doctrine_Core::dumpData(APPLICATION_PATH . "/configs/data/fixtures/", false);
    		$export = new Doctrine_Data_Export(APPLICATION_PATH . "/configs/data/fixtures/");
    		$export->setFormat('yml');
    		$export->setModels(array());
    		$export->exportIndividualFiles(true);
    		$export->doExport();
    	}
        die('done');
    }

    /**
     * Restore the sample data
     */
    public function dataimportAction() {
    	try{
	    	$conn = Doctrine_Manager::getInstance()->getCurrentConnection();
	    	$conn->setAttribute ( Doctrine::ATTR_USE_NATIVE_ENUM, true );
	    	$conn->setCharset ( 'UTF8' );
	    	$dbh = $conn->getDbh();
	    	$models = Doctrine::getLoadedModels();
// 	    	Zend_Debug::dump($models);
// 	    	die;
	    	$connections = array();
	    	$conn->execute('SET FOREIGN_KEY_CHECKS = 0');
	    	foreach ($models as $model) {
	    		$tablename = Doctrine::getTable($model)->getTableName();
	    		$dbh->query("TRUNCATE TABLE $tablename");
	    	}
	    	
	    	Doctrine_Core::loadData(APPLICATION_PATH . "/configs/data/fixtures/", true);
	    	$conn->execute('SET FOREIGN_KEY_CHECKS = 1');
	    	
    	}catch (Exception $e){
    		Zend_Debug::dump($e->getMessage() . $e->getTraceAsString());
    		Shineisp_Commons_Utilities::log("System Restore Sample Data:" . $e->getMessage());
    	}
        die('done');
    }
}