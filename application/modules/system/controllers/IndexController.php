<?php

class System_IndexController extends Zend_Controller_Action {
		
    public function indexAction() {
        die();
    }
		
    public function dataexportAction() {
    	$dsn = Shineisp_Main::getDSN();
    	$conn = Doctrine_Manager::connection($dsn, 'doctrine');
    	$conn->execute('SHOW TABLES'); # Lazy loading of the connection. If I execute a simple command the connection to the database starts.
    	$conn->setAttribute ( Doctrine::ATTR_USE_NATIVE_ENUM, true );
    	$conn->setCharset ( 'UTF8' );
    	
    	// Set the current connection
    	$manager = Doctrine_Manager::getInstance()->setCurrentConnection('doctrine');
    		
    	if ($conn->isConnected()) {
    		Doctrine_Core::dumpData(APPLICATION_PATH . "/configs/data/fixtures/", false);
    	}
        die('done');
    }
}