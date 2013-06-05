<?php
/**
 * ShineISP new Plugin Architecture
 * @author GUEST.it s.r.l. <assistenza@guest.it>
 *
 */
class Shineisp_Plugins {
	/*
	 * Initialize all plugins available and register subscriptions
	 */
	public function initAll() {
		
		// Get the Zend Event Manager
		$em = Zend_Registry::get('em');
		
		$mainConfigfile = APPLICATION_PATH . "/configs/config.xml";
			
		if(file_exists($mainConfigfile)){
			$mainconfig = simplexml_load_file($mainConfigfile);
		}else{
			throw new Exception($mainConfigfile . " has been not found");
		}
			
		if(!count($mainconfig->xpath("/shineisp/modules"))){
			$modules = $mainconfig->addChild('modules');
		}else{
			$modules = $mainconfig->xpath("/shineisp/modules");
		}
		
		$path = PROJECT_PATH . "/library/Shineisp/Plugins";
		$iterator = new DirectoryIterator($path);
		foreach ($iterator as $fileinfo) {
    		if ($fileinfo->isDir() && !$fileinfo->isDot()) {
    			
        		$pluginDir      = $fileinfo->getFilename();
				$pluginMainFile = $path.'/'.$pluginDir.'/Main.php';
				$pluginConfigFile = $path.'/'.$pluginDir.'/config.xml';
				$pluginName     = 'Shineisp_Plugins_'.$pluginDir.'_Main';
				
				Shineisp_Commons_Utilities::logs("Open plugin '".$pluginName."'", "plugins.log" );
				
				// Check if plugins looks good
				$reflectionClass = new ReflectionClass($pluginName);
				if ( ! ($reflectionClass->isInstantiable() && $reflectionClass->implementsInterface('Shineisp_Plugins_Interface') && is_callable(array($pluginName,'events')) ) ) {
					Shineisp_Commons_Utilities::logs("Skipping not instantiable plugin '".$pluginName."'", "plugins.log" );
					continue;
				}

				// Initialize
				$plugin = new $pluginName;
				$plugin->events($em);				 
				
				// Check if the Main exists
				if ( file_exists($pluginConfigFile) && is_readable($pluginConfigFile) ) {
					
					$info = pathinfo($pluginConfigFile);
					
					if(!empty($info['extension']) && $info['extension'] == "xml"){
						
						if (file_exists ($pluginConfigFile)) {
							$config = simplexml_load_file ( $pluginConfigFile );
		
							// If the config file has been created for the registrar ignore it 
							// because the configuration is delegated to the registrar management
							if(isset($config->attributes ()->type) && "registrars" == (string)$config->attributes ()->type){
								continue;
							}
							
							$panelName = (string)$config->attributes ()->name;
							$var = (string)$config['var'];

							// Save the module setup in the config.xml file
							// Now we are checking if the module is already set in the config.xml file
							if(!count($mainconfig->xpath("/shineisp/modules/$var"))){
		
								// The module is not present, we have to create it
								$module = $modules[0]->addChild($var);
								
								// Now we add the setup date as attribute
								$module->addAttribute('setup', date('YmdHis'));
							} else {
								
								// The module is present and we get it
								$module = $mainconfig->xpath("/shineisp/modules/$var");
								
								// If the setup date attribute is present skip the module process setup 
								if(!empty($module[0]) && $module[0]->attributes()->setup){
									continue;
								}else{
									// The setup attribute is not present restart the module setup process
									$module[0]->addAttribute('setup', date('YmdHis'));
								}
							}
							
							$help = (string)$config->general->help ? (string)$config->general->help : NULL;
							$description = (string)$config->general->description ? (string)$config->general->description : NULL;
							
							$group_id = SettingsGroups::addGroup($config['name'], $description, $help);
							
							foreach ($config->settings->children() as $node) {
								$arr   = $node->attributes();
								$var   = strtolower($config['var']) . "_" . (string) $arr['var'];
								$label = (string) $arr['label'];
								$type  = (string) $arr['type'];
								$description = (string) $arr['description'];
							
								if (!empty($var) && !empty($label) && !empty($type)) {
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
						
						$xmlstring = $mainconfig->asXML();
						
						// Prettify and save the xml configuration
						$dom = new DOMDocument('1.0');
						$dom->loadXML($xmlstring);
						$doc->formatOutput = TRUE;
						$doc->preserveWhiteSpace = TRUE;
						$dom->saveXML();
						if(!@$dom->save($mainConfigfile)){
							throw new Exception("Error on saving the xml file in $mainConfigfile <br/>Please check the folder permissions");
						}
					}
				} 
    		}
		}
	}
}
