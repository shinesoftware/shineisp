<?php

/**
 * TicketsController
 * Manage the tickets table
 * @version 1.0
 */

class Admin_TicketsController extends Zend_Controller_Action {
	
	protected $tickets;
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
		$this->tickets = new Tickets ();
		$this->translator = Zend_Registry::getInstance ()->Zend_Translate;
		$this->datagrid = $this->_helper->ajaxgrid;
		$this->datagrid->setModule ( "tickets" )->setModel ( $this->tickets );
	}
	
	/**
	 * indexAction
	 * Create the User object and get all the records.
	 * @return unknown_type
	 */
	public function indexAction() {
		$redirector = Zend_Controller_Action_HelperBroker::getStaticHelper ( 'redirector' );
		$redirector->gotoUrl ( '/admin/tickets/list' );
	}
	

	/**
	 * indexAction
	 * Create the User object and get all the records.
	 * @return datagrid
	 */
	public function listAction() {
		$this->view->title = "Tickets list";
		$this->view->description = "Here you can see all the tickets.";
		$this->view->buttons = array(array("url" => "/admin/tickets/new/", "label" => $this->translator->translate('New'), "params" => array('css' => array('button', 'float_right'))));
		$this->datagrid->setConfig ( Tickets::grid() )->datagrid ();
	}

	/**
	 * Load Json Records
	 *
	 * @return string Json records
	 */
	public function loadrecordsAction() {
		$this->_helper->ajaxgrid->setConfig ( Tickets::grid() )->loadRecords ($this->getRequest ()->getParams());
	}
	
	/**
	 * searchProcessAction
	 * Search the record 
	 * @return unknown_type
	 */
	public function searchprocessAction() {
		$this->_helper->ajaxgrid->setConfig ( Tickets::grid() )->search ();
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
		$this->view->form = $this->getForm ( "/admin/tickets/process" );
		$this->view->title = "New Ticket";
		$this->view->description = "Here you can handle the ticket support.";
		
		$this->view->buttons = array(array("url" => "#", "label" => $this->translator->translate('Save'), "params" => array('css' => array('button', 'float_right'), 'id' => 'submit')),
								array("url" => "/admin/tickets/list", "label" => $this->translator->translate('List'), "params" => array('css' => array('button', 'float_right'))));
		
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
				$this->view->title = $this->translator->translate ( 'Are you sure to delete this ticket and its messages?' );
				$this->view->description = $this->translator->translate ( 'The ticket will be no more longer available.' );
				
				$record = $this->tickets->find ( $id );
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
	 * Delete a record previously selected by the tickets
	 * @return unknown_type
	 */
	public function deleteAction() {
		$files = new Files ( );
		$id = $this->getRequest ()->getParam ( 'id' );
		if (is_numeric ( $id )) {
			$this->tickets->find ( $id )->delete ();
		}
		return $this->_helper->redirector ( 'index', 'tickets' );
	}
	
	/**
	 * deleteNoteAction
	 * Delete the ticket note
	 * @return unknown_type
	 */
	public function deletenoteAction() {
		$files = new Files ( );
		$noteId = $this->getRequest ()->getParam ( 'id' );
		if (is_numeric ( $noteId )) {
			$ticketNote = TicketsNotes::getAllInfo($noteId);
			if(!empty($ticketNote[0])){
				$ticketid = $ticketNote[0]['ticket_id'];
				TicketsNotes::deleteNote( $noteId );
				$this->_helper->redirector ( 'edit', 'tickets', 'admin', array('id' => $ticketid, 'mex' => 'Note has been deleted', 'status' => 'success') );
			}
		}
		$this->_helper->redirector ( 'edit', 'tickets', 'admin', array('id' => $ticketid, 'mex' => 'Error on deleting the customer note', 'status' => 'error') );
	}
	
	/**
	 * editAction
	 * Get a record and populate the application form 
	 * @return unknown_type
	 */
	public function editAction() {
		$form = $this->getForm ( '/admin/tickets/process' );
		$form->getElement ( 'save' )->setLabel ( 'Reply' );
		$id = $this->getRequest ()->getParam ( 'id' );
		
		$this->view->title = "Edit Ticket";
		$this->view->description = "Here you can handle the ticket support.";
		
		// Create the buttons in the edit form
		$this->view->buttons = array(
				array("url" => "#", "label" => $this->translator->translate('Save'), "params" => array('css' => array('button', 'float_right'), 'id' => 'submit')),
				array("url" => "/admin/tickets/list/", "label" => $this->translator->translate('List'), "params" => array('css' => array('button', 'float_right'), 'id' => 'submit')),
				array("url" => "/admin/tickets/new/", "label" => $this->translator->translate('New'), "params" => array('css' => array('button', 'float_right'))),
		);
		
		if (! empty ( $id ) && is_numeric ( $id )) {
			
			$this->view->buttons[] = array("url" => "/admin/tickets/setstatus/id/$id/statusid/" . Statuses::id('closed', 'tickets'), "label" => $this->translator->translate('Set as closed'), "params" => array('css' => array('button button_blue', 'float_right')));
			$this->view->buttons[] = array("url" => "/admin/tickets/setstatus/id/$id/statusid/" . Statuses::id('solved', 'tickets'), "label" => $this->translator->translate('Set as solved'), "params" => array('css' => array('button button_green', 'float_right')));
			$this->view->buttons[] = array("url" => "/admin/tickets/confirm/id/$id", "label" => $this->translator->translate('Delete'), "params" => array('css' => array('button button_red', 'float_right')));
			
			$form->populate ( array('datetime' => date('d/m/Y H:i:s'), 'ticket_id' => $id) );
			
			$fields = "DATE_FORMAT(t.date_open, '%d/%m/%Y') as date_open, user_id, sibling_id, category_id as category, CONCAT(d.domain, '.', ws.tld) as domain, DATE_FORMAT(t.date_close, '%d/%m/%Y') as date_close, t.subject, t.description, t.status_id, s.status, c.email as email, c.customer_id as customer_id, CONCAT(c.firstname, ' ', c.lastname) as fullname, c.company as company, (DATEDIFF(t.date_close, t.date_open)) as days";
			$rs = $this->tickets->getAllInfo ( $id, $fields, true );
			
			if (! empty ( $rs [0] )) {
				$form->populate ( array('datetime' => date('d/m/Y H:i:s'), 'ticket_id' => $id) + $rs [0] );
				
				$siblings = Tickets::getListbyCustomerId($rs[0]['customer_id'], true, true);
				unset($siblings[$id]);
				$form->getElement('sibling_id')->setMultiOptions($siblings);
				
				if(!empty($rs [0]['sibling_id'])){
					$rs[0]['sibling'] = $this->tickets->getAllInfo ( $rs [0]['sibling_id'], $fields, true );
				}
				
				$this->view->record = $rs [0];
				$this->view->siblings = $siblings;
				$this->view->customerid = $rs[0]['customer_id'];
				
				$isp = Isp::getActiveISP();
				$this->view->customeravatar = Shineisp_Commons_Gravatar::get_gravatar ( $rs [0] ['email'], 50);
				$this->view->notes = Tickets::Notes ( $id, "c.customer_id, tn.note_id, t.ticket_id, note_id as id, admin as adminreply, DATE_FORMAT(date_post, '%d/%m/%Y %H:%i:%s') as date_post, note as reply", true );
				
				
				// Header Information
				$this->view->title = "Edit Ticket: " . $rs[0]['subject'];
				$userlink = "<a href='/admin/customers/edit/id/".$rs[0]['customer_id']."'>".$rs[0]['fullname']."</a>";
				
				if(!empty($rs[0]['company'])){
					$userlink .= " [<a href='/admin/customers/edit/id/".$rs[0]['customer_id']."'>".$rs[0]['company']."</a>]";
				}
				
				$description[] = $this->translator->_("Ticket support from %s", $userlink);
				$description[] = TicketsCategories::getName($rs[0]['category']);
				$description[] = $this->translator->_("Opened at %s", $rs[0]['date_open']);
				
				if(!empty($rs[0]['date_close'])){
					$description[] = $this->translator->_("Closed at %s", $rs[0]['date_close']);
				}
				
				if(!empty($rs[0]['domain'])){
					$description[] = "<a target=\"blank\" href=\"http://www.".$rs[0]['domain']."\">" . $rs[0]['domain'] . "</a>";
				}
				
				$this->view->description = implode(" - ", $description);
				
				// Hide these fields and values inside the vertical grid object
				unset ( $rs [0] ['parent'] );
				unset ( $rs [0] ['description'] );
				
				$this->view->data = array ('records' => $rs );
			}
		}
		
		
		
		$this->view->form = $form;
		$this->render ( 'applicantform' );
	}
	
	/**
	 * searchAction
	 * Get a record and populate the application form 
	 * @return unknown_type
	 */
	public function searchAction() {
		$form = $this->getForm ( '/admin/tickets/searchprocess' );
		$form->getElement ( 'save' )->setName ( 'search' );
		
		$this->view->form = $form;
		$this->render ( 'searchform' );
	}
	
	/**
	 * Delete an attached file
	 */
	public function delattachAction() {
		$id = $this->getRequest ()->getParam ( 'id' );
		if(is_numeric($id)){
			Files::del($id);
			$this->_helper->redirector ( 'list', 'tickets', 'admin', array('mex' => 'File deleted', 'status' => 'success') );
		}
		$this->_helper->redirector ( 'list', 'tickets', 'admin', array('mex' => 'File not deleted', 'status' => 'error') );
	}	
	
	

	/**
	 * Set the status of the ticket
	 */
	public function setstatusAction() {
		$statusid = $this->getRequest ()->getParam ( 'statusid' );
		$id = $this->getRequest ()->getParam ( 'id' );
		if(is_numeric($id) && is_numeric($statusid)){
			Tickets::setStatus($id, $statusid);
			$this->_helper->redirector ( 'edit', 'tickets', 'admin', array('id' => $id, 'mex' => 'The task requested has been done', 'status' => 'success') );
		}
		$this->_helper->redirector ( 'list', 'tickets', 'admin', array('mex' => 'There was a problem', 'status' => 'error') );
	}
	
	/**
	 * processAction
	 * Update the record previously selected
	 * @return unknown_type
	 */
	public function processAction() {
		$redirector = Zend_Controller_Action_HelperBroker::getStaticHelper ( 'redirector' );
		$form = $this->getForm ( "/admin/tickets/process" );
		$request = $this->getRequest ();
		
		$this->view->title = "New Ticket";
		$this->view->description = "Here you can handle the ticket support.";
		
		// Create the buttons in the edit form
		$this->view->buttons = array(
				array("url" => "#", "label" => $this->translator->translate('Save'), "params" => array('css' => array('button', 'float_right'), 'id' => 'submit')),
				array("url" => "/admin/tickets/list", "label" => $this->translator->translate('List'), "params" => array('css' => array('button', 'float_right'), 'id' => 'submit')),
				array("url" => "/admin/tickets/new/", "label" => $this->translator->translate('New'), "params" => array('css' => array('button', 'float_right'))),
		);
				
		// Check if we have a POST request
		if (! $request->isPost ()) {
			$this->_helper->redirector ( 'list', 'tickets', 'admin' );
		}
		
		if ($form->isValid ( $request->getPost () )) {
			// Get the id 
			$id = $this->getRequest ()->getParam ( 'ticket_id' );
			$isp = Isp::getActiveISP ();
			if ($isp) {
				
				$operatorId = Settings::findbyParam('tickets_operator', 'admin', Isp::getActiveISPID());
				if(!is_numeric($operatorId)){
					$operator = AdminUser::getFirstAdminUser();
					$operatorId = $operator['user_id'];
				}else{
					$operator = AdminUser::getAllInfo($operatorId);
				}
				
				// Set the new values
				if (is_numeric ( $id )) {
					
					$ispmail = explode("@", $isp ['email']);
	                $ispmail = "noreply@" . $ispmail[1];
					
					// Get the values posted
					$params = $request->getPost ();
					
					$tickets = Doctrine::getTable ( 'Tickets' )->find ( $id );
					$t = $tickets->toArray ();
					
					if (isset ( $t ['customer_id'] ) && is_numeric ( $t ['customer_id'] )) {
						
						// Retrieve the customer data
						$c = Customers::find ( $t ['customer_id'], "CONCAT(firstname,' ', lastname, ' ', company) as fullname, email", true );
						$cc = Contacts::getAllEmails($t['customer_id']);
							
						if (! empty ( $params ['status_id'] )) {
							$tickets->status_id = $params ['status_id'];
						}
						
						if ($params ['status_id'] == Statuses::id('closed', 'statuses') || $params ['status_id'] == Statuses::id('solved', 'statuses')) { // If the status is closed or solved we have to set the date_close field
							$tickets->date_close = date ( 'Y-m-d H:i:s' );
						}
						
						if(!empty($params ['sibling_id']) && $params ['sibling_id'] != $id){
							$tickets->sibling_id = $params ['sibling_id'];
						}
						
						$tickets->date_updated = date ( 'Y-m-d H:i:s' );
						$tickets->user_id = $params['user_id'];
						
						$tickets->save ();
						
						if(!empty($params ['note'])){
							$ticketnotes = new TicketsNotes ( );
							$ticketnotes->date_post = !empty($params['datetime']) ? Shineisp_Commons_Utilities::formatDateIn($params['datetime']) : date ( 'Y-m-d H:i:s' );
							$ticketnotes->note = $params ['note'];
							$ticketnotes->ticket_id = $id;
							$ticketnotes->admin = true;
							$ticketnotes->trySave ();
						}
						
						// Save the upload file
						TicketsNotes::UploadDocument($id, $t ['customer_id']);
						
						$retval = Shineisp_Commons_Utilities::getEmailTemplate ( 'ticket_message' );

						if ($retval && $params['sendemail'] && !empty($params['note'])) {
							
							$link = Fastlinks::findbyId ( $id, 'tickets', $t ['customer_id'] );
							$in_reply_to = md5($id)  . "@" . $_SERVER['HTTP_HOST'];
							
							if (! empty ( $link [0] ['code'] )) {
								$link = $link [0] ['code'];
							}else{
								$link = Fastlinks::CreateFastlink('tickets', 'edit', json_encode ( array ('id' => $id ) ), 'tickets', $id, $t['customer_id']);
							}
							
							$subject = $retval ['subject'];
							$subject = str_replace ( "[subject]", $t ['subject'], $subject );
							$subject = str_replace ( "[company]", $isp ['company'], $subject );
							$subject = str_replace ( "[issue_number]", $t ['ticket_id'], $subject );
							
							$body = $retval ['template'];
							$body = str_replace ( "[operator]", $operator['lastname'] . " " . $operator['firstname'] . ".", $body );
							$body = str_replace ( "[subject]", $t ['subject'], $body );
							$body = str_replace ( "[customer]", $c ['fullname'], $body );
							$body = str_replace ( "[issue_number]", $t ['ticket_id'], $body );
							$body = str_replace ( "[status]", Statuses::getLabel ( $params ['status_id'] ), $body );
							$body = str_replace ( "[link]", "http://" . $_SERVER ['HTTP_HOST'] . "/index/link/id/" . $link, $body );
							$body = str_replace ( "[date_open]", Shineisp_Commons_Utilities::formatDateOut ( $t ['date_open'] ), $body );
							
							$attachment = Tickets::hasAttachments($id);
							if(!empty($attachment)){
								$body = str_replace ( "[attachment]", "http://" . $_SERVER['HTTP_HOST'] . $attachment[0]['path'] . $attachment[0]['file'], $body );
								$body = str_replace ( "[attachment_name]", $attachment[0]['file'], $body );
							}else{
								$body = str_replace ( "[attachment]", "", $body );
								$body = str_replace ( "[attachment_name]", "", $body );
							}
							
							if ($params ['status_id'] == Statuses::id("closed", "tickets") || $params ['status_id'] == Statuses::id("solved", "tickets")) {
								$body = str_replace ( "[date_close]", date ( 'd/m/Y H:i:s' ), $body );
							} else {
								$body = str_replace ( "[date_close]", "-", $body );
							}
							
							$body = str_replace ( "[description]", $params ['note'], $body );
							$body = str_replace ( "[company]", $isp ['company'], $body );
							
							// All the customer email contacts will be receive a message
							foreach ($cc as $address){
								$bcc[] = $address['email'];
							}
							$bcc = implode(",", $bcc);
								
							Shineisp_Commons_Utilities::SendEmail ( $ispmail, Contacts::getEmails($t ['customer_id']), $bcc, $subject, $body, true, $in_reply_to );
						}
					}
					$redirector->gotoUrl ( "/admin/tickets/edit/id/$id#last" );
				}
			}
			
			$redirector->gotoUrl ( "/admin/tickets/list" );
		
		} else {
			$this->view->form = $form;
			return $this->render ( 'applicantform' );
		}
	}
	
	/**
	 * getForm
	 * Get the customized application form 
	 * @return unknown_type
	 */
	private function getForm($action) {
		$form = new Admin_Form_TicketsForm ( array ('action' => $action, 'method' => 'post' ) );
		return $form;
	}
	
}
