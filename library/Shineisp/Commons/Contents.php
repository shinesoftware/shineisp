<?php

/**
 * This class shows all the text modifications
 * like blocks, modules. The text parsed with these 
 * methods will be edited before to be shown in the
 * web pages.
 * @version 1.0
 * @author Shine Software
 */

class Shineisp_Commons_Contents {
	
	/**
	 * getAllBlocks
	 * get all the blocks within a long text
	 * @param string $text
	 * @return array
	 */
	public static function getAllBlocks($text) {
		preg_match_all( '(.*{block(.+)}.*)Ui', $text, $matches );
		return $matches;
	}
	
	/**
	 * getAllModules
	 * get all the modules within a long text
	 * @param string $text
	 */
	public static function getAllModules($text) {
		preg_match_all( '(.*{module(.+)}.*)Ui', $text, $matches );
		return $matches;
	}
	
	/**
	 * chkModule
	 * Replace all the blocks placeholder with the process of a custom class
	 * 
	 * In the cms page you have to write in this way within the text body
	 * {module name="productlist" class="Products" method="showlist" ... }
	 * 
	 * @param string $text
	 * @return string
	 */
	public static function chkModule($text, $locale="en_US") {

		// Get all the blocks in the whole text
		$modules = self::getAllModules($text);
		$parameters = array();
		
		if(!empty($modules[0])){
			
			// For each block do ... 
			foreach ($modules[0] as $module) {
				$name 		= "";
				$class 		= "";
				$method 	= "";
				$parameters = array();
				
				// Get the information from the block 
				preg_match_all ( '(([a-zA-Z0-9]+)=\"(.+)\")Ui', $module, $matches );
				
				// If the information are correct we get the name of the cms block to load
				if(!empty($matches[1][1]) && $matches[1][0] == "name" 
							&& !empty($matches[1][1]) && $matches[1][1] == "class" 
							&& !empty($matches[1][1]) && $matches[1][2] == "method"){

					// Here we need to delete the --> " <--- char 
					$name = str_replace("\"", "", $matches[2][0]);	
					$class = str_replace("\"", "", $matches[2][1]);	
					$method = str_replace("\"", "", $matches[2][2]);

					/*
					 * Handle the parameters sent to the module
					 * {module name="test1" class="test1" method="show" parameter1="1"}
					 * {module name="test2" class="test2" method="show" parameter1="1" parameter2="21"}
					 */  
					
					if(count($matches[2]) > 3){
						for ($i=3; $i < count($matches[2]); $i++){
							$parameters[$matches[1][$i]] = str_replace("\"", "", $matches[2][$i]);
						}
					}	
					
					if(!empty($name) & !empty($class) && !empty($method)){
						if(class_exists($class) && method_exists($class, $method)){
							
							$myobj = new $class();
							$result = $myobj->$method($parameters);
							$text = str_replace($module, $result, $text);
						}else{
							$text = str_replace($module, "[ERROR class: $class or method: $method doesn't exist.]", $text);
						}
					}
				}
			}
		}
		
		return $text;
	}
	
	/**
	 * chkCMSBlocks
	 * Replace all the blocks with the cms blocks found
	 * 
	 * In the cms page you have to write in this way within the text body
	 * {block name="homepage"}
	 * 
	 * @param string $text
	 * @return string
	 */
	public static function chkCMSBlocks($text, $locale="en_US") {
		$languageID = Languages::get_language_id($locale);
		
		// Get all the blocks in the whole text
		$blocks = self::getAllBlocks($text);
		
		if(!empty($blocks[0])){
			
			// For each block do ... 
			foreach ($blocks[0] as $block) {
				
				// Get the information from the block 
				preg_match_all ( '(([a-zA-Z0-9]+)=\"(.+)\")Ui', $block, $matches );
				
				// If the information are correct we get the name of the cms block to load
				if(!empty($matches[2][0])){

					// This is the name of the cms block to load from the db
					$blockname = str_replace("\"", "", $matches[2][0]);	
					
					// Get the block information
					$rsblock = CMSBlocks::findbyvar($blockname, $languageID);

					// Replace the block placeholder with the cmsblock body
					if(!empty($rsblock[0])){
						$text = str_replace($block, $rsblock[0]['body'], $text);
					}else{
						$text = str_replace($block, "[ERROR CMS block: $blockname doesn't exist.]", $text);
					}
				}
			}
		}
		
		return $text;
	}
}