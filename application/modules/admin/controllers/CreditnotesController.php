<?php

/**
 * Creditnotes Controller
 * Handle the credit notes
 * @version 1.0
 */

class Admin_CreditnotesController extends Shineisp_Controller_Admin {
	
	protected $creditnotes;
	protected $datagrid;
	protected $session;
	protected $translator;
	
	/**
	 * Starting of the module
	 * (non-PHPdoc)
	 * @see library/Zend/Controller/Zend_Controller_Action#preDispatch()
	 */
	
	public function preDispatch() {
		$this->session = new Zend_Session_Namespace ( 'Admin' );
		$this->creditnotes = new CreditNotes ();
		$this->translator = Shineisp_Registry::getInstance ()->Zend_Translate;
		$this->datagrid = $this->_helper->ajaxgrid;
		$this->datagrid->setModule ( "creditnotes" )->setModel ( $this->creditnotes );		
	}
	
	/**
	 * Create the User object and get all the records.
	 * @return unknown_type
	 */
	public function indexAction() {
		$this->_helper->redirector ( 'list', 'creditnotes', 'admin' );
	}
	
	/**
	 * Create the User object and get all the records.
	 * @return datagrid
	 */
	public function listAction() {
		$this->view->title = $this->translator->translate("Credit notes list");
		$this->view->description = $this->translator->translate("Here you can see all the credit notes.");
		$this->view->buttons = array(array("url" => "/admin/creditnotes/new/", "label" => $this->translator->translate('New'), "params" => array('css' => array('btn'))));
		$this->datagrid->setConfig ( CreditNotes::grid() )->datagrid ();
	}
	
	/**
	 * Load Json Records
	 *
	 * @return string Json records
	 */
	public function loadrecordsAction() {
		$this->_helper->ajaxgrid->setConfig ( CreditNotes::grid() )->loadRecords ($this->getRequest ()->getParams());
	}
	
	/**
	 * searchProcessAction
	 * Search the record 
	 * @return unknown_type
	 */
	public function searchprocessAction() {
		$this->_helper->ajaxgrid->setConfig ( CreditNotes::grid() )->search ();
	}
	
	/*
	 *  Execute a custom function for each item selected in the list
	 *  this method will be call from a jQuery script 
	 *  @return string
	 */
	public function bulkAction() {
		$this->_helper->ajaxgrid->massActions ();
	}
	
	/**
	 * Set the number of the records per page
	 * @return unknown_type
	 */
	public function recordsperpageAction() {
		$this->_helper->ajaxgrid->setRowNum ();
	}
	
	/**
	 * Create the form module in order to create a record
	 * @return unknown_type
	 */
	public function newAction() {
		
		$this->view->form = $this->getForm ( "/admin/creditnotes/process" );
		$this->view->title = $this->translator->translate("New Credit Notes");
		$this->view->description = $this->translator->translate("Create a new credit note using this form.");
		$this->view->buttons = array(array("url" => "#", "label" => $this->translator->translate('Save'), "params" => array('css' => array('btn'), 'id' => 'submit')),
									 array("url" => "/admin/creditnotes/list", "label" => $this->translator->translate('List'), "params" => array('css' => array('btn'))));
		$this->render ( 'applicantform' );
	}
	
	/**
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
				$this->view->title = $this->translator->translate ( 'Are you sure you want to delete the selected credit note?' );
				$this->view->description = $this->translator->translate ( 'The credit note will no longer be available. ' );
				
				$record = $this->creditnotes->find ( $id );
				$this->view->recordselected = $record ['number'] . " - " . Shineisp_Commons_Utilities::formatDateOut ( $record ['creationdate'] );
			} else {
				$this->_helper->redirector ( 'list', $controller, 'admin', array ('mex' => $this->translator->translate ( 'Unable to process the request at this time.' ), 'status' => 'error' ) );
			}
		} catch ( Exception $e ) {
			echo $e->getMessage ();
		}
	
	}
	
	/**
	 * Delete a record previously selected by the order
	 */
	public function deleteAction() {
		$files = new Files ( );
		$id = $this->getRequest ()->getParam ( 'id' );
		try {
			CreditNotes::DeleteByID($id);
			
		} catch ( Exception $e ) {
			die ( $e->getMessage () . " " . $e->getTraceAsString () );
		}
		
		return $this->_helper->redirector ( 'index', 'creditnotes' );
	}
	
	
	/**
	 * Create the credit note document
	 */
	public function printpdfAction() {
		$files = new Files ( );
		$id = $this->getRequest ()->getParam ( 'id' );
		try {
			CreditNotes::PrintPDF($id);
		} catch ( Exception $e ) {
			die ( $e->getMessage () . " " . $e->getTraceAsString () );
		}
		
		return $this->_helper->redirector ( 'edit', 'creditnotes', 'admin', array ('id' => $id, 'mex' => $this->translator->translate ( 'The task requested has been executed successfully.' ), 'status' => 'success' )  );
	}
	
	/**
	 * Get a record and populate the application form 
	 * 
	 */
	public function editAction() {
		$form = $this->getForm ( '/admin/creditnotes/process' );
		$id = $this->getRequest ()->getParam ( 'id' );
		
		$this->view->title = $this->translator->translate("Credit Note Edit");
		$this->view->description = $this->translator->translate("Here you can edit the selected credit notes.");
		
		// Create the buttons in the edit form
		$this->view->buttons = array(
				array("url" => "#", "label" => $this->translator->translate('Save'), "params" => array('css' => array('btn'), 'id' => 'submit')),
				array("url" => "/admin/creditnotes/confirm/id/$id", "label" => $this->translator->translate('Delete'), "params" => array('css' => array('btn'))),
				array("url" => "/admin/creditnotes/list", "label" => $this->translator->translate('List'), "params" => array('css' => array('btn'), 'id' => 'submit')),
				array("url" => "/admin/creditnotes/new/", "label" => $this->translator->translate('New'), "params" => array('css' => array('btn'))),
		);
		
		if (! empty ( $id ) && is_numeric ( $id )) {
			$rs = $this->creditnotes->get_by_id ( $id );
			if (! empty ( $rs )) {
				$rs['creationdate'] = Shineisp_Commons_Utilities::formatDateOut($rs['creationdate']);
				$this->view->title = $this->translator->translate("Credit Note") . " #" . $rs['number'] . " - " . $rs['creationdate'];
				
				$this->view->id = $id;
				$form->populate ( $rs );
			}
			
			// Check if the order has been invoiced
			$invoice_id = $rs['invoice_id'];
			if($invoice_id){
				$this->view->buttons[] = array("url" => "/admin/creditnotes/printpdf/id/$id", "label" => $this->translator->translate('Print'), "params" => array('css' => array('btn')));
				$this->view->buttons[] = array("url" => "/admin/invoices/edit/id/$invoice_id", "label" => $this->translator->translate('Invoice'), "params" => array('css' => array('btn')));
			}
			
		}
		
		$this->view->mex = urldecode ( $this->getRequest ()->getParam ( 'mex' ) );
		$this->view->mexstatus = $this->getRequest ()->getParam ( 'status' );
		$this->view->items = $this->itemsGrid ();
		$this->view->form = $form;
		$this->render ( 'applicantform' );
	}
	
	/**
	 *  
	 * Enter description here ...
	 */
	private function itemsGrid() {
		$request = Zend_Controller_Front::getInstance ()->getRequest ();
		if (!empty ( $request->id ) && is_numeric ( $request->id )) {
			return array ('records' => CreditNotesItems::getDetails ( $request->id ), 'delete' => array ('controller' => 'creditnotes', 'action' => 'deleteitem' ), 'pager' => true );
		}
	}
	
	/**
	 * Update the record previously selected
	 */
	public function processAction() {
		$form = $this->getForm ( "/admin/creditnotes/process" );
		$request = $this->getRequest ();
		
		// Create the buttons in the edit form
		$this->view->buttons = array(
				array("url" => "#", "label" => $this->translator->translate('Save'), "params" => array('css' => array('btn'), 'id' => 'submit')),
				array("url" => "/admin/creditnotes/list", "label" => $this->translator->translate('List'), "params" => array('css' => array('btn'), 'id' => 'submit')),
				array("url" => "/admin/creditnotes/new/", "label" => $this->translator->translate('New'), "params" => array('css' => array('btn'))),
		);
		
		// Check if we have a POST request
		if (! $request->isPost ()) {
			return $this->_helper->redirector ( 'list', 'creditnotes', 'admin' );
		}

		if ($form->isValid ( $request->getPost () )) {
			$id = CreditNotes::saveAll($this->getRequest ()->getParam ( 'creditnote_id' ), $request->getPost ());
			$this->_helper->redirector ( 'edit', 'creditnotes', 'admin', array ('id' => $id, 'mex' => $this->translator->translate ( 'The task requested has been executed successfully.' ), 'status' => 'success' ) );
		} else {
			$this->view->form = $form;
			$this->view->title = $this->translator->translate("Credit Notes Edit");
			$this->view->description = $this->translator->translate("Here you can edit the selected credit notes.");
			return $this->render ( 'applicantform' );
		}
	}
	
	/**
	 * Get the customized application form 
	 * @return unknown_type
	 */
	private function getForm($action) {
		$form = new Admin_Form_CreditNotesForm ( array ('action' => $action, 'method' => 'post' ) );
		return $form;
	}
	
	/**
	 * Delete the file attached
	 */
	public function deleteitemAction() {
		$creditnote = CreditNotesItems::get_all($this->getRequest ()->getParam ( 'id' ));
		if(!empty($creditnote)){
			CreditNotesItems::DeleteByID($creditnote['creditnoteitem_id']);
			CreditNotes::updateTotals($creditnote['creditnote_id']);
			$this->_helper->redirector ( 'edit', 'creditnotes', 'admin', array ('id' => $creditnote['creditnote_id'], 'mex' => $this->translator->translate ( 'The task requested has been executed successfully.' ), 'status' => 'success' ) );
		}
		return $this->_helper->redirector ( 'list', 'creditnotes', 'admin' );
	}
}