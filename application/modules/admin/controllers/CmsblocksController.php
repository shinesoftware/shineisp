<?php

/**
 * Admin_CmsblocksController
 * Manage the Pages Blocks Items
 * @version 1.0
 * <?xml version="1.0"?>
	<layout version="0.1">
	    <reference column="right">
	        <block name="test" />
	        <block name="test1" />
	        <block name="test2" />
	    </reference>
	</layout>
 * 
 */

class Admin_CmsblocksController extends Shineisp_Controller_Admin {
	
	protected $cmsblocks;
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
		$this->cmsblocks = new CmsBlocks ();
		$this->translator = Shineisp_Registry::getInstance ()->Zend_Translate;
		$this->datagrid = $this->_helper->ajaxgrid;
		$this->datagrid->setModule ( "cmsblocks" )->setModel ( $this->cmsblocks );		
	}
	
	/**
	 * indexAction
	 * Create the User object and get all the records.
	 * @return unknown_type
	 */
	public function indexAction() {
		$redirector = Zend_Controller_Action_HelperBroker::getStaticHelper ( 'redirector' );
		$redirector->gotoUrl ( '/admin/cmsblocks/list' );
	}
	
	/**
	 * indexAction
	 * Create the User object and get all the records.
	 * @return datagrid
	 */
	public function listAction() {
		$this->view->title = $this->translator->translate("Cms Blocks list");
		$this->view->description = $this->translator->translate("Here you can see all the cms blocks.");
		$this->view->buttons = array(array("url" => "/admin/cmsblocks/new/", "label" => $this->translator->translate('New'), "params" => array('css' => array('btn'))));
		
		$this->datagrid->setConfig ( CmsBlocks::grid() )->datagrid ();
	}

	/**
	 * Load Json Records
	 *
	 * @return string Json records
	 */
	public function loadrecordsAction() {
		$this->_helper->ajaxgrid->setConfig ( CmsBlocks::grid() )->loadRecords ($this->getRequest ()->getParams());
	}	
	
	/**
	 * searchProcessAction
	 * Search the record 
	 * @return unknown_type
	 */
	public function searchprocessAction() {
		$this->_helper->ajaxgrid->setConfig ( CmsBlocks::grid() )->search ();
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
		$this->view->form = $this->getForm ( "/admin/cmsblocks/process" );
		$this->view->title = $this->translator->translate("Create a page");
		$this->view->description = $this->translator->translate("Here you can create a static page.");
		$this->view->buttons = array(array("url" => "#", "label" => $this->translator->translate('Save'), "params" => array('css' => array('btn'), 'id' => 'submit')),
									 array("url" => "/admin/cmsblocks/list", "label" => $this->translator->translate('List'), "params" => array('css' => array('btn'), 'id' => 'submit')));
		
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
				$this->view->title = $this->translator->translate ( 'Are you sure you want to delete this page?' );
				$this->view->description = $this->translator->translate ( 'The page will no longer be available.' );
				
				$record = $this->cmsblocks->find ( $id );
				$this->view->recordselected = $this->translator->translate ( $record ['title'] );
			} else {
				$this->_helper->redirector ( 'list', $controller, 'admin', array ('mex' => $this->translator->translate ( 'Unable to process the request at this time.' ), 'status' => 'error' ) );
			}
		} catch ( Exception $e ) {
			echo $e->getMessage ();
		}
	}
	
	/**
	 * deleteAction
	 * Delete a record previously selected by the cmsblocks
	 * @return unknown_type
	 */
	public function deleteAction() {
		$id = $this->getRequest ()->getParam ( 'id' );
		if (is_numeric ( $id )) {
			CmsBlocks::deleteItem( $id );
		}
		return $this->_helper->redirector ( 'index', 'cmsblocks' );
	}
	
	/**
	 * editAction
	 * Get a record and populate the application form 
	 * @return unknown_type
	 */
	public function editAction() {
		$form = $this->getForm ( '/admin/cmsblocks/process' );
		$id = $this->getRequest ()->getParam ( 'id' );
		
		if (! empty ( $id ) && is_numeric ( $id )) {
			
			$record = $this->cmsblocks->getAllInfo ( $id );
			if (! empty ( $record )) {
				$form->populate ($record );
			}
		}
		
		$this->view->title = $this->translator->translate("Edit Page");
		$this->view->description = $this->translator->translate("Here you can edit the page.");
		
		// Create the buttons in the edit form
		$this->view->buttons = array(
				array("url" => "#", "label" => $this->translator->translate('Save'), "params" => array('css' => array('btn'), 'id' => 'submit')),
				array("url" => "/admin/cmsblocks/confirm/id/$id", "label" => $this->translator->translate('Delete'), "params" => array('css' => array('btn'))),
				array("url" => "/admin/cmsblocks/list", "label" => $this->translator->translate('List'), "params" => array('css' => array('btn'), 'id' => 'submit')),
				array("url" => "/admin/cmsblocks/new/", "label" => $this->translator->translate('New'), "params" => array('css' => array('btn'))),
		);
		
		$this->view->form = $form;
		$this->render ( 'applicantform' );
	}
	
	/**
	 * processAction
	 * Update the record previously selected
	 * @return unknown_type
	 */
	public function processAction() {
		$redirector = Zend_Controller_Action_HelperBroker::getStaticHelper ( 'redirector' );
		$form = $this->getForm ( "/admin/cmsblocks/process" );
		$request = $this->getRequest ();
		
		// Create the buttons in the edit form
		$this->view->buttons = array(
				array("url" => "#", "label" => $this->translator->translate('Save'), "params" => array('css' => array('btn'), 'id' => 'submit')),
				array("url" => "/admin/cmsblocks/list", "label" => $this->translator->translate('List'), "params" => array('css' => array('btn'), 'id' => 'submit')),
				array("url" => "/admin/cmsblocks/new/", "label" => $this->translator->translate('New'), "params" => array('css' => array('btn'))),
		);
		
		// Check if we have a POST request
		if (! $request->isPost ()) {
			return $this->_helper->redirector ( 'list', 'cmsblocks', 'admin' );
		}
		
		if ($form->isValid ( $request->getPost () )) {
			
			// Get the id 
			$id = $this->getRequest ()->getParam ( 'block_id' );
			
			CmsBlocks::saveAll($id, $form->getValues ());
			
			$redirector->gotoUrl ( "/admin/cmsblocks/edit/id/$id" );
		
		} else {
			$this->view->form = $form;
			$this->view->title = $this->translator->translate("CMS Page Details");
			$this->view->description = $this->translator->translate("Here you can reply to all the customers requests");
			return $this->render ( 'applicantform' );
		}
	}
	
	/**
	 * getForm
	 * Get the customized application form 
	 * @return unknown_type
	 */
	private function getForm($action) {
		$form = new Admin_Form_CmsblocksForm ( array ('action' => $action, 'method' => 'post' ) );
		return $form;
	}
}