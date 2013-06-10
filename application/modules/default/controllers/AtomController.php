<?php

class AtomController extends Shineisp_Controller_Default {
	
	/**
	 * Export the products by the Google Product Atom Export
	 * @author Shine Software
	 * @return xml 
	 */
	public function googleproductsAction() {
		
		// Calling Google Product Extension
		Zend_Feed_Writer::addPrefixPath('Shineisp_Feed_Writer_Extension_', 'Shineisp/Feed/Writer/Extension/');
		Zend_Feed_Writer::registerExtension('Google');
		
		$isp = Shineisp_Registry::get('ISP');
		
		$feed = new Zend_Feed_Writer_Feed ();
		$feed->setTitle ( $isp->company );
		$feed->setLink ( $isp->website );
		$feed->setFeedLink ( $isp->website . '/atom/products', 'atom' );
		$feed->addAuthor ( array ('name' => $isp->manager, 'email' => $isp->email, 'uri' => $isp->website ) );
		$feed->setDateModified ( time () );
		$feed->setGenerator("ShineISP Atom Extension");
		
		$products = Products::getAllRss();
// 		print_r($products);
// 		die;
		foreach ($products as $product){

			// Get the google categories
			$categories = ProductsCategories::getGoogleCategories ( $product['categories']);
			$cattype = Products::get_text_categories( $product['categories']);
			
			// Create the product entries
			$entry = $feed->createEntry();
			$entry->setTitle($product['ProductsData'][0]['name']);
			$entry->setProductType(Products::get_text_categories( $product['categories']));
			$entry->setBrand($isp->company);
			$entry->setAvailability(true);
			$entry->setLink($isp->website . "/" . $product['uri'] . ".html");

			// Custom Attributes Google Product Extension
			if(!empty($product['ProductsMedia'][0]['path'])){
				$entry->setImageLink($isp->website . str_replace(" ", "%20", $product['ProductsMedia'][0]['path']));
			}
			
			if(!empty($product['uri'])){
				$entry->setProductId($product['uri']);
			}
			
			if(!empty($categories[0]['googlecategs'])){
				$entry->setCategory($categories[0]['googlecategs']);
			}
			
			$price = Products::getPriceSuggested($product['product_id'], true);
			$entry->setPrice($price);
			$entry->setCondition('new');
			
			$entry->setDateModified(time());
			$entry->setDescription(strip_tags($product['ProductsData'][0]['shortdescription']));
			$feed->addEntry($entry);
			
		}
		
		$feed = $feed->export('atom');

		// Feed Fixing for google products
		$feed = $this->googlefixes($feed);
		
		Shineisp_Commons_Utilities::writefile($feed, "documents", "googleproducts.xml");
		die($feed);
	}
	
	/**
	 * Clean up the xml file as google requires
	 * @param string $feed
	 */
	private function googlefixes($feed){
		
		// Delete wrong attributes used by the Atom 1.0
		$feed = str_replace(" type=\"html\"", "", $feed);
		$feed = str_replace(" type=\"text\"", "", $feed);
		
		// Delete some xml childs from the xml
		$xmlfeed = new SimpleXMLElement($feed);
		foreach($xmlfeed->entry as $child)
		{
			
				
			$target = $child->id;
			if($target){
				$dom = dom_import_simplexml($target);
				$dom->parentNode->removeChild($dom);
			}
				
		}
		
		$feed = $xmlfeed->asXml();

		// removeChild method leave a blank line after the deletion of the xml child 
		$feed = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $feed);
		
		return $feed;
	}
	
}