<?php
/**
 * 
 * Import themes within ShineISP
 * @author shinesoftware
 *
 */
class Shineisp_Api_Themes_Templatemonster_Main {
	
	private $config;
	private $doc;
	private $xml;
	private $imgprefix;
	private $credencials;
	private $groupattributesID;
	
	/**
	 * construct of the class
	 */
	public function __construct() {
		$this->doc = new DOMDocument ();
		$this->xml = new XMLReader ();
		set_time_limit ( 0 );
		ini_set ( 'memory_limit', '256M' );
		$this->load ();
		$this->imgprefix = Settings::findbyParam ( "templatemonster_imageprefix", "admin", Isp::getActiveISPID () );
		$login = Settings::findbyParam ( "templatemonster_login", "admin", Isp::getActiveISPID () );
		$webapipassword = Settings::findbyParam ( "templatemonster_webapipassword", "admin", Isp::getActiveISPID () );
		$this->credencials = "&login=$login&webapipassword=$webapipassword";
		echo "Start process\n";
		$this->update_templates ();
	}
	
	/**
	 * Update the templates
	 * 
	 * 
	 * @throws Exception
	 */
	private function update_templates() {
		
		$fromdate = Settings::findbyParam ( "templatemonster_lastupdate", "admin", Isp::getActiveISPID () );
		try {
			if (! empty ( $fromdate ) && Shineisp_Commons_Utilities::isDate ( $fromdate )) {
				echo "Download template update\n";
				$uri = "http://www.templatemonster.com/webapi/template_updates.php?from=$fromdate&to=" . date ( 'Y-m-d H:i:s' ) . $this->credencials;
				$uri = str_replace ( " ", "%20", $uri );
				
				$path = PUBLIC_PATH . "/imports/";
				@mkdir ( $path, 0777 );

				$xml = file_get_contents($uri);
				file_put_contents($path . "t_info_updates.xml", $xml);

				if (file_exists ( $path . "t_info_updates.xml" )) {
					if (file_exists ( $path . "t_info_updates.xml" )) {
						$this->read ( $path . "t_info_updates.xml" );
					}
				} else {
					throw new Exception ( "t_info_updates.xml has been not downloaded" );
				}
			} else {
				echo "Download template database\n";
				$this->download_templates ();
			}
			
			echo "Update ShineISP settings\n";
			
			// Update the date 
			Settings::saveSetting ( "templatemonster_lastupdate", date ( 'Y-m-d H:i:s' ) );
		} catch ( Exception $e ) {
			throw new Exception ( $e->getMessage () );
		}
	}
	
	/**
	 * 
	 * Download the template from Template Monster
	 */
	private function download_templates() {
		try {
			$path = PUBLIC_PATH . "/imports/";
			@mkdir ( $path, 0777 );
			if (file_exists ( $path . "t_info.xml" )) {
				$this->read ( $path . 't_info.xml' );
			} else {
				$zipfile = file_get_contents ( 'http://www.templatemonster.com/webapi/xml/t_info.zip' );
				$f = fopen ( $path . 't_info.zip', 'w' );
				fwrite ( $f, $zipfile );
				fclose ( $f );
				if (file_exists ( $path . "t_info.zip" )) {
					Shineisp_Commons_Utilities::unZip ( $path . "t_info.zip", $path );
					if (file_exists ( $path . "t_info.xml" )) {
						$this->read ( $path . "t_info.xml" );
					}
				} else {
					throw new Exception ( "t_info.zip has been not downloaded" );
				}
			}
		} catch ( Exception $e ) {
			throw new Exception ( $e->getMessage () );
		}
	}
	
	/**
	 * Load the configuration file
	 */
	private function load() {
		$path = PROJECT_PATH . "/library/Shineisp/Api/Themes/Templatemonster/";
		if (file_exists ( $path . "config.xml" )) {
			$this->config = simplexml_load_file ( $path . "config.xml" );
		}
	}
	
	/**
	 * Post a request to the server
	 * 
	 * @param string $uri
	 * @param array $vars
	 */
	private function get($uri) {
		$client = new Zend_Http_Client ();
		$client->setHeaders ( 'UserAgent', 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.3) Gecko/20070309 Firefox/2.0.0.3' );
		$client->setUri ( $uri );
		$retval = $client->request ( Zend_Http_Client::GET );
		$body = $retval->getBody ();
		if ($body == "Unauthorized usage") {
			throw new Exception ( "MonsterTemplate.com error message: " . $body );
		}
		return $retval->getBody ();
	}
	
	/**
	 * Read the xml file
	 * 
	 * @param $xmlfile
	 * @return $XMLReader
	 */
	private function read($xmlfile) {
		$this->xml->open ( $xmlfile, null, LIBXML_NOERROR | LIBXML_NOWARNING );
	}
	
	/**
	 * Parse the xml file
	 */
	public function parse() {
		$this->groupattributesID = $this->addAttributes ();
		@mkdir ( PUBLIC_PATH . "/media", 0777 );
		@mkdir ( PUBLIC_PATH . "/media/products/", 0777 );
		
		// Add the category
		echo "Save all the categories\n";
		$this->addCategories ();
		
		$z = $this->xml;
		
		while ( $z->read () && $z->name !== 'template' );
		
		while ( $z->name === 'template' ) {
			
			$node = simplexml_import_dom ( $this->doc->importNode ( $z->expand (), true ) );
			$type = ( string ) $node->template_type->type_name;
			
			if ($type == "Magento Themes") {
				ob_flush();
				echo "Save #" . $node ['id'] . "\n";
				self::save ( $node ); // Save data
			}
			
			// go to next <template />
			$z->next ( 'template' );
		}
		die ( 'Import Completed' );
	}
	
	/**
	 * Save the data
	 */
	private function save($node) {
		$product_id = "";
		$keywords = "template magento";
		$keywords .= ( string ) $node->keywords;
		$keywords = str_replace ( " ", ", ", $keywords );
		
		$product = Products::getProductByExternalId ( $node ['id'] );
		
		if (! empty ( $product )) {
			$product_id = $product ['product_id'];
		}

		// Get the custom description of the templates
		$description = Settings::findbyParam ( "templatemonster_description", "admin", Isp::getActiveISPID () );
		if(empty($description)){
			$description = "This template can be used in this categories: {keywords}";
		}
		
		// Replace all the keywords
		foreach ( $node->children () as $key => $n ) {
			$description = str_replace("{" . $key . "}", ( string ) $node->$key, $description);
		}
		
		$params ['name'] = "Magento Template #" . $node ['id'];
		$params ['nickname'] = "Template #" . $node ['id'];
		$params ['shortdescription'] = "<p>Magento Standard Template</p>";
		$params ['description'] = $description;
		
		$link = $this->get_demo_link ( $node->screenshots_list );
		if (! empty ( $link )) {
			$params ['description'] .= "<p>Fare click nel bottone per visualizzare una demo del template: <a href='" . $link . "' target='_blank'>Demo</a></p>";
		}
		
		$params ['categories'] = $this->getCategories ( $node->categories );
		$params ['uri'] = "Magento Template " . $node ['id'];
		$params ['metakeywords'] = $keywords;
		$params ['metadescription'] = "Magento Standard Template. ";
		$params ['cost'] = ( string ) $node->price;
		$params ['price_1'] = ( string ) $node->price;
		$params ['setupfee'] = 0;
		$params ['enabled'] = 1;
		$params ['group_id'] = $this->groupattributesID;
		$params ['tax_id'] = 1;
		$params ['language_id'] = 1;
		$params ['external_id'] = (string)$node ['id'];
		
		$productId = Products::saveAll ( $product_id, $params );
		
		$this->saveAttributes ( $node, $productId );
		$this->screenshots ( $node->screenshots_list, $productId );
	}
	
	/**
	 * Demo link
	 * 
	 * 
	 * @param unknown_type $node
	 */
	private function get_demo_link($node) {
		foreach ( $node->children () as $n ) {
			if (! empty ( $n->uri )) {
				$link = basename ( ( string ) $n->uri );
				if (substr ( $link, - 13 ) == "-magento.html") {
					return ( string ) $n->uri;
				}
			}
		}
		
		return null;
	}
	
	/**
	 * Add a new category
	 * 
	 * @param xml $node
	 */
	private function addCategories() {
		
		$uri = "http://www.templatemonster.com/webapi/categories.php?locale=it&delim=:" . $this->credencials;
		$cats = $this->get ( $uri );
		
		$categ = ProductsCategories::getCategoryByURI ( 'templates' );
		if (! empty ( $categ )) {
			$mainCat = $categ ['category_id'];
		} else {
			$mainCat = ProductsCategories::addNew ( "Templates", "templates", null, null, null, null, null, null, "istemplatecat" );
		}
		
		if (! empty ( $cats )) {
			$categories = explode ( "\r\n", $cats );
			foreach ( $categories as $cat ) {
				$category = explode ( ":", $cat );
				if (! empty ( $category [1] )) {
					$subCat = ProductsCategories::getbyExtenalId ( $category [0] );
					if (empty ( $subCat )) {
						ProductsCategories::addNew ( $category [1], $category [1], $mainCat, 1, null, null, null, $category [0], "istemplatecat" );
					}
				}
			}
		}
	}
	
	/**
	 * download the image
	 * 
	 * @param xmlnode $node
	 */
	private function screenshots($node, $productId) {
		foreach ( $node->children () as $n ) {
			$ext = pathinfo ( ( string ) $n->uri, PATHINFO_EXTENSION );
			
			$filename = basename ( ( string ) $n->uri );
			if ($ext == "jpg" || $ext == "png" || $ext == "gif") {
				if (strpos ( $filename, "banner" ) === false) { // Exclude the banner image
					$prefix = ! empty ( $this->imgprefix ) ? $this->imgprefix : "template-";
					if ((substr ( $filename, - 8 ) == "-m-b.jpg") || (substr ( $filename, - 7 ) == "-rs.jpg")) {
						
						// Save the file
						$fp = fopen ( PUBLIC_PATH . '/media/products/' . $prefix . $filename, 'w' );
						fwrite ( $fp, @file_get_contents ( $n->uri ) );
						fclose ( $fp );
						
						// Check if the file is the main picture
						$default = (strpos ( $filename, "-rs" ) > 0) ? true : false;
						
						// Check if the file is already saved
						$filesaved = ProductsMedia::getMediabyFilename ( $filename );
						if (empty ( $filesaved )) {
							ProductsMedia::addMedia ( $prefix . $filename, "", $productId, $default );
						}
					}
				}
			}
		}
	}
	
	/**
	 * Save the templates product attributes
	 */
	private function saveAttributes($node, $productId) {
		$i = 0;
		
		// Get the attributes set in the xml config file
		$attributes = $this->config->attrs;
		
		if (! empty ( $attributes ) && is_object ( $attributes )) {
			
			$params ['group_id'] = ProductsAttributesGroups::getGroupByProductId ( $productId );
			
			foreach ( $attributes->children () as $attribute ) {
				$nodename = ( string ) $attribute ['node'];
				$code = Shineisp_Commons_UrlRewrites::format ( ( string ) $attribute );
				
				// Check if the attribute exists
				$attr = ProductsAttributes::getAttributebyCode ( $code );
				
				if (! empty ( $attr )) {
					$params [$code] = ( string ) $node->$nodename;
					$i ++;
				}
			}
			
			ProductsAttributesIndexes::saveAll ( $params, $productId );
		}
	}
	
	/**
	 * 
	 * Add the templates product attributes
	 */
	private function addAttributes() {
		$ids = array ();
		
		// Get the attributes set in the xml config file
		$attributes = $this->config->attrs;
		
		if (! empty ( $attributes ) && is_object ( $attributes )) {
			
			// Get the attribute groupname
			if (! empty ( $attributes ['attrgroup'] )) {
				$group = $attributes ['attrgroup'];
			} else {
				$group = "No Name";
			}
			
			$pgroup = ProductsAttributesGroups::findbyCode ( Shineisp_Commons_UrlRewrites::format ( $group ) );
			if (! empty ( $pgroup [0] )) {
				$group_id = ProductsAttributesGroups::addNew ( $pgroup [0] ['group_id'], $group );
			} else {
				$group_id = ProductsAttributesGroups::addNew ( null, $group );
			}
			
			foreach ( $attributes->children () as $attribute ) {
				
				$code = ( string ) $attribute;
				$label = ( string ) $attribute;
				$type = ! empty ( $attribute ['type'] ) ? $attribute ['type'] : "string";
				
				// Check if the attribute exists
				$attr = ProductsAttributes::getAttributebyCode ( Shineisp_Commons_UrlRewrites::format ( $code ) );
				
				if (! empty ( $attr [0] )) {
					$ids [] = ProductsAttributes::addNew ( $attr [0] ['attribute_id'], $code, $label, $type );
				} else {
					$ids [] = ProductsAttributes::addNew ( null, $code, $label, $type );
				}
			}
			
			// Delete the old group
			ProductsAttributesGroupsIndexes::deleteAllAttributes ( $group_id );
			
			if (! empty ( $ids )) {
				ProductsAttributesGroupsIndexes::AddAttributes ( $group_id, $ids );
			}
		}
		
		return $group_id;
	}
	
	/**
	 * Get the categories
	 * 
	 * @param xml node $categories
	 */
	private function getCategories($categories) {
		$ids = array ();
		foreach ( $categories->children () as $category ) {
			$cat = ProductsCategories::getbyExtenalId ( ( string ) $category->category_id );
			if (! empty ( $cat )) {
				$ids [] = $cat [0] ['parent'];
				$ids [] = $cat [0] ['category_id'];
			}
		}
		$ids = array_unique ( $ids );
		return implode ( "/", $ids );
	}
}
