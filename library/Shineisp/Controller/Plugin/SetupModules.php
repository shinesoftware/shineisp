<?php

/**
 * Setup all the Modules
 * @author shinesoftware
 *
 */
class Shineisp_Controller_Plugin_SetupModules extends Zend_Controller_Plugin_Abstract {
	
	/**
	 * Execute the check in the /library/Shineisp/Api/Panels path 
	 * in order to get all the config.xml files and create the module
	 * settings paramenters within the database. 
	 * 
	 * (non-PHPdoc)
	 * @see Zend_Controller_Plugin_Abstract::preDispatch()
	 */
	public function preDispatch(Zend_Controller_Request_Abstract $request) {
		
		$path = PROJECT_PATH . "/library/Shineisp/Api";
		$directory_iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path));
		foreach($directory_iterator as $filename => $path_object)
		{
			$info = pathinfo($filename);
			if(!empty($info['extension']) && $info['extension'] == "xml"){
				if (file_exists ($filename)) {
					$config = simplexml_load_file ( $filename );

					// If the config file has been created for the registrar ignore it 
					// because the configuration is delegated to the registrar management
					if(isset($config->attributes ()->type) && "registrars" == (string)$config->attributes ()->type){
						continue;
					}
					
					$panelName = ( string ) $config->attributes ()->name;

					$help = (string)$config->general->help ? (string)$config->general->help : NULL;
					$description = (string)$config->general->description ? (string)$config->general->description : NULL;
					
					$group_id = SettingsGroups::addGroup($config['name'], $description, $help);
					
					foreach ($config->settings->children() as $node) {
						$arr   = $node->attributes();
						$var   = strtolower($config['var']) . "_" . (string) $arr['var'];
						$label = (string) $arr['label'];
						$type  = (string) $arr['type'];
						$description = (string) $arr['description'];
					
						if(!empty($var) && !empty($label) && !empty($type)){
							SettingsParameters::addParam($label, $description, $var, $type, 'admin', 1, $group_id);
						}
					}
					if(!empty($config->customfields)){
						foreach ($config->customfields->children() as $node) {
							$arr      = $node->attributes();
							$var      = ( string ) $node;
							$label    = (string) $arr['label'];
							$type     = (string) $arr['type'];
							$section  = (string) $arr['section'];
					
							// Fetch panel_id from database
							if ( !empty($panelName) ) {
								$Panels = Panels::getAllInfoByName($panelName);
							}
							
							$panel_id = (!empty($Panels) && isset($Panels['panel_id']) && $Panels['panel_id'] > 0) ? intval($Panels['panel_id']) : null;
												
							if(!empty($var) && !empty($label) && !empty($type)){
								CustomAttributes::createAttribute($var, $label, $type, $section, $panel_id);
							}
						}
					}
				}
			}
		}
	}
}