<?php

class CartController extends Shineisp_Controller_Default {
	protected $customer;
	protected $session;
	protected $cart;
	protected $translator;
	protected $currency;
	
	/**
	 * preDispatch
	 * Starting of the module
	 * (non-PHPdoc)
	 * @see library/Zend/Controller/Shineisp_Controller_Default#preDispatch()
	 */
	
	public function preDispatch() {
		$session 	= new Zend_Session_Namespace ( 'Default' );
		$cart 		= Zend_Registry::isRegistered('cart');
		
		// Create the cart object
		if(empty($cart)){
			Zend_Registry::set('cart', new Cart());
		}
		
		if (empty($this->customer)) {
			$this->customer = $session->customer;
		}
		
		if (empty($this->session)) {
			$this->session = $session;
		}
		
		$this->currency = Shineisp_Registry::get ( 'Zend_Currency' );
		$this->translator = Shineisp_Registry::get ( 'Zend_Translate' );

		$this->getHelper ( 'layout' )->setLayout ( '2columns-right' );
	}
	
	/**
	 * Redirect the user to the list action
	 */
	public function indexAction() {
		
		try{
			
			$session 	= new Zend_Session_Namespace ( 'Default' );
		
			if (empty ( $session->cart ) || $session->cart->isEmpty()) {
				$session->cart = new Cart();
			}
			
			// Check if the product is present in the cart
			if ($session->cart->checkIfHostingProductIsPresentWithinCart ()) {
				$this->_helper->redirector ( 'domain' );
			} else {
				$this->_helper->redirector ( 'contacts' );
			}

		}catch (Exception $e){
			$this->_helper->redirector ( 'index', 'index', 'default', array('mex' => $e->getMessage()) );
		}
	}
	
	/**
	 * Check the product and redirect the user to the right destination
	 */
	public function addAction() {
		
		try{

			$session 	= new Zend_Session_Namespace ( 'Default' );
		
			if (empty ( $session->cart )) {
				$session->cart = new Cart();
			}
			
			// Get the sent parameters
			$request = $this->getRequest ()->getParams ();
			
			if (! empty ( $request ['product_id'] ) && is_numeric ( $request ['product_id'] )) {
				
				// Check the quantity value posted
				if (! empty ( $request ['quantity'] ) && is_numeric ( $request ['quantity'] )) {
					
					// Get all the info about the product selected
					$product = Products::find ( $request ['product_id'] );
	
					$w1 = new CartItem($request ['product_id'], Products::getProductType($request ['product_id']), $request ['term']);
					
					// Add the items to the cart:
					$session->cart->addItem($w1);
	
					// Update some quantities:
					$session->cart->updateItem($w1, $request ['quantity']);
					
					// Check if a hosting product is present in the cart
					if ($session->cart->checkIfHostingProductIsPresentWithinCart ()) {
						$this->_helper->redirector ( 'domain', 'cart', 'default' );
					} else {
						$this->_helper->redirector ( 'contacts' );
					}
				}
				
				// Quantity is not correct and the user is redirected to the homepage
				$this->_helper->redirector ( 'index', 'index', 'default' );
			}
			
		}catch (Exception $e){
			$this->_helper->redirector ( 'index', 'index', 'default', array('mex' => $e->getMessage()) );
		}
	}

	/*
	 * Show the domain checker form
	 */
	public function domainAction() {
		
		try{
			
			$session = new Zend_Session_Namespace ( 'Default' );
			
			// Create a new form domain checker
			$form = new Default_Form_DomaincheckerForm ( array ('action' => "/cart/checkdomain", 'method' => 'post' ) );
			$this->view->form = $form;
			
			// Create the sidebar if the cart has products
			if (! empty ( $session->cart ) && false === $session->cart->isEmpty()) {
				if ($session->cart->hasDomain()) {
					$this->_helper->redirector ( 'contacts', 'cart', 'default', array ('mex' => 'You can complete the order checkout or delete one or more products and inserting a new one. Just click on the delete link in the cart summary.', 'status' => 'attention' ) );
				}
				$this->view->placeholder ( "right" )->append ( $this->view->partial ( 'partials/cartsidebar.phtml', array ('items' => $session->cart->getItems() ) ) );
			}
			
			$this->_helper->viewRenderer ( 'domain' );

		}catch (Exception $e){
			$this->_helper->redirector ( 'index', 'index', 'default', array('mex' => $e->getMessage()) );
		}
		
	}

	/*
	 * Check the domain availability
	*/
	public function checkdomainAction() {
	
		try{
			
			$session = new Zend_Session_Namespace ( 'Default' );
			
			// Get all the params sent by the customer
			$params = $this->getRequest ()->getParams ();
		
			// redirect the customer to the contact form
			if ( empty($params ['mode']) ) {
				$this->_helper->redirector ( 'contacts' );
				return true;
			}
		
			if (empty ( $params ['domain'] ) || empty ( $params ['tlds'] )) {
				$this->_helper->redirector ( 'domain', 'cart', 'default', array ('mex' => 'The domain is a mandatory field. Choose a domain name.', 'status' => 'error' ) );
			}
		
			// Get the product (tld) selected by the customer
			$tldInfo = DomainsTlds::getAllInfo ( $params ['tlds'] );
		
			// Check if the parameter exists in our database
			if (isset ( $tldInfo ['tld_id'] )) {
		
				// If the owner of the domain wants to transfer the domain ...
				if (! empty ( $params ['mode'] ) && $params ['mode'] == "link") {
		
					if (! empty ( $tldInfo )) {

						// create the domain name
						$domain = trim(strtolower($params ['domain'])) . "." . $tldInfo ['DomainsTldsData'] [0] ['name'];
						
						// get the hosting item added in the cart
						$hostingItem = $session->cart->getHostingItem();
						
						// attach the domain name to the hosting plan
						if($hostingItem){
							$session->cart->addDomain($hostingItem, $domain, $tldInfo['tld_id'], "transfer");
						}
						
						// redirect the customer to the contact form
						$this->_helper->redirector ( 'contacts' );
					}
						
				} else { // If the domain is still free and the customer needs to register it then ...
					
					$strDomain = $params ['domain'] . "." . $tldInfo ['DomainsTldsData'] [0] ['name'];
		
					// Check if the domain is still free
					$result = Domains::isAvailable ( $params ['domain'] . "." . $tldInfo ['DomainsTldsData'] [0] ['name'] );
		
					if ($result) { // If it is free
		
						// create the domain name
						$domain = trim(strtolower($params ['domain'])) . "." . $tldInfo ['DomainsTldsData'] [0] ['name'];
						
						// get the hosting item added in the cart
						$hostingItem = $session->cart->getHostingItem();
						
						// attach the domain name to the hosting plan
						if($hostingItem){
							$session->cart->addDomain($hostingItem, $domain, $tldInfo['tld_id'], "register");
						}
		
						// Redirect the user to the
						$this->_helper->redirector ( 'contacts', 'cart', 'default', array ('mex' => 'The domain is available for registration', 'status' => 'success' ) );
		
					} else {
						// If not redirect the customer to choose another name
						$this->_helper->redirector ( 'domain', 'cart', 'default', array ('mex' => 'The domain is not available for registration. Choose another domain name.', 'status' => 'error' ) );
					}
				}
			}

			$this->_helper->redirector ( 'domain', 'cart', 'default', array ('mex' => 'The domain is available for registration', 'status' => 'success' ) );

		}catch (Exception $e){
			$this->_helper->redirector ( 'index', 'index', 'default', array('mex' => $e->getMessage()) );
		}
	}
	
	/*
     * Get the customer information
     */
	public function contactsAction() {

		try{
			$request = $this->getRequest ();
			$session = new Zend_Session_Namespace ( 'Default' );
			Zend_Debug::dump($session->cart);
			
			if (empty ( $session->cart ) || $session->cart->isEmpty()) {
				$this->_helper->redirector ( 'index', 'index', 'default', array('mex' => "Cart is empty") );
			}
			
			// Check if there is a domain service within the cart.
			// If a domain is present we have to create a nic-handle in order to register the 
			// customer in the remote registrant database
			$hasdomain = $session->cart->hasDomain ();
			
			// Check if the user has been logged in
			if (!empty($this->customer)) {
				$customer = $this->customer;
				
				// Set the customer for the active cart
				$session->cart->setCustomer($customer['customer_id']);
				
				// Check if the customer is a reseller
				if (! empty ( $customer ['isreseller'] ) && $customer ['isreseller']) {
					$session->cart->setReseller($customer['customer_id']);
					$this->_helper->redirector ( 'reseller', 'cart', 'default' );
				} else {
					$session->cart->removeReseller();
				}
				
				$this->view->contact = $customer;
				$this->_helper->viewRenderer ( 'contactlogged' );
			
			} else {
				// Clean the session vars
				$session->cart->removeCustomer();
				$session->cart->removeReseller();
			}
			
			// Create the sidebar if the cart has products
			$items = $session->cart->getItems();
			$this->view->placeholder ( "right" )->append ( $this->view->partial ( 'partials/cartsidebar.phtml', array ('items' => $items ) ) );
			
			$this->view->hasdomain = $hasdomain;

			// Create the form
			$form = new Default_Form_CustomerForm ( array ('action' => "/cart/contacts", 'method' => 'post' ) );
			$form->getElement ( 'submit' )->setLabel ( 'Continue Order' );
			$form->populate ( array('country_id' => 82, 'contacttypes' => 1) ); // Set Italy as default and the first contact type id 1 as telephone
			
			if ($session->cart->getCustomer()) {
				$form->populate ( $session->cart->getCustomer() );
			}
			
			// If the product/service include a domain we need more information
			if ($hasdomain === false){
				$form->getElement ( 'sex' )->setRequired ( false );
				$form->getElement ( 'sex' )->setRegisterInArrayValidator ( false );
				$form->getElement ( 'birthdate' )->setRequired ( false );
				$form->getElement ( 'birthplace' )->setRequired ( false );
				$form->getElement ( 'birthdistrict' )->setRequired ( false );
				$form->getElement ( 'birthcountry' )->setRequired ( false );
				$form->getElement ( 'birthnationality' )->setRequired ( false );
			}
			
			$this->view->form = $form;
			
			// Check if we have a POST request
			if ($request->isPost ()) {
				$params = $request->getPost ();
				
				if ($form->isValid ( $params )) {
					
					// Send the confirmation email to the customer
					$params['welcome_mail'] = true;
					
					// Create a customer or get his ID
					$result = Customers::Create( $params );
					
					if (is_numeric ( $result )) {
						
						// Do the login
						if($this->doLogin ( $result )){
							$this->_helper->redirector ( 'payment', 'cart', 'default', array ('mex' => 'Well done! Now you have to choose your preferite payment method.', 'status' => 'success' ) );
						}
					} else {
						$this->view->mex = $result;
						$this->view->mexstatus = "error";
					}
				}
			}

		}catch (Exception $e){
			$this->_helper->redirector ( 'index', 'index', 'default', array('mex' => $e->getMessage()) );
		}
	}
	
	/*
     * Show the logged reseller information
     */
	public function resellerAction() {
		$this->session = new Zend_Session_Namespace ( 'Default' );
		
		if (! isset ( $cart->products ) || count ( $cart->products ) == 0) {
			$this->_helper->redirector ( 'index', 'index', 'default' );
		}
		
		$request = $this->getRequest ();
		
		if (!empty($this->session->customer)) {
			$cart->reseller = $this->session->customer;
		} else {
			unset ( $cart->reseller );
			$this->_helper->redirector ( 'contacts', 'cart', 'default' );
		}
		
		// Create the sidebar if the cart has products
		if (! isset ( $cart->products )) {
			$this->_helper->redirector ( 'index', 'index', 'default' );
		} else {
			$items = $cart->products;
			$this->view->placeholder ( "right" )->append ( $this->view->partial ( 'partials/cartsidebar.phtml', array ('items' => $items ) ) );
		}
		
		// Create the item for the customers select object 
		$criteria = array (array ('where' => "parent_id = ?", 'params' => $cart->reseller ['customer_id'] ) );
		
		// Create the form
		$form = new Default_Form_ResellerForm ( array ('action' => "/cart/reseller", 'method' => 'post' ) );
		
		// Get the reseller information 
// 		$reseller = array ($cart->reseller ['customer_id'] => $cart->reseller ['firstname'] . " " . $cart->reseller ['lastname'] . " - " . $cart->reseller ['company'] );
		
		// Get the customers connected to the reseller
		$customers = Customers::getList ( false, $criteria );
		
		$form->getElement ( 'submit' )->setLabel ( 'Continue Order' );
		
		// Assign the customers to the select object
// 		$form->getElement ( 'customers' )->setMultiOptions ( $reseller + $customers );
		$this->view->form = $form;
		
		// Check if we have a POST request
		if ($request->isPost ()) {
			$params = $request->getPost ();
			
			if ($form->isValid ( $params )) {
				$cart->contacts = Customers::getAllInfo ( $params ['customers'], "c.customer_id, a.address_id, cts.type_id, l.legalform_id, ct.country_id, cn.contact_id, s.status_id, c.*, a.*, l.*, cn.*, cts.*, s.*" );
				$this->_helper->redirector ( 'payment', 'cart', 'default' );
			}
		}
		
		$this->view->reseller = $cart->reseller;
		$this->_helper->viewRenderer ( 'reseller' );
	}
	

	
	
	/*
     * Show the contact form
     */
	public function simplecontactsAction() {
		$this->session = new Zend_Session_Namespace ( 'Default' );
		
		unset ( $cart->reseller );
		
		if (! isset ( $cart->products ) || count ( $cart->products ) == 0) {
			$this->_helper->redirector ( 'index', 'index', 'default' );
		} else {
			$items = $cart->products;
			$this->view->placeholder ( "right" )->append ( $this->view->partial ( 'partials/cartsidebar.phtml', array ('items' => $items ) ) );
		}
		
		$request = $this->getRequest ();
		
		$form = new Default_Form_CartsimpleprofileForm ( array ('action' => "/cart/simplecontacts", 'method' => 'post' ) );
		
		// Fill the form if the user has already write his/her information
		if (isset ( $cart->contacts ) && is_array ( $cart->contacts )) {
			$form->populate ( $cart->contacts );
		}
		
		$form->getElement ( 'save' )->setLabel ( 'Continue Order' );
		$this->view->form = $form;
		
		// Check if we have a POST request
		if ($request->isPost ()) {
			$params = $request->getPost ();
			
			if ($form->isValid ( $params )) {
				
				$params = $request->getPost ();
				$cart->contacts = $params;
				
				// Check if there is a domain service within the cart.
				// If a domain is present we have to create a nic-handle in order to register the 
				// customer in the remote registrant database
				$hasdomain = $this->hasDomain ();
				
				// Create a customer or get his ID
				$customerid = $this->CreateCustomer ( $params, $hasdomain );
				
				// Do the login
				$this->doLogin ( $customerid );
				
				$this->_helper->redirector ( 'payment', 'cart', 'default', array ('mex' => 'Well done! Now you have to choose your preferite payment method.', 'status' => 'success' ) );
			}
		}
		
	}

	
	/*
     * Request the payment of the order
     */
	public function paymentAction() {
		$session = new Zend_Session_Namespace ( 'Default' );
			
		$request = $this->getRequest ();
		
		if (empty ( $session->cart ) || $session->cart->isEmpty()) {
			#$this->_helper->redirector ( 'index', 'index', 'default', array('mex' => "Cart is empty") );
		}
		
		// Get the heading of the cart 
		$cart_heading = $session->cart->getHeading();
		
// 		Zend_Debug::dump($cart_heading);
// 		die;
		
		// Check if the user is VAT free
		$isVATFree = Customers::isVATFree($cart_heading['customer']['id']);
		
		// Set the template
		$this->getHelper ( 'layout' )->setLayout ( '1column' );

		// Get the form
		$form = new Default_Form_CartsummaryForm ( array ('action' => '/cart/payment', 'method' => 'post' ) );
		
// 		foreach ( $session->cart->products as &$products ) {
// 			$products['tax_id'] = ($isVATFree) ? null : $products['tax_id'];
// 		}
		
		// Add the sidebar with the list of the product in the cart
		$this->view->placeholder ( "shoppingcart" )->append ( $this->view->partial ( 'partials/shoppingcart.phtml', array ('items' => $session->cart->getItems() ) ) );
		
		$this->view->containhosting = ($session->cart->checkIfHostingProductIsPresentWithinCart ()) ? 1 : 0;
		Zend_Debug::dump($request);
		die;
		// Check if we have a POST request
		if ($request->isPost ()) {
			$params = $request->getPost ();
			
			if(empty($params ['payment'])){
				$this->_helper->redirector ( 'payment', 'cart', 'default', array ('mex' => 'Please select the payment method.', 'status' => 'error' ) );
				die;
			}
			
			if ($form->isValid ( $params )) {
				
				/**
				 * Create the order
				 * create($customerId, $statusId = 9, $note = "")
				 * $statusId = 9 --> To be pay 
				 * 
				 */
				 
				$theOrder = Orders::create ( $cart_heading['customer']['id'], Statuses::id('tobepaid', 'orders'), $params ['note'] );
				
				$items = $session->cart->getItems();
				Zend_Debug::dump($items);
				die;
				
				foreach ( $items as $item ) {
					
					
					// Check the Tranche selected by the user
					if (! empty ( $product ['trancheid'] )) {
						$trancheID = $product ['trancheid'];
					} else {
						$trancheID = null;
					}
					
					if ($product ['type'] == "domain") {
						$domain = DomainsTlds::getAllInfo ( $product ['tld_id'] );
						
						$action = $product ['isavailable'] ? "registerDomain" : "transferDomain";
						$price  = $product ['isavailable'] ? $domain ['registration_price'] : $domain ['transfer_price'];
						$cost   = $product ['isavailable'] ? $domain ['registration_cost']  : $domain ['transfer_cost'];
                        
                        // Check if domain included in a hosting
						$price = ( $this->checkIfDomainIncluse( $product['domain_selected']) == false ) ? 0 : $price;

						// Create the order item for the domain
						Orders::addOrderItem ( $theOrder ['order_id'], $product ['domain_selected'], 1, 3, $price, $cost, 0, array ('domain' => $product ['domain_selected'], 'action' => $action, 'authcode' => '', 'tldid' => $domain ['tld_id']) );
					
					} else {
						// Create the order item for other products

						Orders::addItem ( $product ['product_id'], $product ['quantity'], $product ['billingid'], $trancheID, $product['ProductsData'][0]['name'], array() );
					}
				}
				
				$orderID = $theOrder ['order_id'];
				Orders::sendOrder ( $orderID );
				
				// Set the order ID
				$cart->orderid = $orderID;
				
				// Get the totals
				$cart->totals = $this->Totals ();
				$cart->payment->notes = ! empty ( $params ['note'] ) ? $params ['note'] : "";
				
				// Calculate the Grand Total
				$amount = $cart->totals ['total'];
				
				if (is_numeric ( $params ['payment'] )) {
					$cart->payment->id = $params ['payment'];
					$this->_helper->redirector ( 'gateway', 'cart', 'default' );
				} else {
					$this->_helper->redirector ( 'index', 'index', 'default' );
					unset ( $cart );
				}
			
			}
		} else {
			
			$this->view->isVATFree	= $isVATFree;
			$this->view->cart = $cart;
			$this->view->totals = $this->Totals ();
			$this->view->form = $form;
			
		}
	}
	
	/**
	 * Create the payment gateway form
	 */
	public function gatewayAction() {
		$this->session = new Zend_Session_Namespace ( 'Default' );
		
		$orderID = $cart->orderid;
		
		// Get the payment form object
		$banks = Banks::find ( $cart->payment->id, "*", true );
		if (! empty ( $banks [0] ['classname'] )) {
			
			$class = $banks [0] ['classname'];
			
			if (class_exists ( $class )) {
				
				// Get the payment form object
				$banks = Banks::findbyClassname ( $class );
				
				$gateway = new $class ( $orderID );
				$gateway->setFormHidden ( true );
				$gateway->setRedirect ( true );
				
				$gateway->setUrlOk ( $_SERVER ['HTTP_HOST'] . "/orders/response/gateway/" . md5 ( $banks ['classname'] ) );
				$gateway->setUrlKo ( $_SERVER ['HTTP_HOST'] . "/orders/response/gateway/" . md5 ( $banks ['classname'] ) );
				$gateway->setUrlCallback ( $_SERVER ['HTTP_HOST'] . "/common/callback/gateway/" . md5 ( $banks ['classname'] ) );
				
				$this->view->gateway = $gateway->CreateForm ();
				$this->_helper->viewRenderer ( 'gateway' );
				
				// Destroy the cart
				unset ( $cart );
			
			} else {
				$this->_helper->redirector ( 'payment', 'cart', 'default' );
			}
		}
	
	}
	
	/*
     * Delete a product or domain from the cart list 
     */
	public function deleteAction() {
		$this->session = new Zend_Session_Namespace ( 'Default' );
		
		// Get the parameters
		$params = $request = $this->getRequest ()->getParams ();
		
		// Get all the cart products
		$products = $cart->products;
		
		$index = 0;
		
		// If the product is a domain delete the temporary session domain information
		if (! empty ( $params ['tld'] )) {
			foreach ( $products as $key => $product ) {
			    #Delete domain in hosting if is incluse
                if (! empty ( $product ['domain_selected'] ) && $product ['domain_selected'] == $params ['tld']) {
					unset ( $products [$index] );  // Delete the product from the session cart 
					$cart->products = array_values ( $products );
					unset ( $cart->domain );
				} elseif( $products['type'] == 'hosting' && $products['site'] == $params ['tld']) {
                   unset( $cart->products[$key]['site'] ); 
                }
                
				$index ++;
			}
		}
		
		// Check if the product selected is a common product 
		if (! empty ( $params ['product'] ) && is_numeric ( $params ['product'] )) {
			
			// Cycle all the products inserted in the cart
			foreach ( $products as $product ) {
				
				// Matching of the product selected and the product cycled
				if ($product ['product_id'] == $params ['product']) {
					
					// Delete the product from the session cart 
					unset ( $products [$index] );
					$cart->products = array_values ( $products );
					break;
				}
				$index ++;
			}
		} elseif (! empty ( $params ['product'] ) && $params ['product'] == "all") {
			unset ( $cart );
		}
		
		if (! empty ( $cart->products ) && count ( $cart->products ) > 0) {
			if ($this->checkIfHostingProductIsPresentWithinCart ()) {
				$this->_helper->redirector ( 'domain', 'cart', 'default', array ('mex' => 'The domain has been deleted from the cart list. Choose another domain name.', 'status' => 'success' ) );
			} else {
				$this->_helper->redirector ( 'contacts', 'cart', 'default', array ('mex' => 'The domain has been deleted from the cart list.', 'status' => 'success' ) );
			}
		} else {
			$this->_helper->redirector ( 'index', 'index', 'default' );
		}
	
	}
	
	
	/*
	 * Total
	 * Create the total of the order
	 */
	private function Totals() {
		$this->session = new Zend_Session_Namespace ( 'Default' );
		
		$isVATFree = Customers::isVATFree($cart->contacts['customer_id']);
		
		if (! empty ( $cart->products ) && is_array ( $cart->products )) {
			$products = $cart->products;
			$total = 0;
			$tax   = 0;
			$taxes = 0;
			
			// Read all the product added in the cart
			foreach ( $products as $product ) {
				$price = ($product ['price_1'] * $product ['quantity']) + $product ['setupfee'];
				$vat = 0;
				$tax = 0;
				
				// check the taxes for each product
				if (! empty ( $product ['tax_id'] ) && !$isVATFree) {
					$tax = Taxes::find ( $product ['tax_id'], null, true );
					if (! empty ( $tax [0] ['percentage'] ) && is_numeric ( $tax [0] ['percentage'] ) ) {
						$percentage = $tax [0] ['percentage'];
						$vat = ($price * $percentage) / 100;
						$price = ($price * (100 + $percentage)) / 100;
					}
				}
				$total += $price;
				$taxes += $vat;
			}
			$total = $this->currency->toCurrency($total, array('currency' => Settings::findbyParam('currency')));
			$taxes = $this->currency->toCurrency($taxes, array('currency' => Settings::findbyParam('currency')));
			return array ('total' => $total, 'taxes' => $taxes );
		} else {
			return array ('total' => '0', 'taxes' => '0' );
		}
	}
	
	/**
	 * Do the login action
	 * 
	 * @param integer $customerid
	 */
	private function doLogin($customerid) {
		
		if(is_numeric($customerid)){
			$session = new Zend_Session_Namespace ( 'Default' );
			$auth = Zend_Auth::getInstance ();
			$auth->setStorage ( new Zend_Auth_Storage_Session ( 'default' ) );
			
			$result = new Zend_Auth_Result ( Zend_Auth_Result::SUCCESS, null );
			$customer = Customers::getAllInfo ( $customerid, "c.customer_id, a.address_id, cts.type_id, l.legalform_id, ct.country_id, cn.contact_id, s.status_id, c.*, a.*, l.*, cn.*, cts.*, s.*" );
			
			// We're authenticated!
			$auth->getStorage ()->write ( $customer );
			
			// Set the owner of the cart
			$session->cart->setCustomer($customerid);
			
			return $customer;
		}
		
		return false;
	}
	
	/*
	 * Create a customer using the cart information and it creates 
	 * the Nic Handle in order to communicate with the default registrars 
	 */
	private function CreateCustomer($params, $createNicHandle = false) {
		
		$params ['contacttypes'] = 1; // Telephone
		$params ['contact'] = $params ['telephone'];
		
		$customerID = Customers::Create ( $params );
		//		$result = CustomersDomainsRegistrars::chkIfCustomerExist ( $customerID );
		

		// If there is no nic for the customer ...
		//		if ($result == 0) {
		//			
		//			// Create the nic-handle using the default registrar
		//			if ($createNicHandle) {
		//				$retval = Customers::CreateNicHandle ( $customerID );
		//				
		//				// If the customer has written wrong information, the record is deleted.
		//				if ($retval === false || is_string ( $retval )) {
		//					Customers::del ( $customerID );
		//					return $retval;
		//				}
		//			}
		

		//		}
		

		return $customerID;
	}
	
	/*
     * Delete the cart session
     */
	public function deletesessionAction() {
		$this->session = new Zend_Session_Namespace ( 'Default' );
		
		if (is_numeric ( $cart->contacts ['customer_id'] )) {
			Customers::del ( $cart->contacts ['customer_id'] );
		}
		unset ( $cart );
		$this->_helper->redirector ( 'out', 'index', 'default' );
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function getcompanytypesAction() {
		// Get the parameters
		$id = $request = $this->getRequest ()->getParam ( 'id' );
		if (! empty ( $id ) && is_numeric ( $id )) {
			echo json_encode ( CompanyTypes::getListbyLegalformID ( $id ) );
		}
		die ();
	}
}