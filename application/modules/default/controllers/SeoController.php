<?php

/**
 * Creation of the sitemap in xml format
 * and extraction fo the data from the database
 * 
 * @author shinesoftware
 *
 */

class SeoController extends Shineisp_Controller_Default {

	/**
	 * preDispatch
	 * Starting of the module
	 * (non-PHPdoc)
	 * @see library/Zend/Controller/Shineisp_Controller_Default#preDispatch()
	 */
	
	public function preDispatch() {
		$this->getHelper ( 'layout' )->setLayout ( '1column' );
	}

	/**
	 * Create the sitemap for customers
	 */
	public function indexAction() {
		
	}
	
	public function productsAction() {
		
		$ns 	= new Zend_Session_Namespace ();
		$xml 	= new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><shineisp></shineisp>');
		
		$localeID 	= $ns->idlang;
		$products 	= $xml->addChild('products');
		
		try{
			// Get all the products
			$records = Products::getAll(null,$localeID);
			
			if(!empty($records)){
				foreach ($records as $item){
					
					$item['ProductsData'][0]['shortdescription'] = strip_tags($item['ProductsData'][0]['shortdescription']);
					$item['ProductsData'][0]['description'] = strip_tags($item['ProductsData'][0]['description']);
					
					$item['ProductsData'][0]['shortdescription'] = html_entity_decode($item['ProductsData'][0]['shortdescription'], ENT_NOQUOTES, "UTF-8");
					$item['ProductsData'][0]['description'] = html_entity_decode($item['ProductsData'][0]['description'], ENT_NOQUOTES, "UTF-8");
					
					$item['ProductsData'][0]['shortdescription'] = str_replace("&", "", $item['ProductsData'][0]['shortdescription']);
					$item['ProductsData'][0]['description'] = str_replace("&", "", $item['ProductsData'][0]['description']);
					
					$categories = products::get_text_categories($item['categories']);
					$categories = htmlspecialchars($categories);
					
					$product = $products->addChild('product');
					$product->addAttribute('uuid', $item['uuid']);
					$product->addAttribute('id', $item['product_id']);
					$product->addAttribute('inserted_at', !empty($item ['inserted_at']) ? strtotime($item ['inserted_at']) : null);
					$product->addAttribute('updated_at', !empty($item ['updated_at']) ? strtotime($item ['updated_at']) : null);
					$product->addChild('sku', htmlentities($item['sku']));
					
					if(!empty($item['ProductsMedia'][0]['path']) && file_exists(PUBLIC_PATH . $item['ProductsMedia'][0]['path'])){
						$product->addChild('image', "http://" . $_SERVER['HTTP_HOST'] . $item['ProductsMedia'][0]['path']);
					}
					
					$product->addChild('name', !empty($item['ProductsData'][0]['name']) ? $item['ProductsData'][0]['name'] : null);
					$product->addChild('shortdescription', !empty($item['ProductsData'][0]['shortdescription']) ? "<![CDATA[" . $item['ProductsData'][0]['shortdescription'] . "]]>" : null);
					$product->addChild('description', !empty($item['ProductsData'][0]['description']) ? "<![CDATA[" . $item['ProductsData'][0]['description'] . "]]>" : null);
					$product->addChild('categories', $categories);
					$price = $product->addChild('price', Products::getPrice($item['product_id']));
					$price->addAttribute('taxincluded', 0);
					$price->addAttribute('isrecurrent', products::isRecurring($item['product_id']));
					$price->addAttribute('currency', Settings::findbyParam('currency'));
				}
			}

			header('Content-Type: text/xml; charset=utf-8');
			die( $xml->asXML());
			
		}catch(Exception $e){
			Shineisp_Commons_Utilities::log(__CLASS__ . " " . $e->getMessage());
			die;
		}
	}
}