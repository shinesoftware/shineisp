<?php

/**
 * ProfileController
 * Manage the isp profile
 * @version 1.0
 */

class ProfileController extends Shineisp_Controller_Default {
	
	protected $profile;
	protected $translator;
	
	/**
	 * preDprofileatch
	 * Starting of the module
	 * (non-PHPdoc)
	 * @see library/Zend/Controller/Shineisp_Controller_Default#preDprofileatch()
	 */
	
	public function preDispatch() {
		$registry = Shineisp_Registry::getInstance ();
		$ns = new Zend_Session_Namespace ();
		
		if (!empty($ns->customer)) {
			$this->profile = $ns->customer;
		} else {
			return $this->_helper->redirector ( 'out', 'login', 'default' );
		}
		$this->translator = $registry->Zend_Translate;
		$this->getHelper ( 'layout' )->setLayout ( '1column' );
	}
	
	/**
	 * indexAction
	 * Create the User object and get all the records.
	 * @return unknown_type
	 */
	public function indexAction() {
		$redirector = Zend_Controller_Action_HelperBroker::getStaticHelper ( 'redirector' );
		$redirector->gotoUrl ( '/default/profile/account' );
	}
	
	/**
	 * getForm
	 * Get the customized application form 
	 * @return unknown_type
	 */
	private function getForm($action) {
		$form = new Default_Form_ProfileForm ( array ('action' => $action, 'method' => 'post' ) );
		return $form;
	}
	
	/**
	 * accountAction
	 * Manage the profile account settings.
	 * @return void
	 */
	public function accountAction() {
		$form = $this->getForm ( '/profile/process' );
		$this->view->form = $form;
		
		$this->view->mex = $this->getRequest ()->getParam ( 'mex' );
		$this->view->mexstatus = $this->getRequest ()->getParam ( 'status' );
		
		$this->view->title = $this->translator->translate("Profile details");
		$this->view->description = $this->translator->translate("Update here your details filling the applicant form with all the information about you.");
		
		$rs = Customers::getAllInfo ( $this->profile ['customer_id'], "c.customer_id as customer_id, c.firstname as firstname, c.lastname as lastname, c.company as company, c.type_id as company_type_id, c.legalform_id as legalform, c.email as email, c.vat as vat, a.address as address, a.city as city, a.code as code, a.country_id as country_id, a.area as area, DATE_FORMAT(c.birthdate,'%d/%m/%Y') as birthdate, c.birthplace as birthplace, c.taxpayernumber as taxpayernumber, c.gender as gender, c.birthdistrict as birthdistrict, c.birthcountry as birthcountry, c.birthnationality as birthnationality, c.issubscriber as newsletter" );
		
		if (! empty ( $rs )) {
			$form->populate ( $rs );
			$this->view->contactsdatagrid = $this->contactsGrid ( $this->profile ['customer_id'] );
			$this->view->clients = $this->getClients ( $this->profile ['customer_id'] );
			$this->view->isReseller = Customers::isReseller($this->profile ['customer_id']);
		}else{
			$this->view->isReseller = false;
		}
		
		$this->_helper->viewRenderer ( 'applicantform' );
	
	}
	
	/**
	 * getClients
	 * Get all the contacts of the customer
	 * @return unknown_type
	 */
	private function getClients($customer_id) {
		if (is_numeric ( $customer_id )) {
			$rs = Customers::getClients( $customer_id, "company, firstname, lastname, email, password" );

			if (isset ( $rs[0] )) {
				$email = md5($rs[0]['email']);
				$password = $rs[0]['password'];
				
				// hide the password value 
				unset($rs[0]['password']);
				
				// http://www.shineisp.it/default/index/fastlogin/id/84eb99c2cae8c8f892fb359373d30a68-889842705797842ba33dcae0eac1cf03
				return array ('records' => $rs, 'actions' => array ("/index/fastlogin/id/$email-$password/" => 'login' ) );
			}
		}
	}
	
	/**
	 * contactsGrid
	 * Get all the contacts of the customer
	 * @return unknown_type
	 */
	private function contactsGrid($customer_id) {
		if (is_numeric ( $customer_id )) {
			$rs = Contacts::getContacts ( $customer_id );
			if (isset ( $rs )) {
				return array ('records' => $rs, 'delete' => array ('controller' => 'profile', 'action' => 'deletecontact' ) );
			}
		}
	}
	
	/**
	 * confirmdeleteAction
	 * Ask to the customer a confirmation before the deletion of the record selected
	 * @return unknown_type
	 */
	public function confirmdeleteAction() {
		
		$id = $this->getRequest ()->getParam ( 'id' );
		if (is_numeric ( $id )) {
			$contact = Contacts::find ( $id, null, true );
			if (Contacts::find ( $id )) {
				$this->view->contact = $contact;
			} else {
				$this->_helper->redirector ( 'account', 'profile', 'default' );
			}
			$this->_helper->viewRenderer ( 'confirmdelete' );
		}
	}
	
	/**
	 * deleteAction
	 * Delete a contact of the customer
	 * @return unknown_type
	 */
	public function deletecontactAction() {
		$id = $this->getRequest ()->getParam ( 'id' );
		if (is_numeric ( $id )) {
			Contacts::find ( $id )->delete ();
		}
		$this->_helper->redirector ( 'account', 'profile', 'default' );
	}
	
	/**
	 * processAction
	 * Update the record previously selected
	 * @return unknown_type
	 */
	public function processAction() {
		$redirector = Zend_Controller_Action_HelperBroker::getStaticHelper ( 'redirector' );
		$form = $this->getForm ( "/profile/process" );
		$request = $this->getRequest ();
		
		// Check if we have a POST request
		if (! $request->isPost ()) {
			return $this->_helper->redirector ( 'index', 'index', 'default' );
		}
		
		if ($form->isValid ( $request->getPost () )) {
			// Get the id 
			$id = $this->getRequest ()->getParam ( 'customer_id' );
			try {
				// Set the new values
				if (is_numeric ( $id )) {
					$customer = Doctrine::getTable ( 'Customers' )->find ( $id );
					
					$oldCustomer = $customer->toArray();
					
					// Get the values posted
					$params = $form->getValues ();
					
					$customer->company = $params ['company'];
					$customer->firstname = $params ['firstname'];
					$customer->lastname = $params ['lastname'];
					$customer->email = $params ['email'];
					$customer->birthdate = Shineisp_Commons_Utilities::formatDateIn ( $params ['birthdate'] );
					if (! empty ( $params ['password'] )) {
						$customer->password = MD5($params ['password']);
					}
					$customer->birthplace = $params ['birthplace'];
					$customer->birthdistrict = $params ['birthdistrict'];
					$customer->birthcountry = $params ['birthcountry'];
					$customer->birthnationality = $params ['birthnationality'];
					$customer->vat = $params ['vat'];
					$customer->taxpayernumber = $params ['taxpayernumber'];
					$customer->type_id = ! empty ( $params ['company_type_id'] ) ? $params ['company_type_id'] : NULL;
					$customer->legalform_id = $params ['legalform'];
					$customer->gender = $params ['gender'];
					
					// Save the data
					$customer->save ();
					$id = is_numeric ( $id ) ? $id : $customer->getIncremented ();
					
					// Manage the address of the customer 
					$address = new Addresses ( );
					$mainAddress = $address->findOneByUserId ( $id );
					if ($mainAddress) {
						$address = $mainAddress;
					}
					
					$address->address = $params ['address'];
					$address->city = $params ['city'];
					$address->code = $params ['code'];
					$address->country_id = $params ['country_id'];
					$address->area = $params ['area'];
					$address->customer_id = $id;
					$address->save ();
					
					if (! empty ( $params ['contact'] )) {
						$contacts = new Contacts ( );
						$contacts->contact = $params ['contact'];
						$contacts->type_id = $params ['contacttypes'];
						$contacts->customer_id = $id;
						$contacts->save ();
					}
					
					// Add or Remove the customer email in the newsletter list
					Customers::newsletter_subscription($id, $params ['newsletter']);
		
					$retval = Shineisp_Commons_Utilities::getEmailTemplate ( 'profile_changed' );
					if ($retval) {
						$subject = $retval ['subject'];
						$subject = str_replace ( "[user]", $params ['firstname'] . " " . $params ['lastname'], $retval ['subject'] );
						
						// Alert the administrator about the changing of the customer information
						$body = $retval ['template'];
						$body = str_replace ( "[user]", $params ['firstname'] . " " . $params ['lastname'], $body );
						$body = str_replace ( "[old]", print_r($oldCustomer, true), $body );
						$body = str_replace ( "[new]", print_r($customer->toArray(), true), $body );
						$isp = Shineisp_Registry::get('ISP');
						Shineisp_Commons_Utilities::SendEmail ( $isp->email, $isp->email, null, $subject, $body );
					}
				}
			} catch ( Exception $e ) {
				echo $e->getMessage ();
				die ();
			}
			return $this->_helper->redirector ( 'account', 'profile', 'default', array ('mex' => 'The task requested has been executed successfully.', 'status' => 'success' ) );
		} else {
			$this->view->form = $form;
			$this->view->title = $this->translator->translate("Profile details");
			$this->view->description = $this->translator->translate("Update here your details filling the applicant form with all the information about you.");
			return $this->_helper->viewRenderer ( 'applicantform' );
		}
	
	}

}
    