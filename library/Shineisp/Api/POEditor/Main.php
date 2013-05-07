<?php

/**
 * 
 * @link http://ga-dev-tools.appspot.com/explorer/
 * @author shinesoftware
 *
 */
class Shineisp_Api_POEditor_Main {

	protected static $service;
	
	/**
	 * Download the *.mo file
	 */
	public static function get_languages(){
		try{
			$api = Settings::findbyParam('poeditor_api');
			if(!empty($api)){
				$file_params['id'] = "5434";
				$file_params['action'] = "list_languages";
				$file_params['api_token'] = $api;
					
				$query = http_build_query($file_params);
					
				$contextData = array(
						'method' => 'POST',
						'header' => "Connection: close\r\n".
                    				"Content-type: application/x-www-form-urlencoded\r\n",
						"Content-Length: " . strlen($query) . "\r\n",
						'content' => $query);
	
				// Connect ShineISP to POEditor
				$context = stream_context_create(array('http' => $contextData));
				$result = file_get_contents('https://poeditor.com/api/', false, $context);
				$obj = json_decode($result, true);
				if(!empty($obj['list'])){
					return $obj['list'];
				}
				return false;
			}
		}catch(Exception $e){
			Shineisp_Commons_Utilities::logs ($e->getMessage(), "shineisp.log" );
		}
	}
	
	/**
	 * Download the *.mo file
	 */
	public static function download(){
		try{
			$api = Settings::findbyParam('poeditor_api');
			if(!empty($api)){
				$languages = Languages::getActiveLanguageList();
				$poeditorlanglist = self::get_languages();

				foreach ($languages as $language){
					
					foreach ($poeditorlanglist as $translation){
						
						if($language['code'] == $translation['code']){
							
							$file_params['id'] = "5434";
							$file_params['action'] = "export";
							$file_params['api_token'] = $api;
							$file_params['type'] = "mo";
							$file_params['language'] = $language['code'];
								
							$query = http_build_query($file_params);
								
							$contextData = array(
									'method' => 'POST',
									'header' => "Connection: close\r\n".
                    				"Content-type: application/x-www-form-urlencoded\r\n".
									"Content-Length: " . strlen($query) . "\r\n",
									'content' => $query);
							
							// Connect ShineISP to POEditor
							$context = stream_context_create(array('http' => $contextData));
							$result = file_get_contents('https://poeditor.com/api/', false, $context);
							$obj = json_decode($result, true);
								
							// Check the result
							if(!empty($obj['item'])){
								$file = $obj['item'];
								$destination = PUBLIC_PATH . "/languages/". $language['code'] . "/";
							
								// Create the directory
								@mkdir($destination);
								if(is_dir($destination)){
									
									@unlink($destination . $language['code'] . ".mo");
									
									// Save the translation file
									copy($file, $destination . $language['code'] . ".mo");
									
									// Log the resutl
									Shineisp_Commons_Utilities::logs ("Translation Updated: " . $language['code'] . ".mo", "shineisp.log" );
								}
							}
						}
					}
				}
			}
			return true;
		}catch(Exception $e){
			Shineisp_Commons_Utilities::logs ($e->getMessage(), "shineisp.log" );
			return false;
		}
	}
}
