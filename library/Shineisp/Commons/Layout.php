<?php
/**
 * Shineisp_Commons_Layout
 * Handle the template of the project
 * @author Shine Software Staff
 *
 */
class Shineisp_Commons_Layout {
	
	protected static $data = array ();
	protected static $blocks = array ();
	
	/**
	 * getData
	 * get the layout data for each resource of the project
	 */
	public static function getData($module, $skin) {
		try {
			$path = APPLICATION_PATH . '/layout.xml';

			// Override the layout file
			if(!empty($module)){
				$tmp_path = APPLICATION_PATH . "/modules/$module/layout.xml";
				if(file_exists($tmp_path)){
					$path = $tmp_path;
				}
			}
			
			// Override the layout file
			if(!empty($module) && !empty($skin)){
				$tmp_path = APPLICATION_PATH . "/modules/$module/$skin/layout.xml";
				if(file_exists($tmp_path)){
					$path = $tmp_path;
				}
			}
			
			// Override the layout file
			if(!empty($module) && !empty($skin)){
				$tmp_path = PUBLIC_PATH . "/skins/$module/$skin/layout.xml";
				if(file_exists($tmp_path)){
					$path = $tmp_path;
				}
			}
			
			// Check if the layout xml file exists 
			if (file_exists ( $path )) {
				
				// Load the xml into a variable
				$data = simplexml_load_file ( $path );
			
			} else {
				die ( 'Layout file has not been found at ' . $path );
			}
		
		} catch ( Exception $e ) {
			die ( $e->getMessage () );
		}
		return $data;
	}
	
	/**
	 * setData
	 * Create the list of resources to include in the html page
	 * @param simple_xml $xmlobject
	 * @param string $module
	 * @param string $controller
	 * @param string $skin [base]
	 */
	private static function setData($xmlobject, $module, $controller, $skin = "base") {
		$item = array();
		
		// Get the default project resources
		if (count ( $xmlobject )) {
			
			foreach ( $xmlobject as $resource ) {
				$item = array();
				
				if (( string ) $resource ['override'] == 1) {
					$item['resource'] = ( string ) $resource;
				} else {
					$item['resource'] = "/skins/$module/$skin" . ( string ) $resource;
				}
				
				if((string) $resource['conditional']){
					$item['conditional'] = (string) $resource['conditional'];
				}
				
				if((string) $resource['position']){
					$item['position'] = (string) $resource['position'];
				}
				
				$data [] = $item;
			}
		}
		
		if (! empty ( $data ) && is_array ( $data )) {
			self::$data = array_merge ( self::$data, $data );
			return true;
		}
		
		return false;
	}
	
	/**
	 * Get all the resources by type
	 * 
	 * @param string $module
	 * @param string $controller
	 * @param string $resourcetype [js, css]
	 * @param string $skin [base, etc..]
	 */
	public static function getResources($module, $controller, $resourcetype, $skin = "base") {
		$resources = self::getData ($module, $skin);
		self::$data = array ();

		// Check the controller action xml resources
		$action = $resources->xpath ( "$module/$controller/head/@action" );
		$action = !empty($action[0]) ? $action[0] : Null;
		 
		switch ($action) {
			case "clearallresources":
				
				// Adding only the controller resources
				$xmlobject = ! empty ( $resourcetype ) ? $resources->xpath ( "$module/$controller/head/$resourcetype" ) : null;
				self::setData ( $xmlobject, $module, $controller, $skin );
				break;
			
			case "commonsresourcesonly":
				
				// Adding only the controller resources
				$xmlobject = ! empty ( $resourcetype ) ? $resources->xpath ( "general/head/$resourcetype" ) : null;
				self::setData ( $xmlobject, $module, $controller, $skin );
				
				// Adding only the controller resources
				$xmlobject = ! empty ( $resourcetype ) ? $resources->xpath ( "$module/$controller/head/$resourcetype" ) : null;
				self::setData ( $xmlobject, $module, $controller, $skin );
				break;
				
			default:
				
				// Adding the Main resources
				$xmlobject = ! empty ( $resourcetype ) ? $resources->xpath ( "general/head/$resourcetype" ) : null;
				self::setData ( $xmlobject, $module, $controller, $skin );
					
				// Adding the Module resources
				$xmlobject = ! empty ( $resourcetype ) ? $resources->xpath ( "$module/commons/head/$resourcetype" ) : null;
				self::setData ( $xmlobject, $module, $controller, $skin );
					
				// Adding the Controller resources
				$xmlobject = ! empty ( $resourcetype ) ? $resources->xpath ( "$module/$controller/head/$resourcetype" ) : null;
				self::setData ( $xmlobject, $module, $controller, $skin );
				
				break;
		}
		
		return self::$data;
	}
	
	/**
	 * getDefaultTemplate
	 * get the default template
	 */
	public static function getDefaultTemplate($module = null, $skin = null) {
		
		$resources = self::getData ($module, $skin);
		
		$xmlobject = $resources->xpath ( "default" );
		
		if (! empty ( $xmlobject [0] ['template'] )) {
			return ( string ) $xmlobject [0] ['template'];
		}
		return "1column";
	}
	
	/**
	 * getTemplate
	 * get the default template
	 */
	public static function getTemplate($module, $controller = null, $skin = null) {
		$resources = self::getData ($module, $skin);
		
		$template = self::getDefaultTemplate ($module, $skin);
		
		$xmlobject = $resources->xpath ( "$module/$controller" );
		
		// Get first the template set for the controller page
		if (! empty ( $xmlobject [0] ['template'] )) {
			return ( string ) $xmlobject [0] ['template'];
		}
		
		$xmlobject = $resources->xpath ( $module );
		
		// Get first the template set for the module section
		if (! empty ( $xmlobject [0] ['template'] )) {
			return ( string ) $xmlobject [0] ['template'];
		}
		
		// Else the default template will be returned
		return $template;
	}
	
	/**
	 * getBlockItems
	 * Get the block items
	 * @param simple_xml $xmlobject
	 */
	private static function getBlockItems($xmlobject) {
		$data = array ();
		$i = 0;
		if (count ( $xmlobject )) {
			foreach ( $xmlobject as $blocks ) {
				foreach ( $blocks->block as $block ) {
					$data [$i] ['block'] ['name'] = ( string ) $block;
					$data [$i] ['action'] = ( string ) $block ['action'];
					$data [$i] ['side'] = ( string ) $block ['side'];
					$data [$i] ['position'] = ( string ) $block ['position'];
					$i ++;
				}
			}
		}
		
		if (! empty ( $data ) && is_array ( $data )) {
			self::$blocks = array_merge ( self::$blocks, $data );
			return true;
		}
		return false;
	}
	
	/**
	 * Update the layout using a custom xml elements 
	 * 
	 * <layout>
			<default template="fullscreen"> <!-- Template set for all the frontend pages -->
				<commons> 
					<head action="clearall">
					     <js>/resources/js/flowplayer-3.2.6.min.js</js>
		                 <js>/resources/js/wysiwyg/tiny_mce.js</js>
					</head>
		            
				</commons>
			</default>
		</layout>
		
	 * @param Zend_View $view
	 * @param String $xml
	 */
	public static function updateLayout(Zend_View $view, $xml){
		
		$action = "";
		
		// Check the xml layout string
		if(empty($xml)){
			return false;
		}
		
		// Load the xml file
		$data = simplexml_load_string($xml) ;
		
		// Get the action in the head
		$action = $data->xpath ( "default/commons/head/@action" );
		
		// Get the javascript items
		$js = $data->xpath ( "default/commons/head/js" );
		$css = $data->xpath ( "default/commons/head/css" );
		
		// Check the action in the header 
		// Action: clearall delete all the items in the js section
		if(!empty($action[0])){
			$action = (string)$action[0];
			if($action=="clearall"){
				$view->headScript()->exchangeArray(array());
				$view->headLink()->exchangeArray(array());
			}
		}
		
		self::addJs($view, $js);
		self::addCss($view, $css);
	}
	
	/**
	 * Add a JS file in the webpage
	 * @param Zend_View $view
	 * @param array $js
	 */
	private static function addJs(Zend_View $view, $js){
		
		// Custom XML file inclusion of the js files
		if (! empty ( $js )) {
			foreach ( $js as $item ) {
				$view->headScript ()->appendFile ( $item );
			}
		}
	}
	
	/**
	 * Add a CSS file in the webpage
	 * @param Zend_View $view
	 * @param array $css
	 */
	private static function addCSS(Zend_View $view, $css){
		
		// Custom XML file inclusion of the css files
		if (! empty ( $css )) {
			foreach ( $css as $item ) {
				$view->headLink ()->appendStylesheet ( $item );
			}
		}
	}
	
	/**
	 * getBlocks
	 * get the blocks
	 */
	public static function getBlocks($module, $side, $controller = null, $skin = null) {
		$resources = self::getData ($module, $skin);
		
		// Clear the array to avoid double results
		self::$blocks = array (); 
		
		// Check the controller action xml blocks
		$action = $resources->xpath ( "$module/$controller/blocks/@action" );
		
		// Check if the controller needs to exclude the common blocks
		if(!empty($action[0]) && ((string) $action[0] == "clearallblocks")){

			// Adding the controller blocks
			$xmlobject = $resources->xpath ( "$module/$controller/blocks" );
			self::getBlockItems($xmlobject);
			
		}else{
			
			// Adding all the commons blocks 
			$xmlobject = $resources->xpath ( "$module/commons/blocks" );
			self::getBlockItems($xmlobject);
			
			// Adding the controller blocks
			$xmlobject = $resources->xpath ( "$module/$controller/blocks" );
			self::getBlockItems($xmlobject);
		}
		
		return self::$blocks;
	}
}