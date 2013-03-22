<?php

class TicketsController extends Zend_Controller_Action {
	protected $customer;
	protected $tickets;
	protected $translator;
	
	/**
	 * preDispatch
	 * Starting of the module
	 * (non-PHPdoc)
	 * @see library/Zend/Controller/Zend_Controller_Action#preDispatch()
	 */
	
	public function preDispatch() {
		$NS = new Zend_Session_Namespace ( 'Default' );
		
		if (empty($NS->customer)) {
			$this->_helper->redirector ( 'index', 'index', 'default' );
		}
		$this->customer = $NS->customer;
		
		$registry = Zend_Registry::getInstance ();
		$this->tickets = new Tickets ();
		$this->translator = $registry->Zend_Translate;
		
		// Set the navigation menu for the client control panel page on the left sidebar
		#$this->view->placeholder ( "left" )->append ( $string);	

		$this->getHelper ( 'layout' )->setLayout ( '1column' );
	}
	
	/**
	 * indexAction
	 * Create the User object and get all the records.
	 * @return unknown_type
	 */
	public function indexAction() {
		$redirector = Zend_Controller_Action_HelperBroker::getStaticHelper ( 'redirector' );
		$redirector->gotoUrl ( '/tickets/list' );
	}
	
	public function listAction() {
		$NS = new Zend_Session_Namespace ( 'Default' );
		$arrSort = array ();
		$params = array ();
		
		$page = $this->getRequest ()->getParam ( 'page' );
		$sort = $this->getRequest ()->getParam ( 'sort' );
		
		if (! empty ( $sort )) {
			$arrSort [] = $this->SortingData ( $sort );
			$arrSort [] = $sort;
		} else {
			$arrSort [] = "t.date_open desc";
		}
		
		$params ['search'] = array ('method' => 'addWhere', 'criteria' => "c.customer_id = ?", 'value' => $NS->customer ['customer_id'] );
		
		$page = ! empty ( $page ) && is_numeric ( $page ) ? $page : 1;
		$data = $this->tickets->findAll ( "tc.category_id as category_id, tc.category as category, t.subject, s.status as status, DATE_FORMAT(t.date_open, '%d/%m/%Y ') as Starting", $page, $NS->recordsperpage, $arrSort, $params );
		$data ['currentpage'] = $page;
		
		$this->view->mex = $this->getRequest ()->getParam ( 'mex' );
		$this->view->mexstatus = $this->getRequest ()->getParam ( 'status' );
		$this->view->title = "Tickets list";
		$this->view->description = "Here you can see the list of all the issue posted.";
		$this->view->tickets = $data;
		$this->_helper->viewRenderer ( 'index' );
	}
	
	/**
	 * newAction
	 * Create the form module in order to create a record
	 * @return unknown_type
	 */
	public function newAction() {
		$form = $this->getForm ( '/tickets/process' );
		$this->view->title = "Add new Issue";
		$this->view->description = "Post a new issue.";
		$this->view->form = $form;
		
		$this->getHelper ( 'layout' )->setLayout ( '2columns-right' );
		$this->view->placeholder ( "right" )->set ( $this->view->partial ( 'partials/wikisidebar.phtml', array ('items' => Wiki::get_items(5) ) ) );
		$this->view->canreply = true;
		$this->_helper->viewRenderer ( 'customform' );
	}
	
	/**
	 * editAction
	 * Get a record and populate the application form 
	 * @return unknown_type
	 */
	public function editAction() {
		$this->getHelper ( 'layout' )->setLayout ( '1column' );
		$form = $this->getForm ( '/tickets/process' );
		
		$id = $this->getRequest ()->getParam ( 'id' );
		if (! empty ( $id ) && is_numeric ( $id )) {
			$isp = Isp::getActiveISP ();
			$fields = "DATE_FORMAT(t.date_open, '%d/%m/%Y %H:%i:%s') as creationdate, t.sibling_id,  DATE_FORMAT(t.date_close, '%d/%m/%Y %H:%i:%s') as expiringdate, 
			t.subject, t.description, t.status_id as status_id, t.vote as vote, s.status as status, c.email as email, CONCAT(c.firstname, ' ', c.lastname) as customer, c.company as company, (DATEDIFF(t.date_close, t.date_open)) as days";
			
			$rs = $this->tickets->getAllInfo ( $id, $fields, true );
			
			if (! empty ( $rs [0] )) {
				if(!empty($rs [0]['sibling_id'])){
					$rs[0]['sibling'] = Tickets::getAllInfo ( $rs [0]['sibling_id'], $fields, true );
				}
				
				$this->view->headTitle()->prepend ( $rs [0] ['customer'] );
				$this->view->headTitle()->prepend ( $rs [0] ['subject'] );
				
				$form->populate ( $rs [0] );
				$this->view->record = $rs [0];
				$this->view->isp = $isp;
				$this->view->adminavatar = Shineisp_Commons_Gravatar::get_gravatar ( $isp ['email'] );
				$this->view->customeravatar = Shineisp_Commons_Gravatar::get_gravatar ( $rs [0] ['email'] );
				$this->view->notes = Tickets::Notes ( $id, "note_id, admin as adminreply, vote as vote, DATE_FORMAT(date_post, '%d/%m/%Y %H:%i:%s') as date_post,CONCAT(c.firstname, ' ', c.lastname) as customer, c.company as company, note", true );
				$this->view->summary = $rs [0];
				
				$status = $rs [0]['status_id'];
				if($status == Statuses::id('closed', 'tickets') || $status == Statuses::id('solved', 'tickets')){
					$this->view->canreply = false;
				}else{
					$this->view->canreply = true;
				}
				
			}
			$this->view->id = $id;
			
			$this->view->headertitle = $this->translator->translate('Ticket page');
			
			$this->getHelper ( 'layout' )->setLayout ( '2columns-right' );
			$this->view->placeholder ( "right" )->append ( $this->view->partial ( 'partials/wikisidebar.phtml', array ('items' => Wiki::get_items(5) ) ) );
		}
		
		$this->view->title = "Ticket Edit";
		$this->view->description = "Here you can write down your problem. Remember to be clear and analytic in order to explain the problem that has been occurred.";
		$this->view->dnsdatagrid = $this->dnsGrid ();
		$this->view->form = $form;
		$this->_helper->viewRenderer ( 'customform' );
	}
	
	/**
	 * Ticket rating 
	 */
	public function voteAction() {
		$id = $this->getRequest ()->getParam('id');
		$stars = $this->getRequest ()->getParam('stars');
		TicketsNotes::Vote($id, $stars);
		die();
	}
	
	/**
	 * processAction
	 * Update the record previously selected
	 * @return unknown_type
	 */
	public function processAction() {
		$NS = new Zend_Session_Namespace ( 'Default' );
		$request = $this->getRequest ();
		$customerid = $NS->customer ['customer_id'];
		
		$this->view->title = "Ticket process";
		$this->view->description = "Check the information before save again.";
			
		// Check if we have a POST request
		if (! $request->isPost ()) {
			return $this->_helper->redirector ( 'index' );
		}
		
		// Get our form and validate it
		$form = $this->getForm ( '/tickets/process' );

		// Invalid entries
		if (! $form->isValid ( $request->getPost () )) {
			$this->view->form = $form;
			$this->view->canreply = true;
			return $this->_helper->viewRenderer ( 'customform' ); 
		}
		
		// Get the id 
		$id = $this->getRequest ()->getParam ( 'ticket_id' );
		
		$data = $request->getPost ();
		$data['note'] = htmlspecialchars($data['note']);
		
		if (is_numeric ( $id )) {
			TicketsNotes::saveNew($id, $data);
		} else {
			Tickets::saveNew ($data, $customerid );
		}
		
		return $this->_helper->redirector ( 'index', 'tickets', 'default', array ('mex' => 'The task requested has been executed successfully.', 'status' => 'success' ) );
	}
	
	/**
	 * getForm
	 * Get the customized application form 
	 * @return unknown_type
	 */
	private function getForm($action) {
		$form = new Default_Form_TicketsForm ( array ('action' => $action, 'method' => 'post' ) );
		return $form;
	}
	
	/**
	 * SortingData
	 * Manage the request of sorting of the user 
	 * @return string
	 */
	private function sortingData($sort) {
		$strSort = "";
		if (! empty ( $sort )) {
			$sort = addslashes ( htmlspecialchars ( $sort ) );
			$sorts = explode ( "-", $sort );
			
			foreach ( $sorts as $sort ) {
				$sort = explode ( ",", $sort );
				$strSort .= $sort [0] . " " . $sort [1] . ",";
			}
			
			if (! empty ( $strSort )) {
				$strSort = substr ( $strSort, 0, - 1 );
			}
		}
		
		return $strSort;
	}
	
	/**
	 * recordsperpage
	 * Set the number of the records per page
	 * @return unknown_type
	 */
	public function recordsperpageAction() {
		$NS = new Zend_Session_Namespace ( 'Default' );
		$redirector = Zend_Controller_Action_HelperBroker::getStaticHelper ( 'redirector' );
		$records = $this->getRequest ()->getParam ( 'id' );
		if (! empty ( $records ) && is_numeric ( $records )) {
			$NS->recordsperpage = $records;
		} elseif (! empty ( $records ) && $records == "all") {
			$NS->recordsperpage = 999999;
		}
		$redirector->gotoUrl ( '/tickets/' );
	}
	
	/**
	 * bulkexport
	 * Custom function called by the Bulk action method
	 * @param $items
	 * @return url link
	 */
	public function bulkExport($items) {
		if (is_array ( $items )) {
			$fields = "d.domain, d.tld";
			$tickets = $this->tickets->findbyIds ( $fields, $items );
			$cvs = Shineisp_Commons_Utilities::cvsExport ( $tickets );
			die ( json_encode ( array ('mex' => '<a href="/public/documents/export.csv">' . $registry->Zend_Translate->translate ( "download" ) . '</a>' ) ) );
		}
		die ( json_encode ( array ('mex' => $this->translator->translate ( "exporterror" ) ) ) );
	}
	
	/*
     *  bulkAction
     *  Execute a custom function for each item selected in the list
     *  this method will be call from a jQuery script 
     *  @return string
     */
	public function bulkAction() {
		$request = $this->getRequest ();
		$items = $request->getParams ();
		if (! empty ( $items ['params'] )) {
			parse_str ( $items ['params'], $arrparams );
			$action = isset ( $arrparams ['do'] ) ? $arrparams ['do'] : "";
			if (method_exists ( __CLASS__, $action )) {
				$retval = $this->$action ( $arrparams ['item'] );
				if ($retval) {
					die ( json_encode ( array ('mex' => $this->translator->translate ( "The task requested has been executed successfully." ) ) ) );
				}
			} else {
				die ( json_encode ( array ('mex' => $this->translator->translate ( "methodnotset" ) ) ) );
			}
		}
		die ( json_encode ( array ('mex' => $this->translator->translate ( "An error has occured during the task requested." ) ) ) );
	}
	
	/**
	 * dnsGrid
	 * Get the dns zone information.
	 * @return array
	 */
	private function dnsGrid() {
		$request = Zend_Controller_Front::getInstance ()->getRequest ();
		if (isset ( $request->id ) && is_numeric ( $request->id )) {
			$zones = new Dns_Zones ();
			$records = $zones->findAllbyDomain ( $request->id, 'subdomain, zt.zone, target', true );
			if (isset ( $records [0] )) {
				return array ('records' => $records );
			}
		}
	}
}

