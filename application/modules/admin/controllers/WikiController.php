<?php

/**
 * WikiController
 * Manage the Wiki Article table
 * @version 1.0
 */

class Admin_WikiController extends Shineisp_Controller_Admin {
	
	protected $wiki;
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
		$this->wiki = new Wiki ();
		$this->translator = Zend_Registry::getInstance ()->Zend_Translate;
		$this->datagrid = $this->_helper->ajaxgrid;
		$this->datagrid->setModule ( "wiki" )->setModel ( $this->wiki );
				
	}
	
	/**
	 * indexAction
	 * Create the User object and get all the records.
	 * @return unknown_type
	 */
	public function indexAction() {
		$redirector = Zend_Controller_Action_HelperBroker::getStaticHelper ( 'redirector' );
		$redirector->gotoUrl ( '/admin/wiki/list' );
	}
	
	/**
	 * indexAction
	 * Create the User object and get all the records.
	 * @return datagrid
	 */
	public function listAction() {
		$this->view->title = $this->translator->translate("Wiki list");
		$this->view->description = $this->translator->translate("Here you can see all the wiki articles.");
		$this->view->buttons = array(array("url" => "/admin/wiki/new/", "label" => $this->translator->translate('New'), "params" => array('css' => array('button', 'float_right'))));
		$this->datagrid->setConfig ( Wiki::grid() )->datagrid ();
	}

	/**
	 * Load Json Records
	 *
	 * @return string Json records
	 */
	public function loadrecordsAction() {
		$this->_helper->ajaxgrid->setConfig ( Wiki::grid() )->loadRecords ($this->getRequest ()->getParams());
	}
	
	/**
	 * searchProcessAction
	 * Search the record 
	 * @return unknown_type
	 */
	public function searchprocessAction() {
		$this->_helper->ajaxgrid->setConfig ( Wiki::grid() )->search ();
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
		$this->view->form = $this->getForm ( "/admin/wiki/process" );
		$this->view->title = $this->translator->translate("Wiki Details");
		$this->view->description = $this->translator->translate("Here you can reply to all the customers requests");
		$this->view->buttons = array(array("url" => "#", "label" => $this->translator->translate('Save'), "params" => array('css' => array('button', 'float_right'), 'id' => 'submit')),
									 array("url" => "/admin/wiki/list", "label" => $this->translator->translate('List'), "params" => array('css' => array('button', 'float_right'))));
		
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
				$this->view->title = $this->translator->translate ( 'Are you sure to delete this article?' );
				$this->view->description = $this->translator->translate ( 'The article will be no more longer available.' );
				
				$record = $this->wiki->find ( $id );
				$this->view->recordselected = $this->translator->translate ( $record ['subject'] );
			} else {
				$this->_helper->redirector ( 'list', $controller, 'admin', array ('mex' => $this->translator->translate ( 'Unable to process request at this time.' ), 'status' => 'error' ) );
			}
		} catch ( Exception $e ) {
			echo $e->getMessage ();
		}
	}
	
	/**
	 * deleteAction
	 * Delete a record previously selected by the wiki
	 * @return unknown_type
	 */
	public function deleteAction() {
		$files = new Files ( );
		$id = $this->getRequest ()->getParam ( 'id' );
		if (is_numeric ( $id )) {
			$this->wiki->find ( $id )->delete ();
		}
		return $this->_helper->redirector ( 'index', 'wiki' );
	}
	
	
	/**
	 * editAction
	 * Get a record and populate the application form 
	 * @return unknown_type
	 */
	public function editAction() {
		$form = $this->getForm ( '/admin/wiki/process' );
		$id = $this->getRequest ()->getParam ( 'id' );
		
		// Create the buttons in the edit form
		$this->view->buttons = array(
				array("url" => "#", "label" => $this->translator->translate('Save'), "params" => array('css' => array('button', 'float_right'), 'id' => 'submit')),
				array("url" => "/admin/wiki/list", "label" => $this->translator->translate('List'), "params" => array('css' => array('button', 'float_right'), 'id' => 'submit')),
				array("url" => "/admin/wiki/new/", "label" => $this->translator->translate('New'), "params" => array('css' => array('button', 'float_right'))),
		);
		
		if (! empty ( $id ) && is_numeric ( $id )) {
			$fields = "w.wiki_id, w.subject as subject, DATE_FORMAT(w.creationdate, '%d/%m/%Y') as creationdate, w.metakeywords as metakeywords, w.metadescription as metadescription, w.uri as uri, wc.category_id as category_id, wc.category as category, w.content as content, w.active as active, w.language_id as language_id";
			$rs = $this->wiki->getAllInfo ( $id, $fields, true );
			
			if (! empty ( $rs [0] )) {
				$form->populate ( $rs [0] );
				$this->view->buttons[] = array("url" => "/admin/wiki/confirm/id/$id", "label" => $this->translator->translate('Delete'), "params" => array('css' => array('button', 'float_right')));
			}
		}
		
		$this->view->title = $this->translator->translate("Wiki Article");
		$this->view->description = $this->translator->translate("Here you can add or edit a wiki article.");
		
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
		$form = $this->getForm ( "/admin/wiki/process" );
		$request = $this->getRequest ();
		
		// Create the buttons in the edit form
		$this->view->buttons = array(
				array("url" => "#", "label" => $this->translator->translate('Save'), "params" => array('css' => array('button', 'float_right'), 'id' => 'submit')),
				array("url" => "/admin/wiki/list", "label" => $this->translator->translate('List'), "params" => array('css' => array('button', 'float_right'), 'id' => 'submit')),
				array("url" => "/admin/wiki/new/", "label" => $this->translator->translate('New'), "params" => array('css' => array('button', 'float_right'))),
		);
		
		// Check if we have a POST request
		if (! $request->isPost ()) {
			return $this->_helper->redirector ( 'list', 'wiki', 'admin' );
		}
		
		if ($form->isValid ( $request->getPost () )) {
			// Get the id 
			$id = $this->getRequest ()->getParam ( 'wiki_id' );
			
			// Set the new values
			if (is_numeric ( $id )) {
				$this->wiki = Doctrine::getTable ( 'Wiki' )->find ( $id );
			} else {
				$this->wiki->creationdate = date ( 'Y-m-d' );
			}
			
			// Get the values posted
			$params = $form->getValues ();
			
			$this->wiki->uri = !empty($params ['uri']) ? Shineisp_Commons_UrlRewrites::format($params ['uri']) : Shineisp_Commons_UrlRewrites::format($params ['subject']);
			$this->wiki->subject = $params ['subject'];
			$this->wiki->metadescription = $params ['metadescription'];
			$this->wiki->metakeywords = $params ['metakeywords'];
			$this->wiki->category_id = $params ['category_id'];
			$this->wiki->language_id = $params ['language_id'];
			$this->wiki->content = $params ['content'];
			$this->wiki->active = $params ['active'] ? 1 : 0;
			$this->wiki->save ();
			
			$redirector->gotoUrl ( "/admin/wiki/edit/id/$id" );
		
		} else {
			$this->view->form = $form;
			$this->view->title = $this->translator->translate("Wiki Details");
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
		$form = new Admin_Form_WikiForm ( array ('action' => $action, 'method' => 'post' ) );
		return $form;
	}
	
}
