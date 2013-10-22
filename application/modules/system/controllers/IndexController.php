<?php

class System_IndexController extends Shineisp_Controller_Default {
		
    public function indexAction() {
        die();
    }
    
    /**
     * Update the database item translation using the poedit!
     * This method creates a temporary file that helps you to parse the 
     * database table common contents.  
     */
    public function updatetranslationAction() {
        
        $content = "<?php\n ";
        $content .= "# WARNING: This file has been created only for the POEDIT software.\n";
        $content .= "#          You can delete it, if you don't use it! \n\n";
        
        // Setting Parameters 
        $data = SettingsParameters::getAllInfo();
        foreach ($data as $item){
            $content .= "echo _('".$item['name']."')\n";
            $content .= "echo _('".$item['description']."')\n";
        }
        
        // Server Types 
        $data = Servers_Types::getList();
        foreach ($data as $id => $item){
            $content .= "echo _('".$item."')\n";
        }
        
        // Contact types
        $data = ContactsTypes::getList();
        foreach ($data as $id => $item){
            $content .= "echo _('".$item."')\n";
        }
        
        // Legal form
        $data = Legalforms::getList();
        foreach ($data as $id => $item){
            $content .= "echo _('".$item."')\n";
        }
        
        // Get the default navigation items
        $config = new Zend_Config_Xml(APPLICATION_PATH . '/modules/default/navigation.xml','nav');
        $navigation = new Zend_Navigation($config);
        
        // Iterate recursively using RecursiveIteratorIterator
        $pages = new RecursiveIteratorIterator($navigation, RecursiveIteratorIterator::SELF_FIRST);
        foreach ($pages as $page){
            $label = (string)$page->label;
            $content .= "echo _('$label')\n";
        }
        
        // Get the administration navigation items
        $config = new Zend_Config_Xml(APPLICATION_PATH . '/modules/admin/navigation.xml','nav');
        $navigation = new Zend_Navigation($config);
        
        // Iterate recursively using RecursiveIteratorIterator
        $pages = new RecursiveIteratorIterator($navigation, RecursiveIteratorIterator::SELF_FIRST);
        foreach ($pages as $page){
            $label = $page->getLabel();
            $content .= "echo _('$label')\n";
        }
        
        $content .= "?>";
        Shineisp_Commons_Utilities::writefile($content, "tmp", "translations.php");
       
        die('Ok! Now update the default.po file by the poedit software');
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
}