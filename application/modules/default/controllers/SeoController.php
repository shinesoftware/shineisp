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
	public function productsAction() {
		
		$ns 	= new Zend_Session_Namespace ();
		$xml 	= new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><shineisp></shineisp>');
		
		$localeID 	= $ns->idlang;
		$products 	= $xml->addChild('products');
		
		// Get all the products
		$records = Products::getAll($localeID);
		foreach ($records as $item){
			$product = $products->addChild('product');
			$product->addAttribute('uuid', $item['uuid']);
			$product->addAttribute('id', $item['product_id']);
			$product->addChild('sku', htmlentities($item['sku']));
			$product->addChild('name', !empty($item['ProductsData'][0]['name']) ? htmlentities($item['ProductsData'][0]['name']) : null);
			$product->addChild('shortdescription', !empty($item['ProductsData'][0]['shortdescription']) ? "<![CDATA[" . htmlentities($item['ProductsData'][0]['shortdescription']) . "]]>" : null);
			$product->addChild('description', !empty($item['ProductsData'][0]['description']) ? "<![CDATA[" . htmlentities($item['ProductsData'][0]['description'])."]]>" : null);
			$product->addChild('metadescription', !empty($item['ProductsData'][0]['metadescription']) ? "<![CDATA[" . htmlentities($item['ProductsData'][0]['metadescription']) ."]]>" : null);
			$product->addChild('metakeywords', !empty($item['ProductsData'][0]['metakeywords']) ? "<![CDATA[" . htmlentities($item['ProductsData'][0]['metakeywords']) ."]]>" : null);
			$product->addChild('categories', products::get_text_categories($item['categories']));
			$price = $product->addChild('price', Products::getPrice($item['product_id']));
			$price->addAttribute('taxincluded', 0);
		}
		
		header('Content-type: text/xml');
		echo $xml->asXML();
// 		$xml->saveXML(PUBLIC_PATH . "/tmp/seoproducts.xml");
#Zend_Debug::dump($records);
		die;
		
	}
}