<?php

class CategoriesController extends Shineisp_Controller_Default {
	protected $categories;
	protected $mode;
	protected $translator;
	
	/**
	 * preDispatch
	 * Starting of the module
	 * (non-PHPdoc)
	 * @see library/Zend/Controller/Shineisp_Controller_Default#preDispatch()
	 */
	
	public function preDispatch() {
		$auth = Zend_Auth::getInstance ();
		$registry = Shineisp_Registry::getInstance ();
		$this->categories = new ProductsCategories ( );
		$this->translator = $registry->Zend_Translate;
	}
	
	/**
	 * Index page
	 */
	public function indexAction() {
		return $this->_helper->redirector ( 'list' );
	}
	
	/**
	 * Set the layout mode of the list of the categories
	 */
	public function setlayoutAction() {
		$ns = new Zend_Session_Namespace ();
		$ns->layoutmode = $this->getRequest()->getParam('mode', 'list');
		return $this->_helper->redirector ( $ns->lastcategory . ".html" );
	}
	
	/*
	 * List all the products of a requested category
	 */
	public function listAction() {
		$ns = new Zend_Session_Namespace ();
		$products = array ();
		
		// get the category uri
		$uri = $this->getRequest ()->getParam ( 'q' );
		if (! empty ( $uri )) {
			
			// Save the path of the user
			$ns->lastcategory = $uri;
			
			// Get the category information
			$category = $this->categories->getAllInfobyURI ( $uri );
			if (!empty($category [0])) {
				$this->view->category = $category [0];
				
				// Get the subcategories
				$this->view->subcategory = ProductsCategories::getbyParentId($category [0]['category_id'], 1, true);
				
				// Set the Metatag information
				$this->view->headTitle()->prepend ( $category [0] ['name'] );
				if (! empty ( $category [0] ['keywords'] )) {
					$this->view->headMeta ()->setName ( 'keywords', $category [0] ['keywords'] );
				}
				
				if (! empty ( $category [0] ['description'] )) {
					$this->view->headMeta ()->setName ( 'description', $category [0] ['description'] ? Shineisp_Commons_Utilities::truncate ( strip_tags ( $category [0] ['description'] ) ) : '-' );
				}
				
				$this->view->headertitle = $category [0] ['name'];
				
				// Get the products information
				$fields = "pd.productdata_id as productdata_id, 
				           pd.name as name, 
				           pd.shortdescription as shortdescription, 
				           pd.metakeywords as metakeywords, 
				           pd.metadescription as metadescription, 
				           p.*, pag.code as groupcode";
				
				$data = $this->categories->getProductListbyCatUri ( $uri, $fields, $ns->langid );
				if (!empty($data['records'])) {
					
					// Get the media information for each product
					foreach ( $data['records'] as $product ) {
						$product['reviews']    = Reviews::countItems($product['product_id']);
						$product['attributes'] = ProductsAttributes::getAttributebyProductID($product['product_id'], $ns->langid, true);
						$products [] = $product;
					}
					$this->view->products = $products;
					$this->view->pager = $data['pager'];
				}
				
				$this->view->layoutmode = !empty($ns->layoutmode) ? $ns->layoutmode : "list";
				
				$this->_helper->viewRenderer($ns->layoutmode);
				
			}else{
				$this->_helper->redirector ( 'index', 'index', 'default' );
			}
		}
	}
}