<?php

/**
 * InvoicesController
 * Manage the invoices
 * @version 1.0
 */

class Admin_InvoicesController extends Zend_Controller_Action {
	
	protected $invoices;
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
		$this->invoices = new Invoices ();
		$this->translator = Zend_Registry::getInstance ()->Zend_Translate;
		$this->datagrid = $this->_helper->ajaxgrid;
		$this->datagrid->setModule ( "invoices" )->setModel ( $this->invoices );		
	}
	
	/**
	 * indexAction
	 * Create the User object and get all the records.
	 * @return unknown_type
	 */
	public function indexAction() {
		$this->_helper->redirector ( 'list', 'invoices', 'admin' );
	}
	
	/**
	 * indexAction
	 * Create the User object and get all the records.
	 * @return datagrid
	 */
	public function listAction() {
		$this->view->title = $this->translator->translate("Invoice list");
		$this->view->description = $this->translator->translate("Here you can see all the invoices.");
		$this->view->buttons = array(array("url" => "/admin/invoices/new/", "label" => $this->translator->translate('New'), "params" => array('css' => array('button', 'float_right'))));
		$this->datagrid->setConfig ( Invoices::grid() )->datagrid ();
	}
	
	/**
	 * Load Json Records
	 *
	 * @return string Json records
	 */
	public function loadrecordsAction() {
		$this->_helper->ajaxgrid->setConfig ( Invoices::grid() )->loadRecords ($this->getRequest ()->getParams());
	}
	
	/**
	 * searchProcessAction
	 * Search the record 
	 * @return unknown_type
	 */
	public function searchprocessAction() {
		$this->_helper->ajaxgrid->setConfig ( Invoices::grid() )->search ();
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
		
		$this->view->form = $this->getForm ( "/admin/invoices/process" );
		$this->view->title = $this->translator->translate("New Invoice");
		$this->view->description = $this->translator->translate("Create a new invoice using this form.");
		$this->view->buttons = array(array("url" => "#", "label" => $this->translator->translate('Save'), "params" => array('css' => array('button', 'float_right'), 'id' => 'submit')),
							   array("url" => "/admin/invoices/list", "label" => $this->translator->translate('List'), "params" => array('css' => array('button', 'float_right'))));
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
				$this->view->title = $this->translator->translate ( 'Are you sure to delete the invoice selected?' );
				$this->view->description = $this->translator->translate ( 'The invoice will not be longer available' );
				$record = $this->invoices->find ( $id );
				$this->view->recordselected = $record ['number'] . " - " . Shineisp_Commons_Utilities::formatDateOut ( $record ['invoice_date'] );
			} else {
				$this->_helper->redirector ( 'list', $controller, 'admin', array ('mex' => $this->translator->translate ( 'Unable to process request at this time.' ), 'status' => 'error' ) );
			}
		} catch ( Exception $e ) {
			echo $e->getMessage ();
		}
	
	}
	
	/**
	 * confirmOverwriteAction
	 * Ask to the user a confirmation before overwriting an invoice
	 * @return null
	 */
	public function confirmoverwriteAction() {
		$id = $this->getRequest ()->getParam ( 'id' );
		$controller = Zend_Controller_Front::getInstance ()->getRequest ()->getControllerName ();
		try {
			if (is_numeric ( $id )) {
				$this->view->back = "/admin/$controller/edit/id/$id";
				$this->view->goto = "/admin/$controller/overwrite/id/$id";
				$this->view->title = $this->translator->translate ( 'Are you sure to overwrite the invoice selected?' );
				$this->view->description = $this->translator->translate ( 'The invoice will be overwritten with current customer/product info.' );
				$record = $this->invoices->find ( $id );
				$this->view->recordselected = $record ['number'] . " - " . Shineisp_Commons_Utilities::formatDateOut ( $record ['invoice_date'] );
				
				$this->render ( 'confirm' );
				
			} else {
				$this->_helper->redirector ( 'list', $controller, 'admin', array ('mex' => $this->translator->translate ( 'Unable to process request at this time.' ), 'status' => 'error' ) );
			}
		} catch ( Exception $e ) {
			echo $e->getMessage ();
		}
	
	}
	
	
	/**
	 * deleteAction
	 * Delete a record previously selected by the order
	 * @return unknown_type
	 */
	public function deleteAction() {
		$files = new Files ( );
		$id = $this->getRequest ()->getParam ( 'id' );
		try {
			if (is_numeric ( $id )) {
				
				// Deleting the order
				$this->invoices->find ( $id )->delete ();
			}
		} catch ( Exception $e ) {
			die ( $e->getMessage () . " " . $e->getTraceAsString () );
		}
		
		return $this->_helper->redirector ( 'index', 'invoices' );
	}
	
	/**
	 * getproductinfo
	 * Get product info
	 * @return Json
	 */
	public function getproductinfoAction() {
		$id = $this->getRequest ()->getParam ( 'id' );
		$product = Doctrine::getTable ( 'Products' )->find ( $id )->toArray ();
		die ( json_encode ( $product ) );
	}
	
	/**
	 * editAction
	 * Get a record and populate the application form 
	 * @return unknown_type
	 */
	public function editAction() {
		$form = $this->getForm ( '/admin/invoices/process' );
		$form->getElement ( 'save' )->setLabel ( 'Update' );
		$id = $this->getRequest ()->getParam ( 'id' );
		
		if (! empty ( $id ) && is_numeric ( $id )) {
			$rs = $this->invoices->find ( $id )->toArray ();
			if (! empty ( $rs )) {
				$rs ['invoice_date'] = Shineisp_Commons_Utilities::formatDateOut ( $rs ['invoice_date'] );
				
				$parent = Customers::find ( $rs ['customer_id'] );
				
				//if customer comes from reseller
				if ($parent  ['parent_id']) {
					$rs ['customer_parent_id'] = $parent  ['parent_id'];
				} else {
					$rs ['customer_parent_id'] = $rs ['customer_id'];
				}
				
				// Create the buttons in the edit form
				$this->view->buttons = array(
						array("url" => "#", "label" => $this->translator->translate('Save'), "params" => array('css' => array('button', 'float_right'), 'id' => 'submit')),
						array("url" => "/admin/invoices/dropboxit/id/$id", "label" => $this->translator->translate('Dropbox It'), "params" => array('css' => array('button', 'float_right'))),
						array("url" => "/admin/invoices/confirm/id/$id", "label" => $this->translator->translate('Delete'), "params" => array('css' => array('button', 'float_right'))),
						array("url" => "/admin/invoices/list", "label" => $this->translator->translate('List'), "params" => array('css' => array('button', 'float_right'), 'id' => 'submit')),
						array("url" => "/admin/invoices/new/", "label" => $this->translator->translate('New'), "params" => array('css' => array('button', 'float_right'))),
				);
				
				// Check if the order has been invoiced
				$this->view->buttons[] = array("url" => "/admin/orders/sendinvoice/id/$id", "label" => $this->translator->translate('Email invoice'), "params" => array('css' => array('button', 'float_right')));
				$this->view->buttons[] = array("url" => "/admin/invoices/print/id/$id", "label" => $this->translator->translate('Print invoice'), "params" => array('css' => array('button', 'float_right')));
				$this->view->buttons[] = array("url" => "/admin/invoices/confirmoverwrite/id/$id", "label" => $this->translator->translate('Overwrite invoice'), "params" => array('css' => array('button', 'float_right')));
				$this->view->buttons[] = array("url" => "/admin/orders/edit/id/".$rs ['order_id'], "label" => $this->translator->translate('Order'), "params" => array('css' => array('button', 'float_right')));
				
				$form->populate ( $rs );
			}
		}
		$this->view->title = $this->translator->translate("Invoice Edit");
		$this->view->description = $this->translator->translate("Here you can edit the selected order.");
		
		$this->view->mex = urldecode ( $this->getRequest ()->getParam ( 'mex' ) );
		$this->view->mexstatus = $this->getRequest ()->getParam ( 'status' );
		
		$this->view->form = $form;
		$this->render ( 'applicantform' );
	}
	
	/**
	 * Upload an invoice to the dropbox account
	 * 
	 * @return void
	 */
	public function dropboxitAction() {
		$request = Zend_Controller_Front::getInstance ()->getRequest ();
		if (is_numeric ( $request->id )) {
			$sent = Invoices::DropboxIt( $request->id );
			if ($sent) {
				$this->_helper->redirector ( 'edit', 'invoices', 'admin', array ('id' => $request->id, 'mex' => $this->translator->translate ( 'The invoice has been uploaded in dropbox.' ), 'status' => 'success' ) );
			} else {
				$this->_helper->redirector ( 'edit', 'invoices', 'admin', array ('id' => $request->id, 'mex' => $this->translator->translate ( 'There was a problem during the process.' ), 'status' => 'error' ) );
			}
		}
	}
	
	/**
	 * createinvoiceAction
	 * Create an invoice reference
	 * @return void
	 */
	public function createinvoiceAction() {
		$request = Zend_Controller_Front::getInstance ()->getRequest ();
		if (is_numeric ( $request->id )) {
			$invoiceID = Invoices::Create ( $request->id );
			if (is_numeric ( $invoiceID )) {
				Invoices::setInvoice ( $request->id, $invoiceID );
				$this->_helper->redirector ( 'edit', 'invoices', 'admin', array ('id' => $request->id, 'mex' => $this->translator->translate ( 'The task requested has been executed successfully.' ), 'status' => 'success' ) );
			} else {
				$this->_helper->redirector ( 'edit', 'invoices', 'admin', array ('id' => $request->id, 'mex' => $this->translator->translate ( 'Already exist an invoice for this order.' ), 'status' => 'error' ) );
			}
		}
	}
	
	/**
	 * processAction
	 * Update the record previously selected
	 * @return unknown_type
	 */
	public function processAction() {
		$form = $this->getForm ( "/admin/invoices/process" );
		$request = $this->getRequest ();
		$attachment = $form->getElement ( 'attachments' );
		
		// Check if we have a POST request
		if (! $request->isPost ()) {
			return $this->_helper->redirector ( 'list', 'invoices', 'admin' );
		}
		
		// Create the buttons in the edit form
		$this->view->buttons = array(
				array("url" => "#", "label" => $this->translator->translate('Save'), "params" => array('css' => array('button', 'float_right'), 'id' => 'submit')),
				array("url" => "/admin/invoices/list", "label" => $this->translator->translate('List'), "params" => array('css' => array('button', 'float_right'), 'id' => 'submit')),
				array("url" => "/admin/invoices/new/", "label" => $this->translator->translate('New'), "params" => array('css' => array('button', 'float_right'))),
		);
		
		if ($form->isValid ( $request->getPost () )) {
			// Get the id 
			$id = $this->getRequest ()->getParam ( 'invoice_id' );
			
			// Set the new values
			if (is_numeric ( $id )) {
				$this->invoices = Doctrine::getTable ( 'Invoices' )->find ( $id );
			}
			
			// Get the values posted
			$params = $form->getValues ();
			try {
				
				$this->invoices->invoice_date = Shineisp_Commons_Utilities::formatDateIn ( $params ['invoice_date'] );
				$this->invoices->number = $params ['number'];
				$this->invoices->order_id = $params ['order_id'];
				$this->invoices->note = $params ['note'];
				
				// Save the data
				$this->invoices->save ();
				$id = is_numeric ( $id ) ? $id : $this->invoices->getIncremented ();
			
			} catch ( Exception $e ) {
				$this->_helper->redirector ( 'list', 'invoices', 'admin', array ('mex' => $this->translator->translate ( 'The invoice cannot be created. Please check all the data written.' ), 'status' => 'error' ) );
			}
			
			$this->_helper->redirector ( 'edit', 'invoices', 'admin', array ('id' => $id, 'mex' => $this->translator->translate ( 'The task requested has been executed successfully.' ), 'status' => 'success' ) );
		} else {
			$this->view->form = $form;
			$this->view->title = $this->translator->translate("Invoice Edit");
			$this->view->description = $this->translator->translate("Here you can edit the selected invoice.");
			return $this->render ( 'applicantform' );
		}
	}
	
	/**
	 * getForm
	 * Get the customized application form 
	 * @return unknown_type
	 */
	private function getForm($action) {
		$form = new Admin_Form_InvoicesForm ( array ('action' => $action, 'method' => 'post' ) );
		return $form;
	}
	
		/**
	 * printAction
	 * Create a pdf invoice document
	 * @return void
	 */
	public function exportAction() {
		Invoices::Export();
	}	
	
	/**
	 * printAction
	 * Create a pdf invoice document
	 * @return void
	 */
	public function printAction() {
		$request = Zend_Controller_Front::getInstance ()->getRequest ();
		try {
			if (is_numeric ( $request->id )) {
				$file = Invoices::PrintPDF($request->id, true, false);
			}
		} catch ( Exception $e ) {
			die ( $e->getMessage () );
		}
		die ();
	}

	/**
	 * overwriteAction
	 * Overwrite a pdf invoice document
	 * @return void
	 */
	public function overwriteAction() {
		$request = Zend_Controller_Front::getInstance ()->getRequest ();
		try {
			if (is_numeric ( $request->id )) {
				Invoices::overwrite($request->id);
				$this->_helper->redirector ( 'edit', 'invoices', 'admin', array ('id' => $request->id, 'mex' => $this->translator->translate ( 'The task requested has been executed successfully.' ), 'status' => 'success' ) );
			}
		} catch ( Exception $e ) {
			die ( $e->getMessage () );
		}
		die ();
	}

}