<?php

/**
 * ProductsController
 * Manage the products table
 * @version 1.0
 */

class Admin_ProductsController extends Zend_Controller_Action {
	
	protected $products;
	protected $categories;
	protected $datagrid;
	protected $session;
	protected $translator;
	
	/**
	 * preDispatch
	 * Starting of the module
	 * (non-PHPdoc)
	 * @see library/Zend/Controller/Zend_Controller_Action#preDispatch()
	 */
	
	public function preDispatch() {
		$this->session = new Zend_Session_Namespace ( 'Admin' );
		$this->products = new Products ();
		$this->translator = Zend_Registry::getInstance ()->Zend_Translate;
		$this->datagrid = $this->_helper->ajaxgrid;
		$this->datagrid->setModule ( "products" )->setModel ( $this->products );
	}
	
	/**
	 * indexAction
	 * Create the User object and get all the records.
	 * @return unknown_type
	 */
	public function indexAction() {
		$redirector = Zend_Controller_Action_HelperBroker::getStaticHelper ( 'redirector' );
		$redirector->gotoUrl ( '/admin/products/list' );
	}

	/**
	 * Load Json Records
	 *
	 * @return string Json records
	 */
	public function loadrecordsAction() {
		$this->_helper->ajaxgrid->setConfig ( Products::grid() )->loadRecords ($this->getRequest ()->getParams());
	}
	
	/**
	 * indexAction
	 * Create the User object and get all the records.
	 * @return datagrid
	 */
	public function listAction() {
		$this->view->title = "Products list";
		$this->view->description = "Here you can see all the products.";
		$this->view->buttons = array(array("url" => "/admin/products/new/", "label" => $this->translator->translate('New'), "params" => array('css' => array('button', 'float_right'))));
		$this->datagrid->setConfig ( Products::grid () )->datagrid ();
	}
	
	/**
	 * searchProcessAction
	 * Search the record 
	 * @return unknown_type
	 */
	public function searchprocessAction() {
		$this->_helper->ajaxgrid->setConfig ( Products::grid () )->search ();
	}
	
	/*
	 *  bulkAction
	 *  Execute a custom function for each item selected in the list
	 *  this method will be call from a jQuery script 
	 *  @return string
	 */
	public function bulkAction() {
		$this->_helper->ajaxgrid->massActions ();
	}
	
	/**
	 * recordsperpage
	 * Set the number of the records per page
	 * @return unknown_type
	 */
	public function recordsperpageAction() {
		$this->_helper->ajaxgrid->setRowNum ();
	}
	
	/**
	 * newAction
	 * Create the form module in order to create a record
	 * @return unknown_type
	 */
	public function newAction() {
		$this->view->form = $this->getForm ( "/admin/products/process" );
		
		// I have to add the language id into the hidden field in order to save the record with the language selected 
		$this->view->form->populate ( array('language_id' => $this->session->langid) );
		
		$this->view->title = "Product Details";
		$this->view->description = "Here you can edit the product details";
		$this->view->buttons = array(array("url" => "#", "label" => $this->translator->translate('Save'), "params" => array('css' => array('button', 'float_right'), 'id' => 'submit')),
							   array("url" => "/admin/products/list", "label" => $this->translator->translate('List'), "params" => array('css' => array('button', 'float_right'))));
				
		$this->render ( 'applicantform' );
	}
	
	/**
	 * confirmAction
	 * Ask to the user a confirmation before to execute the task
	 * @return null
	 */
	public function confirmAction() {
		$id = $this->getRequest ()->getParam ( 'id' );
		$controller = Zend_Controller_Front::getInstance ()->getRequest ()->getControllerName ();
		try {
			if (is_numeric ( $id )) {
				$this->view->back = "/admin/$controller/edit/id/$id";
				$this->view->goto = "/admin/$controller/delete/id/$id";
				$this->view->title = $this->translator->translate ( 'Are you sure to delete this product?' );
				$this->view->description = $this->translator->translate ( 'The product will be not longer available.' );
				
				$record = $this->products->find ( $id, null, true );
				
				$this->view->recordselected = '';
                if ( isset($record [0] ['ProductsData'] [0] ['name']) ) {
	                $this->view->recordselected = $record [0] ['ProductsData'] [0] ['name'];
                }
			} else {
				$this->_helper->redirector ( 'list', $controller, 'admin', array ('mex' => $this->translator->translate ( 'Unable to process request at this time.' ), 'status' => 'error' ) );
			}
		} catch ( Exception $e ) {
			echo $e->getMessage ();
		}
	}
	
	/**
	 * delmediaAction
	 * Delete a media file
	 */
	public function delmediaAction() {
		$files = new Files ();
		$id = $this->getRequest ()->getParam ( 'id' );
		if (is_numeric ( $id )) {
			$file = ProductsMedia::getMediabyId ( $id );
			if (! empty ( $file ['filename'] )) {
				if (file_exists ( PUBLIC_PATH . $file ['path'] )) {
					if (unlink ( PUBLIC_PATH . $file ['path'] )) {
						ProductsMedia::delMediabyId ( $id );
						$this->_helper->redirector ( 'edit', 'products', 'admin', array ('id' => $file ['product_id'], 'mex' => 'The media file has been deleted.', 'status' => 'success' ) );
					} else {
						$this->_helper->redirector ( 'edit', 'products', 'admin', array ('id' => $file ['product_id'], 'mex' => 'The media file has been not deleted. Check the file permissions.', 'status' => 'error' ) );
					}
				} else {
					ProductsMedia::delMediabyId ( $id );
					$this->_helper->redirector ( 'edit', 'products', 'admin', array ('id' => $file ['product_id'], 'mex' => 'The media file has not been found but the record has been deleted', 'status' => 'attention' ) );
				}
			} else {
				$this->_helper->redirector ( 'list', 'products', 'admin', array ('mex' => 'The media file has been not deleted.', 'status' => 'error' ) );
			}
		} else {
			$this->_helper->redirector ( 'list', 'products', 'admin', array ('mex' => 'The media file has been not deleted.', 'status' => 'error' ) );
		}
	}
	
	/**
	 * deltrancheAction
	 * Delete a tranche 
	 */
	public function deltrancheAction() {
		$tranches = new ProductsTranches ();
		$id = $this->getRequest ()->getParam ( 'id' );
		if (is_numeric ( $id )) {
			$tranche = $tranches->getTranchebyId ( $id );
			if (! empty ( $tranche )) {
				$tranches->delTranchebyId ( $id );
				$this->_helper->redirector ( 'edit', 'products', 'admin', array ('id' => $tranche ['product_id'], 'mex' => 'The tranche has been deleted.', 'status' => 'success' ) );
			} else {
				$this->_helper->redirector ( 'list', 'products', 'admin', array ('mex' => 'The tranche has been not deleted.', 'status' => 'error' ) );
			}
		} else {
			$this->_helper->redirector ( 'list', 'products', 'admin', array ('mex' => 'The tranche has been not deleted.', 'status' => 'error' ) );
		}
	}
	
	/**
	 * 
	 * Delete a record previously selected by the product
	 */
	public function deleteAction() {
		$id = $this->getRequest ()->getParam ( 'id' );
		try {
			if (is_numeric ( $id )) {
				if(Products::del($id)){
					$this->_helper->redirector ( 'list', 'products', 'admin', array ('mex' => 'The task requested has been executed successfully.', 'status' => 'success' ) );		
				}else{
					$this->_helper->redirector ( 'list', 'products', 'admin', array ('mex' => 'The product is locked by a order', 'status' => 'error' ) );
				}
			}
		} catch ( Exception $e ) {
			$this->_helper->redirector ( 'list', 'products', 'admin', array ('mex' => $e->getMessage (), 'status' => 'error' ) );
		}
	}
	
	/*
	public function gettrancheAction() {
		$id = $this->getRequest ()->getParam ( 'id' );
		$trance	= ProductsTranches::getTranchebyId($id);

		$params	= array();
		$params['title']	= 'Update billing or <a href="#" onclick="return onCleanTranche()">Insert new</a>';
		$params['quantity']	= $trance['quantity'];
		$params['setupfee']	= $trance['setupfee'];
		$params['price']	= $trance['price'];
		$params['billing_cycle_id']	= $trance['billing_cycle_id'];
		$params['measurement']	= $trance['measurement'];	
		
		$params['cost']				= array();
		//$params['cost']['title']	= 			
		echo json_encode($params);
		exit();
	}*/
	
	/**
	 * editAction
	 * Get a record and populate the application form 
	 * @return unknown_type
	 */
	public function editAction() {
		$form = $this->getForm ( '/admin/products/process' );
		
		$id = $this->getRequest ()->getParam ( 'id' );
		$orders = "";
		
		// Create the buttons in the edit form
		$this->view->buttons = array(
				array("url" => "#", "label" => $this->translator->translate('Save'), "params" => array('css' => array('button', 'float_right'), 'id' => 'submit')),
				array("url" => "/admin/products/list", "label" => $this->translator->translate('List'), "params" => array('css' => array('button', 'float_right'), 'id' => 'submit')),
				array("url" => "/admin/products/new/", "label" => $this->translator->translate('New'), "params" => array('css' => array('button', 'float_right'))),
		);
		
		if (! empty ( $id ) && is_numeric ( $id )) {
			$this->session->productid = $id;
			$rs = $this->products->getAllInfo ( $id, $this->session->langid );
			
			if (! empty ( $rs )) {
				// Join the translated data information to populate the form
				$data = !empty($rs['ProductsData'][0]) ? $rs['ProductsData'][0] : array();
				$rs = array_merge($rs, $data);
				$form = $this->createAttributesElements ( $form, $rs ['group_id'] );
				
				$rs['language_id'] = $this->session->langid; // added to the form the language id selected
				$rs['related'] = ProductsRelated::getItemsbyProductID($rs ['product_id']);
				//add panel for select upgrade
				$rs['upgrade'] = ProductsUpgrades::getItemsbyProductID($rs ['product_id']);
                
				// Get the wiki pages attached to the product selected
				$rs['wikipages'] =	Wikilinks::getWikiPagesList($rs ['product_id'], "products", $this->session->langid);
				
				$form->populate ( $rs );
				
				$categories = explode ( "/", $rs ['categories'] );
				$this->view->categories = json_encode ( $this->createCategoryTree ( 0, $categories ) );
				
				$this->view->title = ! empty ( $rs ['name'] ) ? $rs ['name'] : "";
				$this->view->url = ! empty ( $rs ['uri'] ) ? $rs ['uri'] . ".html" : "";
				
				$media = ProductsMedia::getMediabyProductId ( $id, "pm.media_id, pm.filename" );
				if (isset ( $media [0] )) {
					$this->view->media = array ('records' => $media, 'delete' => array ('controller' => 'products', 'action' => 'delmedia' ) );
				}
				
				$tranches = ProductsTranches::getTranches ( $id, "tranche_id, quantity, measurement, setupfee, price, bc.name as billingcycle, selected" );
				if (isset ( $tranches [0] )) {
					$onclick	= array();
                    
                    foreach( $tranches as &$tranche ) {
                        $trancheid  = $tranche['tranche_id'];
                        $include    = ProductsTranchesIncludes::getIncludeForTrancheId( $trancheid );
                        $textInclude    = array();
                        if( array_key_exists('domains', $include) ) {
                            $textInclude[]    = "Domains: ".implode(", ",$include['domains']);
                        }
                        
                        $tranche['include']    = implode("<br/>",$textInclude);
                    }
                    
					$this->view->tranches = array (
												 'records' 	=> $tranches
												,'actions' 	=> array ( '/admin/products/setdefaultrance/id/' => 'Set as default')
												//,'onclick' 	=> array ( 'return onEditTranche([id])' => 'Edit')
												,'delete' 	=> array ('controller' => 'products', 'action' => 'deltranche' )
											);
				}
			}
			$orders = array ('records' => OrdersItems::ProductsInOrdersItems ( $id ), 'edit' => array ('controller' => 'ordersitems', 'action' => 'edit' ) );
			
			$this->view->buttons[] = array("url" => "/admin/products/confirm/id/$id", "label" => $this->translator->translate('Delete'), "params" => array('css' => array('button', 'float_right')));
				
		}
		
		$this->view->description = "Here you can edit the product details";
		$this->view->mex = $this->getRequest ()->getParam ( 'mex' );
		$this->view->mexstatus = $this->getRequest ()->getParam ( 'status' );
		$this->view->orders = $orders;
		$this->view->isSold = (bool)OrdersItems::CheckIfProductExist($id);
		
		$this->view->form = $form;
		$this->render ( 'applicantform' );
	}
	
	/**
	 * Get the categories for the product selected
	 * Pay attention with the productid session value
	 */
	public function getcategoriesAction(){
		$categories = array();

		if(!empty($this->session->productid) && is_numeric($this->session->productid)){
			$rs = $this->products->getAllInfo ( $this->session->productid, $this->session->langid );
			$categories = explode ( "/", $rs ['categories'] );
			unset($this->session->productid);
		}	
		
		die(json_encode ($this->createCategoryTree ( 0, $categories )));
	}
	
	/*
	 * createProductsCategoryTree
	 * 
	 */
	private function createProductsCategoryTree($id, $categoriesel = array()) {
		$cats = array ();
		$isfolder = false;
		$items = ProductsCategories::getbyParentId ( $id, 0 );
		foreach ( $items as $category ) {
			$subcategory = $this->createCategoryTree ( $category ['category_id'], $categoriesel );
			$isfolder = ($subcategory) ? true : false;
			$selected = in_array ( $category ['category_id'], $categoriesel ) ? true : false;
			
			if ($subcategory) {
				$expanded = in_array ( $category ['category_id'], $categoriesel ) ? true : false;
				$cats [] = array ('key' => $category ['category_id'], 'title' => $category ['name'], 'expand' => $expanded, 'select' => $selected, 'isFolder' => $isfolder, 'children' => $subcategory );
			} else {
				$cats [] = array ('key' => $category ['category_id'], 'title' => $category ['name'], 'select' => $selected );
			}
		}
		return $cats;
	}
	
	/*
	 * createCategoryTree
	 * 
	 */
	private function createCategoryTree($id, $categoriesel = array()) {
		$cats = array ();
		$isfolder = false;
		$items = ProductsCategories::getbyParentId ( $id, 0 );
		foreach ( $items as $category ) {
			$subcategory = $this->createCategoryTree ( $category ['category_id'], $categoriesel );
			$isfolder = ($subcategory) ? true : false;
			$selected = in_array ( $category ['category_id'], $categoriesel ) ? true : false;
			
			if ($subcategory) {
				$expanded = in_array ( $category ['category_id'], $categoriesel ) ? true : false;
				$cats [] = array ('key' => $category ['category_id'], 'title' => $category ['name'], 'expand' => $expanded, 'select' => $selected, 'isFolder' => $isfolder, 'children' => $subcategory );
			} else {
				$cats [] = array ('key' => $category ['category_id'], 'title' => $category ['name'], 'select' => $selected );
			}
		}
		return $cats;
	}
	
	/**
	 * setdefaultrance
	 * Set the default trance price 
	 * @return void
	 */
	public function setdefaultranceAction() {
		$id = $this->getRequest ()->getParam ( 'id' );
		if (is_numeric ( $id )) {
			$trance = ProductsTranches::getTranchebyId ( $id );
			ProductsTranches::setDefault ( $id );
			$this->_helper->redirector ( 'edit', 'products', 'admin', array ('id' => $trance ['product_id'], 'mex' => 'The task requested has been executed successfully.', 'status' => 'success' ) );
		}
		$this->_helper->redirector ( 'list', 'products', 'admin', array ('mex' => 'An error occured during the operation.', 'status' => 'error' ) );
	}
	
	/**
	 * Update the record previously selected
	 */
	public function processAction() {
		$redirector = Zend_Controller_Action_HelperBroker::getStaticHelper ( 'redirector' );
		$form = $this->getForm ( "/admin/products/process" );
		$request = $this->getRequest ();
		
		// Create the buttons in the edit form
		$this->view->buttons = array(
				array("url" => "#", "label" => $this->translator->translate('Save'), "params" => array('css' => array('button', 'float_right'), 'id' => 'submit')),
				array("url" => "/admin/products/list", "label" => $this->translator->translate('List'), "params" => array('css' => array('button', 'float_right'), 'id' => 'submit')),
				array("url" => "/admin/products/new/", "label" => $this->translator->translate('New'), "params" => array('css' => array('button', 'float_right'))),
		);

		$form = $this->createAttributesElements ( $form, $request->getParam('group_id') );

		// Check if we have a POST request
		if (! $request->isPost ()) {
			return $this->_helper->redirector ( 'list', 'products', 'admin' );
		}
		
		if ($form->isValid ( $request->getPost () )) {
			// Get the id 
			$id = $this->getRequest ()->getParam ( 'product_id' );
			
			// Get the values posted
			$params = $request->getPost ();
			
			// Save all the data 
			$id = Products::saveAll ( $id, $params, $this->session->langid );
			
			$redirector->gotoUrl ( "/admin/products/edit/id/$id" );
		} else {
			$this->view->form = $form;
			$this->view->title = "Product Details";
			$this->view->description = "Here you can edit the product details";
			return $this->render ( 'applicantform' );
		}
	}
	
	/**
	 * Create the attribute elements
	 * 
	 * 
	 * @param integer $attribute_group_id
	 */
	private function createAttributesElements($form, $group_id) {
		$attributeForm = new Zend_Form_SubForm ();
		$attributeForm->addElementPrefixPath('Shineisp_Decorator', 'Shineisp/Decorator/', 'decorator');
		if (is_numeric ( $group_id )) {
			
			// Get all the elements
			$elements = ProductsAttributesGroups::getAttributesProfiles( $group_id, $this->session->langid );
			if (! empty ( $elements [0] )) {
				foreach ( $elements as $element ) {
					if(!empty($element ['ProductsAttributes'])){
						// Check the label
						$label = (! empty ( $element ['ProductsAttributes']['ProductsAttributesData'] [0] ['label'] )) ? $element ['ProductsAttributes']['ProductsAttributesData'] [0] ['label'] : $element ['ProductsAttributes']['code'];
						$description = (!empty($element ['ProductsAttributes']['ProductsAttributesData'] [0] ['description'])) ? $element ['ProductsAttributes']['ProductsAttributesData'] [0] ['description'] : "";

						// Create the element
						$attributeForm->addElement ( $element ['ProductsAttributes']['type'], $element ['ProductsAttributes']['code'], array ('label' => $label, 'class' => 'text-input large-input', 'decorators' => array('Composite'), 'description' => $description) );
						
						if ($element ['ProductsAttributes']['is_required']) {
							$attributeForm->getElement ( $element['ProductsAttributes'] ['code'] )->setRequired ( true );
						}
						
						// Handle the default option items for the dropdown selector  
						if ($element['ProductsAttributes'] ['type'] == "select") {
							$data = ! empty ( $element['ProductsAttributes'] ['defaultvalue'] ) ? json_decode ( $element ['ProductsAttributes']['defaultvalue'], true ) : array ();
							$attributeForm->getElement ( $element['ProductsAttributes'] ['code'] )->setAllowEmpty ( false )->setRegisterInArrayValidator ( false )->setMultiOptions ( $data );
						} else {
							$attributeForm->getElement ( $element['ProductsAttributes'] ['code'] )->setValue ( $element ['ProductsAttributes']['defaultvalue'] );
						}
					}
				}
				
				$form->addSubForm ( $attributeForm, 'attributes' );
			}
		}
		
		return $form;
	}
	
	/**
	 * Get the customized application form 
	 * 
	 * 
	 * @return unknown_type
	 */
	private function getForm($action) {
		$form = new Admin_Form_ProductsForm ( array ('action' => $action, 'method' => 'post' ) );
		return $form;
	}

}