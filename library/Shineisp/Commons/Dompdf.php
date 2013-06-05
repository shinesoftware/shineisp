<?php
class Shineisp_Commons_Dompdf {
	public $dompdf = null;
	
	public function __construct() {
				
		require_once 'dompdf_config.inc.php';  
		$autoloader = Zend_Loader_Autoloader::getInstance(); // assuming we're in a controller  
		$autoloader->pushAutoloader('DOMPDF_autoload');
		
		$this->dompdf = new DOMPDF();
		
	}
}
