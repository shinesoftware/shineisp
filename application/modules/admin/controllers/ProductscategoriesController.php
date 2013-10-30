<?php

/**
 * ProductscategoriesController
 * Manage the product category table
 * @version 1.0
 */

class Admin_ProductscategoriesController extends Shineisp_Controller_Admin {
	
	protected $productscategories;
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
		$this->productscategories = new ProductsCategories ();
		$this->translator = Shineisp_Registry::getInstance ()->Zend_Translate;
		$this->datagrid = $this->_helper->ajaxgrid;
		$this->datagrid->setModule ( "productscategories" )->setModel ( $this->productscategories );		
	}
	
	/**
	 * indexAction
	 * Create the User object and get all the records.
	 * @return unknown_type
	 */
	public function indexAction() {
		$redirector = Zend_Controller_Action_HelperBroker::getStaticHelper ( 'redirector' );
		$redirector->gotoUrl ( '/admin/productscategories/list' );
	}

	/**
	 * indexAction
	 * Create the User object and get all the records.
	 * @return datagrid
	 */
	public function listAction() {
		$this->view->title = $this->translator->translate("Product Category list");
		$this->view->description = $this->translator->translate("Here you can see all the product categories.");
		$this->view->buttons = array(array("url" => "/admin/productscategories/new/", "label" => $this->translator->translate('New'), "params" => array('css' => null)));
		$this->datagrid->setConfig ( ProductsCategories::grid() )->datagrid ();
	}

	/**
	 * Load Json Records
	 *
	 * @return string Json records
	 */
	public function loadrecordsAction() {
		$this->_helper->ajaxgrid->setConfig ( ProductsCategories::grid() )->loadRecords ($this->getRequest ()->getParams());
	}
	
	/**
	 * searchProcessAction
	 * Search the record 
	 * @return unknown_type
	 */
	public function searchprocessAction() {
		$this->_helper->ajaxgrid->setConfig ( ProductsCategories::grid() )->search ();
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
		$this->view->form = $this->getForm ( "/admin/productscategories/process" );
		$this->view->title = $this->translator->translate("New Category");
		$this->view->description = $this->translator->translate("Add here a new product category");
		$this->view->buttons = array(array("url" => "#", "label" => $this->translator->translate('Save'), "params" => array('css' => null,'id' => 'submit')),
									 array("url" => "/admin/productscategories/list", "label" => $this->translator->translate('List'), "params" => array('css' => null)));
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
                $this->view->title = $this->translator->translate ( 'Do you want delete this category?' );
                $this->view->description = $this->translator->translate ( 'If you delete this record, the category will no longer be available.' );
                
                $record = $this->productscategories->find ( $id);
                $this->view->recordselected = $record [0] ['name'];
            } else {
                $this->_helper->redirector ( 'list', $controller, 'admin', array ('mex' => $this->translator->translate ( 'Unable to process the request at this time.' ), 'status' => 'error' ) );
            }
        } catch ( Exception $e ) {
            echo $e->getMessage ();
        }
    }	
	
	/**
	 * deleteAction
	 * Delete a record previously selected by the category
	 * @return unknown_type
	 */
	public function deleteAction() {
		$id = $this->getRequest ()->getParam ( 'id' );
		try {
			$this->productscategories->find ( $id )->delete ();
		} catch ( Exception $e ) {
			$this->_helper->redirector ( 'list', 'productscategories', 'admin', array ('mex' => $this->translator->translate ( 'Unable to process the request at this time.' ) . ": " . $e->getMessage (), 'status' => 'error' ) );
		}
		return $this->_helper->redirector ( 'list', 'productscategories', 'admin' );
	}
	
	/**
	 * editAction
	 * Get a record and populate the application form 
	 * @return unknown_type
	 */
	public function editAction() {
		$form = $this->getForm ( '/admin/productscategories/process' );
		$id = $this->getRequest ()->getParam ( 'id' );
		$Session = new Zend_Session_Namespace ( 'Admin' );
		$prodselected = array();
		
		// Create the buttons in the edit form
		$this->view->buttons = array(
				array("url" => "#", "label" => $this->translator->translate('Save'), "params" => array('css' => null,'id' => 'submit')),
				array("url" => "/admin/productscategories/list", "label" => $this->translator->translate('List'), "params" => array('css' => null,'id' => 'submit')),
				array("url" => "/admin/productscategories/new/", "label" => $this->translator->translate('New'), "params" => array('css' => null)),
		);
		
		if (! empty ( $id ) && is_numeric ( $id )) {
			$rs = $this->productscategories->getAllInfo ( $id );
			
			if (! empty ( $rs  )) {

				// Get the products selected
				foreach ($rs ['products'] as $product) {
					$prodselected[] = $product['product_id'];
				}
				
				// Get the wiki pages attached to the product selected
				$rs ['wikipages'] =	Wikilinks::getWikiPagesList($rs ['category_id'], "categories", $Session->langid);
				$rs ['products'] = $prodselected;
				$form->populate ( $rs );	
				
				$this->view->buttons[] = array("url" => "/admin/productscategories/confirm/id/$id", "label" => $this->translator->translate('Delete'), "params" => array('css' => null));
				
			}
		}
		
		$this->view->title = $this->translator->translate("Edit Category");
		$this->view->description = $this->translator->translate("Here you can edit the product category.");
		
		$this->view->mex = $this->getRequest ()->getParam ( 'mex' );
		$this->view->mexstatus = $this->getRequest ()->getParam ( 'status' );
		
		$this->view->form = $form;
		$this->render ( 'applicantform' );
	}
	
	/**
	 * processAction
	 * Update the record previously selected
	 * @return unknown_type
	 */
	public function processAction() {
		$i = 0;
		$redirector = Zend_Controller_Action_HelperBroker::getStaticHelper ( 'redirector' );
		$form = $this->getForm ( "/admin/productscategories/process" );
		$request = $this->getRequest ();
		
		// Create the buttons in the edit form
		$this->view->buttons = array(
				array("url" => "#", "label" => $this->translator->translate('Save'), "params" => array('css' => null,'id' => 'submit')),
				array("url" => "/admin/productscategories/list", "label" => $this->translator->translate('List'), "params" => array('css' => null,'id' => 'submit')),
				array("url" => "/admin/productscategories/new/", "label" => $this->translator->translate('New'), "params" => array('css' => null)),
		);
		
		// Check if we have a POST request
		if (! $request->isPost ()) {
			return $this->_helper->redirector ( 'list', 'productscategories', 'admin' );
		}
		
		if ($form->isValid ( $request->getPost () )) {
			// Get the id 
			$id = $this->getRequest ()->getParam ( 'category_id' );
			
			// Get the values posted
			$params = $form->getValues ();
			try {
				$id = ProductsCategories::SaveAll($id, $params);
				
				// Attach the wiki pages to the category
				Wikilinks::addWikiPages2Categories( $id, $params['wikipages'] );

				// Attach the products
				Products::add2category($id, $params['products']);
				
				$this->_helper->redirector ( 'edit', 'productscategories', 'admin', array ('id' => $id, 'mex' => 'The task requested has been executed successfully.', 'status' => 'success' ) );
			
			} catch ( Exception $e ) {
				$this->_helper->redirector ( 'edit', 'productscategories', 'admin', array ('id' => $id, 'mex' => $this->translator->translate ( 'Unable to process the request at this time.' ) . ": " . $e->getMessage (), 'status' => 'error' ) );
			}
			
			$redirector->gotoUrl ( "/admin/productscategories/edit/id/$id" );
		} else {
			$this->view->form = $form;
			$this->view->title = $this->translator->translate("Edit Category");
			$this->view->description = $this->translator->translate("Here you can edit the product category.");
			return $this->render ( 'applicantform' );
		}
	}
	
	/**
	 * getForm
	 * Get the customized application form 
	 * @return unknown_type
	 */
	private function getForm($action) {
		$form = new Admin_Form_ProductsCategoriesForm ( array ('action' => $action, 'method' => 'post' ) );
		return $form;
	}
}