<?php

class CartController extends Zend_Controller_Action {
	protected $customer;
	protected $cart;
	protected $translator;
	protected $currency;
	
	/**
	 * preDispatch
	 * Starting of the module
	 * (non-PHPdoc)
	 * @see library/Zend/Controller/Zend_Controller_Action#preDispatch()
	 */
	
	public function preDispatch() {
		$ns = new Zend_Session_Namespace ( 'Default' );
		
		if (!empty($ns->customer)) {
			$this->customer = $ns->customer;
		}
		
		$this->currency = Zend_Registry::get ( 'Zend_Currency' );
		$this->translator = Zend_Registry::get ( 'Zend_Translate' );
		
		$this->getHelper ( 'layout' )->setLayout ( '2columns-right' );
	}
	
	/**
	 * indexAction
	 * Redirect the user to the list action
	 * @return unknown_type
	 */
	public function indexAction() {
		$NS = new Zend_Session_Namespace ( 'Default' );
		
		if (! empty ( $NS->cart->products ) && count ( $NS->cart->products ) == 0) {
			unset ( $NS->cart->products );
		}
		
		// Check if the product is present in the cart
		if ($this->checkIfHostingProductIsPresentWithinCart ()) {
			$this->_helper->redirector ( 'domain' );
		} else {
			$this->_helper->redirector ( 'contacts' );
		}
	}
	
	private function getPricesWithRefundIfIsRecurring( $orderid, $price, $billing_cicle_id ) {
		$refundInfo		= OrdersItems::getRefundInfo($orderid);
		if( $refundInfo != false ) {
			$refund					= $refundInfo['refund'];
			$idBillingCircle		= $billing_cicle_id;
			$monthBilling			= BillingCycle::getMonthsNumber($idBillingCircle);
			$priceToPay				= $price * $monthBilling;
			$priceToPayWithRefund	= $priceToPay - $refund;
			if( $priceToPayWithRefund < 0 ) {
				$priceToPayWithRefund	= $priceToPay;
			}
			
			return round( $priceToPayWithRefund / $monthBilling,2 );
		}	
		
		return false;	
	}
	
	private function getPriceWithRefund( $orderid, $price ) {
		$refundInfo		= OrdersItems::getRefundInfo($orderid);
		if( $refundInfo != false ) {
			$refund					= $refundInfo['refund'];
			$priceToPayWithRefund	= $price - $refund;
			if( $priceToPayWithRefund > 0 ) {
				return $priceToPayWithRefund;
			}
			
			return $price; 
		}
		
		return false;
	}
	
	private function checkIfIsUpgrade( $productid ){
		$NS = new Zend_Session_Namespace ( 'Default' );
		if( is_array($NS->upgrade) ) {
			//Check if the product is OK for upgrade and if OK take refund
			foreach( $NS->upgrade as $orderid => $upgradeProduct ) {
				if( in_array( $productid, $upgradeProduct) ) {
					return $orderid;
				}
			}
			
		}
		return false;
	}

	/**
	 * Check the product and redirect the user to the right destination
	 */
	public function addAction() {
    	
		$NS = new Zend_Session_Namespace ( 'Default' );
		$tranche = "";
		
		// Get the sent parameters
		$request = $this->getRequest ()->getParams ();
		
		if (! empty ( $request ['product_id'] ) && is_numeric ( $request ['product_id'] )) {
			
			// Check the quantity value posted
			if (! empty ( $request ['quantity'] ) && is_numeric ( $request ['quantity'] )) {
				
				// Get all the info about the product selected
				$product = Products::getAllInfo ( $request ['product_id'] );
				
				// Get the categories
				$product ['cleancategories'] = ProductsCategories::getCategoriesInfo ( $product ['categories'] );
				
				$product['parent_orderid']		= "";
				if ($request ['isrecurring']) {
					
					// Get the tranche selected
					$tranche = ProductsTranches::getTranchebyId ( $request ['quantity'] );
					
					$product ['isrecurring'] = true;
					$product ['quantity'] 	= $tranche ['quantity'];
					$product ['trancheid'] 	= $tranche ['tranche_id'];
					$product ['billingid'] 	= $tranche ['billing_cycle_id'];
					$product ['price_1'] 	= $tranche ['price'] * $tranche ['BillingCycle'] ['months'];
					$product ['setupfee']	= $tranche ['setupfee'];
					
					// JAY 20130409 - Add refund if exist
					//Check if the product is OK for upgrade
					$orderid = $this->checkIfIsUpgrade( $request ['product_id'] );
					
					if( $orderid != false ) {
						unset ( $NS->cart );
						$NS->cart->products [] 				= $product;
						$NS->cart->contacts['customer_id']	= $NS->customer['customer_id'];
						//add new order
						$theOrder = Orders::create ( $NS->customer['customer_id'], Statuses::id('tobepaid', 'orders') );
						$trancheID 	= $tranche['tranche_id'];
						Orders::addItem ( $product ['product_id'], 1, $product ['billingid'], $trancheID, $product['ProductsData'][0]['name'], array(), $orderid );
						
						$orderID = $theOrder ['order_id'];
						Orders::sendOrder ( $orderID );
						
						unset ( $NS->cart );
						$this->_helper->redirector->gotoUrl ( 'orders/edit/id/'.$orderID );				
						exit();						
					} 				
					/*** 20130409 ***/
					
					
				} else {
					$product ['isrecurring'] = false;
					// JAY 20130409 - Add refund if exist
					$orderid	= $this->checkIfIsUpgrade( $request ['product_id'] );
					if( $orderid != false ) {
						$product['parent_orderid']	= $orderid;
						$product ['price_1'] = $this->getPriceWithRefund($orderid, $product ['price_1']);
						
						unset ( $NS->cart );
						$NS->cart->products [] 				= $product;
						$NS->cart->contacts['customer_id']	= $NS->customer['customer_id'];
						//add new order
						$theOrder = Orders::create ( $NS->customer['customer_id'], Statuses::id('tobepaid', 'orders') );
						Orders::addItem ( $product ['product_id'], 1, 5, false, $product['ProductsData'][0]['name'], array(), $orderid );
						
						$orderID = $theOrder ['order_id'];
						Orders::sendOrder ( $orderID );
						
						unset ( $NS->cart );
						$this->_helper->redirector->gotoUrl ( 'orders/edit/id/'.$orderID );				
						exit();								
					}						
					/** 20130409 **/					
					
					$product ['quantity'] = $request ['quantity'];
					$product ['billingid'] = 5; // No expiring
				}
				
				// Check if the product is already within the cart 
				if ($this->checkIfProductIsPresentWithinCart ( $product )) {
					// Delete the old product and add the new one
					$this->changeProduct ( $product );
				} else {
					// Check if the cart contains a hosting plan and if it is already in the cart change the old product with the new one
					if ($product ['type'] == "hosting") {
						if ($this->checkIfHostingProductIsPresentWithinCart ()) {
							// Delete the old product and add the new one
							$this->changeHostingPlan ( $product );
						} else {
							$NS->cart->products [] = $product;
						}
					} else {
						$NS->cart->products [] = $product;
					}
				}
				
				
				// Check if the product is present in the cart
				if ($this->checkIfHostingProductIsPresentWithinCart ()) {
					$this->_helper->redirector ( 'domain', 'cart', 'default' );
				} else {
					$this->_helper->redirector ( 'contacts' );
				}
			}
		}
		$this->_helper->redirector ( 'index', 'index', 'default' );
	}

    private function findHostingWithoutSite( ) {
        $NS = new Zend_Session_Namespace ( 'Default' );
     
        $lastproduct    = "";
        if( isset( $NS->cart->lastproduct ) ) {
            $lastproduct    = $NS->cart->lastproduct;
        }   
        
        foreach( $NS->cart->products as $key => $products ) {
            if( $products['type'] == 'hosting' ) {
                if( $products['uri'] == $lastproduct && ( ! isset($products['domain']) || $products['domain'] != false ) ) {
                    return array( 'key' => $key, 'value' => $products );
                } elseif( ! isset($products['domain']) || $products['domain'] != false ) {
                    return array( 'key' => $key, 'value' => $products );
                }
            }
        }
        
        return false;
    }
	/*
     * Check the domain availability
     */
	public function checkdomainAction() {
		$NS = new Zend_Session_Namespace ( 'Default' );
		
		// Get all the params sent by the customer
		$params = $this->getRequest ()->getParams ();
		
		
		if (empty ( $params ['domain'] ) || empty ( $params ['tlds'] )) {
			$this->_helper->redirector ( 'domain', 'cart', 'default', array ('mex' => 'The domain is a mandatory field. Choose a domain name.', 'status' => 'error' ) );
		}
		
		// Get the product (tld) selected by the customer
		$tldInfo = DomainsTlds::getAllInfo ( $params ['tlds'] );
		
		// Check if the parameter exists in our database
		if (isset ( $tldInfo ['tld_id'] )) {
			
			// If the customer already owns the domain and he wants to transfer it...
			if (! empty ( $params ['mode'] ) && $params ['mode'] == "link") {
				
				if (! empty ( $tldInfo )) {
					
					// Add the domain in the session variable
					$NS->cart->domain = $params ['domain'] . "." . $tldInfo ['DomainsTldsData'] [0] ['name'];
					
					// Adding the name of the product in the cart sidebar
					$completeproduct ['tld_id'] = $tldInfo ['tld_id'];
					$completeproduct ['domain_selected']   = strtolower ( $NS->cart->domain );
					$completeproduct ['domain_action']     = "transferDomain";
					$completeproduct ['quantity']          = 1;
					$completeproduct ['billingid']         = 3;
					$completeproduct ['name']              = $tldInfo ['DomainsTldsData'] [0] ['name'];
					$completeproduct ['description']       = $tldInfo ['DomainsTldsData'] [0] ['description'];
					$completeproduct ['shortdescription']  = $tldInfo ['DomainsTldsData'] [0] ['description'];
                    
                    
                    $completeproduct ['price_1']    = $tldInfo ['transfer_price'];
                    $completeproduct ['tax_id']     = $tldInfo ['tax_id'];
                    $completeproduct ['hosting']    = false;

                    //Check if in the cart there is a hosting without site
                    $hosting    = $this->findHostingWithoutSite();
                    //If find it, check if domains is incluse in hosting price.
                    if( $hosting != false ) {
                        $trancheid  = $hosting['value']['trancheid'];
                        $includes   = ProductsTranchesIncludes::getIncludeForTrancheId( $trancheid );
                        if( ! empty($includes) && array_key_exists('domains', $includes) ) {
                            if( in_array($tldInfo['name'], $includes['domains']) ) {
                                $completeproduct ['price_1']    = 0;
                                $completeproduct ['tax_id']     = 0;
                                
                                $key    = $hosting['key'];
                                $NS->cart->products[$key]['site']   = $params ['domain'] . "." . $tldInfo ['DomainsTldsData'] [0] ['name'];                                
                            }
                        }
                    }
                    
                    
					$completeproduct ['type'] = "domain";
					$completeproduct ['setupfee'] = 0;
					$completeproduct ['isavailable'] = 0;
					
					// Add the product in the cart list
					$NS->cart->products [] = $completeproduct;
					
					// redirect the customer to the contact form
					$this->_helper->redirector ( 'contacts' );
				}
			
			} else { // If the domain is still free and the customer needs to register it then ...
				$strDomain = $params ['domain'] . "." . $tldInfo ['DomainsTldsData'] [0] ['name'];
				
				// Check if the domain is still free
				$result = Domains::isAvailable ( $params ['domain'] . "." . $tldInfo ['DomainsTldsData'] [0] ['name'] );
				
				if ($result) { // If it is free
					
					// Add the domain in the session variable
					$NS->cart->domain = $params ['domain'] . "." . $tldInfo ['DomainsTldsData'] [0] ['name'];
					
					// Adding the name of the product in the cart sidebar
					$completeproduct ['tld_id'] = $tldInfo ['tld_id'];
					$completeproduct ['domain_selected'] = strtolower ( $NS->cart->domain );
					$completeproduct ['domain_action'] = "registerDomain";
					$completeproduct ['quantity'] = 1;
					$completeproduct ['billingid'] = 3;
					$completeproduct ['name'] = $tldInfo ['DomainsTldsData'] [0] ['name'];
					$completeproduct ['description'] = $tldInfo ['DomainsTldsData'] [0] ['description'];
					$completeproduct ['shortdescription'] = $tldInfo ['DomainsTldsData'] [0] ['description'];
					$completeproduct ['price_1'] = $tldInfo ['registration_price'];
					$completeproduct ['tax_id'] = $tldInfo ['tax_id'];
                    
                    //Check if in ther cart there is a hosting without site
                    $hosting    = $this->findHostingWithoutSite();
                    //If find it, check if domains is incluse in hosting price.
                    if( $hosting != false ) {
                        $trancheid  = $hosting['trancheid'];
                        $includes   = ProductsTranchesIncludes::getIncludeForTrancheId( $trancheid );
                        if( ! empty($includes) && array_key_exists('domains', $includes) ) {
                            if( in_array($tldInfo['name'], $includes['domains']) ) {
                                $completeproduct ['price_1']    = 0;
                                $completeproduct ['tax_id']     = 0;
                            }
                            
                        }
                    }
                    //////
                                        
					$completeproduct ['type'] = "domain";
					$completeproduct ['setupfee'] = 0;
					$completeproduct ['isavailable'] = 1;
					
					// Add the product in the cart list
					$NS->cart->products [] = $completeproduct;
					
					// Redirect the user to the 
					$this->_helper->redirector ( 'contacts', 'cart', 'default', array ('mex' => 'The domain is available for registration', 'status' => 'success' ) );
				
				} else {
					// If not redirect the customer to choose another name					 
					$this->_helper->redirector ( 'domain', 'cart', 'default', array ('mex' => 'The domain is not available for registration. Choose another domain name.', 'status' => 'error' ) );
				}
			}
		}
		$this->_helper->redirector ( 'domain', 'cart', 'default', array ('mex' => 'The domain is available for registration', 'status' => 'success' ) );
	}
	
	/*
	 * Show the domain checker form
	 */
	public function domainAction() {
		$items = array ();
		$NS = new Zend_Session_Namespace ( 'Default' );
		
		// Get the sent parameters
		$request = $this->getRequest ()->getParams ();
		
		// Create the sidebar if the cart has products
		if (! empty ( $NS->cart->products )) {
			if (! empty ( $NS->cart->domain )) {
				$this->_helper->redirector ( 'contacts', 'cart', 'default', array ('mex' => 'You can complete the order checkout or delete one or more products and inserting a new one. Just click on the delete link in the cart summary.', 'status' => 'attention' ) );
			}
			$this->view->placeholder ( "right" )->append ( $this->view->partial ( 'partials/cartsidebar.phtml', array ('items' => $NS->cart->products ) ) );
		}
		
		$form = new Default_Form_DomaincheckerForm ( array ('action' => "/cart/checkdomain", 'method' => 'post' ) );
		
		$this->view->form = $form;
		
		$this->view->mex = $this->getRequest ()->getParam ( 'mex' );
		$this->view->mexstatus = $this->getRequest ()->getParam ( 'status' );
		$this->_helper->viewRenderer ( 'domain' );
	}
	
	/*
     * Get the customer information
     */
	public function contactsAction() {
		
		$NS = new Zend_Session_Namespace ( 'Default' );
		
		#Zend_Debug::dump($NS->cart->products);
		
		// Check if there is a domain service within the cart.
		// If a domain is present we have to create a nic-handle in order to register the 
		// customer in the remote registrant database
		$hasdomain = $this->hasDomain ();
		
		// Check if the user has been logged in
		if (!empty($NS->customer)) {
			$customer = $NS->customer;
			
			// Check if the customer is a reseller
			if (! empty ( $customer ['isreseller'] ) && $customer ['isreseller']) {
				$NS->cart->reseller = $NS->customer;
				$this->_helper->redirector ( 'reseller', 'cart', 'default' );
			} else {
				unset ( $NS->cart->reseller );
			}
			
			if (! empty ( $customer )) {
				$NS->cart->contacts = $customer;
				$this->view->contact = $customer;
				$this->_helper->viewRenderer ( 'contactlogged' );
			}
		
		} else {
			// Clean the session vars
			unset ( $NS->cart->reseller );
			unset ( $NS->cart->contacts );
		}
		
		// Create the sidebar if the cart has products
		if (! isset ( $NS->cart->products )) {
			$this->_helper->redirector ( 'index', 'index', 'default' );
		} else {
			$items = $NS->cart->products;
			$this->view->placeholder ( "right" )->append ( $this->view->partial ( 'partials/cartsidebar.phtml', array ('items' => $items ) ) );
		}
		
		$request = $this->getRequest ();
		
		$this->view->mex = $request->getParam ( 'mex' );
		$this->view->mexstatus = $request->getParam ( 'status' );
		$this->view->hasdomain = $hasdomain;
		
		// Create the form
		$form = new Default_Form_CustomerForm ( array ('action' => "/cart/contacts", 'method' => 'post' ) );
		
		$form->getElement ( 'submit' )->setLabel ( 'Continue Order' );
		
		$form->populate ( array('country_id' => 82) ); // Set Italy as default 
		
		if (!empty ( $NS->cart->contacts )) {
			$form->populate ( $NS->cart->contacts );
		}
		
		// If the product/service include a domain we need more information
		if ($hasdomain === false){
			$form->getElement ( 'sex' )->setRequired ( false );
			$form->getElement ( 'sex' )->setRegisterInArrayValidator ( false );
			$form->getElement ( 'sex' )->setRequired ( false );
			$form->getElement ( 'birthdate' )->setRequired ( false );
			$form->getElement ( 'birthplace' )->setRequired ( false );
			$form->getElement ( 'taxpayernumber' )->setRequired ( false );
			$form->getElement ( 'birthdistrict' )->setRequired ( false );
			$form->getElement ( 'birthcountry' )->setRequired ( false );
			$form->getElement ( 'birthnationality' )->setRequired ( false );
		}
		
		$this->view->form = $form;
		
		// Check if we have a POST request
		if ($request->isPost ()) {
			$params = $request->getPost ();
			
			if ($form->isValid ( $params )) {
				
				// Create a customer or get his ID
				$result = $this->CreateCustomer ( $params, $hasdomain );
				if (is_numeric ( $result )) {
					// Do the login
					$NS->cart->contacts = $this->doLogin ( $result );
					$this->_helper->redirector ( 'payment', 'cart', 'default', array ('mex' => 'Well done! Now you have to choose your preferite payment method.', 'status' => 'success' ) );
				} else {
					$this->view->mex = $result;
					$this->view->mexstatus = "error";
				}
			}
		}
	
	}
	
	/*
     * Show the logged reseller information
     */
	public function resellerAction() {
		$NS = new Zend_Session_Namespace ( 'Default' );
		
		if (! isset ( $NS->cart->products ) || count ( $NS->cart->products ) == 0) {
			$this->_helper->redirector ( 'index', 'index', 'default' );
		}
		
		$request = $this->getRequest ();
		
		if (!empty($NS->customer)) {
			$NS->cart->reseller = $NS->customer;
		} else {
			unset ( $NS->cart->reseller );
			$this->_helper->redirector ( 'contacts', 'cart', 'default' );
		}
		
		// Create the sidebar if the cart has products
		if (! isset ( $NS->cart->products )) {
			$this->_helper->redirector ( 'index', 'index', 'default' );
		} else {
			$items = $NS->cart->products;
			$this->view->placeholder ( "right" )->append ( $this->view->partial ( 'partials/cartsidebar.phtml', array ('items' => $items ) ) );
		}
		
		// Create the item for the customers select object 
		$criteria = array (array ('where' => "parent_id = ?", 'params' => $NS->cart->reseller ['customer_id'] ) );
		
		// Create the form
		$form = new Default_Form_ResellerForm ( array ('action' => "/cart/reseller", 'method' => 'post' ) );
		
		// Get the reseller information 
		$reseller = array ($NS->cart->reseller ['customer_id'] => $NS->cart->reseller ['firstname'] . " " . $NS->cart->reseller ['lastname'] . " - " . $NS->cart->reseller ['company'] );
		
		// Get the customers connected to the reseller
		$customers = Customers::getList ( false, $criteria );
		
		$form->getElement ( 'submit' )->setLabel ( 'Continue Order' );
		
		// Assign the customers to the select object
		$form->getElement ( 'customers' )->setMultiOptions ( $reseller + $customers );
		$this->view->form = $form;
		
		// Check if we have a POST request
		if ($request->isPost ()) {
			$params = $request->getPost ();
			
			if ($form->isValid ( $params )) {
				$NS->cart->contacts = Customers::getAllInfo ( $params ['customers'], "c.customer_id, a.address_id, cts.type_id, l.legalform_id, ct.country_id, cn.contact_id, s.status_id, c.*, a.*, l.*, cn.*, cts.*, s.*" );
				$this->_helper->redirector ( 'payment', 'cart', 'default' );
			}
		}
		
		$this->view->reseller = $NS->cart->reseller;
		$this->_helper->viewRenderer ( 'reseller' );
	}
	
	/*
     * Show the contact form
     */
	public function simplecontactsAction() {
		$NS = new Zend_Session_Namespace ( 'Default' );
		unset ( $NS->cart->reseller );
		
		if (! isset ( $NS->cart->products ) || count ( $NS->cart->products ) == 0) {
			$this->_helper->redirector ( 'index', 'index', 'default' );
		} else {
			$items = $NS->cart->products;
			$this->view->placeholder ( "right" )->append ( $this->view->partial ( 'partials/cartsidebar.phtml', array ('items' => $items ) ) );
		}
		
		$request = $this->getRequest ();
		
		$form = new Default_Form_CartsimpleprofileForm ( array ('action' => "/cart/simplecontacts", 'method' => 'post' ) );
		
		// Fill the form if the user has already write his/her information
		if (isset ( $NS->cart->contacts ) && is_array ( $NS->cart->contacts )) {
			$form->populate ( $NS->cart->contacts );
		}
		
		$form->getElement ( 'save' )->setLabel ( 'Continue Order' );
		$this->view->form = $form;
		
		// Check if we have a POST request
		if ($request->isPost ()) {
			$params = $request->getPost ();
			
			if ($form->isValid ( $params )) {
				
				$params = $request->getPost ();
				$NS->cart->contacts = $params;
				
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
		
		$this->view->mex = $this->getRequest ()->getParam ( 'mex' );
		$this->view->mexstatus = $this->getRequest ()->getParam ( 'status' );
	}

    private function checkIfDomainIncluse( $nameDomain ){
        $NS = new Zend_Session_Namespace ( 'Default' );
        foreach ( $NS->cart->products as $product ) {
            if( $product['type'] == 'hosting' && isset($product['site']) && $product['site'] == $nameDomain ) {
                return $product;
            }
        }
        
        return false;
    }
	
	/*
     * Request the payment of the order
     */
	public function paymentAction() {
		$NS = new Zend_Session_Namespace ( 'Default' );
		$request = $this->getRequest ();
		$cart = $NS->cart;
		$contact = $cart->contacts;
		
		$isVATFree = Customers::isVATFree($cart->contacts['customer_id']);
		
		$this->getHelper ( 'layout' )->setLayout ( '1column' );
		$form = new Default_Form_CartsummaryForm ( array ('action' => '/cart/payment', 'method' => 'post' ) );
		
		if (! isset ( $NS->cart->products ) || count ( $NS->cart->products ) == 0) {
			$this->_helper->redirector ( 'index', 'index', 'default' );
		} else {
			//$items = $NS->cart->products;
			foreach ( $NS->cart->products as &$products ) {
				$products['tax_id'] = ($isVATFree) ? null : $products['tax_id'];
				$items[] = $products;
			}
			$this->view->placeholder ( "shoppingcart" )->append ( $this->view->partial ( 'partials/shoppingcart.phtml', array ('items' => $items ) ) );
		}
		
		if ($this->checkIfHostingProductIsPresentWithinCart ()) {
			$this->view->containhosting = 1;
		} else {
			$this->view->containhosting = 0;
		}
		
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
				 
				$theOrder = Orders::create ( $NS->cart->contacts ['customer_id'], Statuses::id('tobepaid', 'orders'), $params ['note'] );
				
				foreach ( $NS->cart->products as $product ) {
					
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
				$NS->cart->orderid = $orderID;
				
				// Get the totals
				$NS->cart->totals = $this->Totals ();
				$NS->cart->payment->notes = ! empty ( $params ['note'] ) ? $params ['note'] : "";
				
				// Calculate the Grand Total
				$amount = $NS->cart->totals ['total'];
				
				if (is_numeric ( $params ['payment'] )) {
					$NS->cart->payment->id = $params ['payment'];
					$this->_helper->redirector ( 'gateway', 'cart', 'default' );
				} else {
					$this->_helper->redirector ( 'index', 'index', 'default' );
					unset ( $NS->cart );
				}
			
			}
		} else {
			
			$this->view->isVATFree	= $isVATFree;
			$this->view->cart = $NS->cart;
			$this->view->totals = $this->Totals ();
			$this->view->form = $form;
			
			$this->view->mex = $this->getRequest ()->getParam ( 'mex' );
			$this->view->mexstatus = $this->getRequest ()->getParam ( 'status' );
		}
	}
	
	/**
	 * Create the payment gateway form
	 */
	public function gatewayAction() {
		$NS = new Zend_Session_Namespace ( 'Default' );
		
		$orderID = $NS->cart->orderid;
		
		// Get the payment form object
		$banks = Banks::find ( $NS->cart->payment->id, "*", true );
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
				unset ( $NS->cart );
			
			} else {
				$this->_helper->redirector ( 'payment', 'cart', 'default' );
			}
		}
	
	}
	
	/*
     * Delete a product or domain from the cart list 
     */
	public function deleteAction() {
		$NS = new Zend_Session_Namespace ( 'Default' );
		
		// Get the parameters
		$params = $request = $this->getRequest ()->getParams ();
		
		// Get all the cart products
		$products = $NS->cart->products;
		
		$index = 0;
		
		// If the product is a domain delete the temporary session domain information
		if (! empty ( $params ['tld'] )) {
			foreach ( $products as $key => $product ) {
			    #Delete domain in hosting if is incluse
                if (! empty ( $product ['domain_selected'] ) && $product ['domain_selected'] == $params ['tld']) {
					unset ( $products [$index] );  // Delete the product from the session cart 
					$NS->cart->products = array_values ( $products );
					unset ( $NS->cart->domain );
				} elseif( $products['type'] == 'hosting' && $products['site'] == $params ['tld']) {
                   unset( $NS->cart->products[$key]['site'] ); 
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
					$NS->cart->products = array_values ( $products );
					break;
				}
				$index ++;
			}
		} elseif (! empty ( $params ['product'] ) && $params ['product'] == "all") {
			unset ( $NS->cart );
		}
		
		if (! empty ( $NS->cart->products ) && count ( $NS->cart->products ) > 0) {
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
	 * Check if within the cart exist already the product selected
	 */
	private function checkIfProductIsPresentWithinCart($selproduct) {
		$NS = new Zend_Session_Namespace ( 'Default' );
		if (! empty ( $NS->cart->products ) && is_array ( $NS->cart->products )) {
			$products = $NS->cart->products;
			if (is_array ( $products ) && count ( $products ) > 0) {
				foreach ( $products as $product ) {
					if ($product ['product_id'] == $selproduct ['product_id']) {
						return true;
					}
				}
			}
		}
		return false;
	}
	
	/*
	 * Check if within the cart exist already a hosting product selected
	 */
	private function checkIfProductIsHostingAndPresentWithinCart($selproduct) {
		$NS = new Zend_Session_Namespace ( 'Default' );
		if (! empty ( $NS->cart->products ) && is_array ( $NS->cart->products )) {
			$products = $NS->cart->products;
			if (is_array ( $products ) && count ( $products ) > 0) {
				foreach ( $products as $product ) {
					if ($selproduct ['product_id'] == $product ['product_id']) {
						if ($product ['type'] == "hosting") {
							return true;
						}
					}
				}
			}
		}
		return false;
	}
	
	/*
	 * Check if within the cart exist already a hosting product selected
	 */
	private function checkIfHostingProductIsPresentWithinCart() {
		$NS = new Zend_Session_Namespace ( 'Default' );
		if (! empty ( $NS->cart->products ) && is_array ( $NS->cart->products )) {
			$products = $NS->cart->products;
			if (is_array ( $products ) && count ( $products ) > 0) {
				foreach ( $products as $product ) {
					if ($product ['type'] == "hosting") {
						return true;
					}
				}
			}
		}
		return false;
	}
	
	/*
	 * Get the tranche selected for the hosting present into the cart
	 */
	private function getTranchefromHosting() {
		$NS = new Zend_Session_Namespace ( 'Default' );
		if (! empty ( $NS->cart->products ) && is_array ( $NS->cart->products )) {
			$products = $NS->cart->products;
			if (is_array ( $products ) && count ( $products ) > 0) {
				foreach ( $products as $product ) {
					if ($product ['type'] == "hosting") {
						return $product ['trancheid'];
					}
				}
			}
		}
		return false;
	}
	
	/*
	 * Check if within the cart exist already a domain selected
	 */
	private function hasDomain() {
		$NS = new Zend_Session_Namespace ( 'Default' );
		if (! empty ( $NS->cart->products ) && is_array ( $NS->cart->products )) {
			$products = $NS->cart->products;
			if (is_array ( $products ) && count ( $products ) > 0) {
				foreach ( $products as $product ) {
					if ($product ['type'] == "domain") {
						return true;
					}
				}
			}
		}
		return false;
	}
	
	/*
	 * Changing of the product
	 */
	private function changeProduct($newproduct) {
		$index	= $this->getIndexProduct($newproduct);
		if( $index === false ) {
			$NS->cart->products [] = $newproduct;
		} else {
			$products	= $NS->cart->products;
			$product	= $products[$index];
			// Delete the old product
			unset( $products[$index] );
			
			// Reorder the indexes
			$NS->cart->products = array_values ( $products );
			
			// Adding the new hosting product in the cart
			$NS->cart->products [] = $newproduct;
		}
	}
	
	/**
	 * Get index product in the cart
	 ****/
	private function getIndexProduct ( $newproduct ) {
		$NS = new Zend_Session_Namespace ( 'Default' );
		if (! empty ( $NS->cart->products ) && is_array ( $NS->cart->products )) {
			$products = $NS->cart->products;
			$index = 0;
			
			if (is_array ( $products ) && count ( $products ) > 0) {
				
				// Read all the product added in the cart
				foreach ( $products as $product ) {
					// Match the product cycled with the hosting product previously inserted in the cart
					if ($product ['product_id'] == $newproduct ['product_id']) {
						return $index;
					}
					$index++;
				}
			}
		}
		
		return false;
	}
	
	/*
	 * Changing hosting product
	 */
	private function changeHostingPlan($newproduct) {
		$NS = new Zend_Session_Namespace ( 'Default' );
		if (! empty ( $NS->cart->products ) && is_array ( $NS->cart->products )) {
			
			$products = $NS->cart->products;
			$index = 0;
			
			if (is_array ( $products ) && count ( $products ) > 0) {
				
				// Read all the product added in the cart
				foreach ( $products as $product ) {
					// Match the product cycled with the hosting product previously inserted in the cart
					if ($product ['type'] == "hosting") {
						// Delete the old product
						unset ( $products [$index] );
						
						// Reorder the indexes
						$NS->cart->products = array_values ( $products );
						
						// Adding the new hosting product in the cart
						$NS->cart->products [] = $newproduct;
					}
					$index ++;
				}
			} else {
				// No products have been found. Adding the new hosting product in the cart
				$NS->cart->products [] = $newproduct;
			}
		}
	}
	
	/*
	 * Total
	 * Create the total of the order
	 */
	private function Totals() {
		$NS = new Zend_Session_Namespace ( 'Default' );
		
		$isVATFree = Customers::isVATFree($NS->cart->contacts['customer_id']);
		
		if (! empty ( $NS->cart->products ) && is_array ( $NS->cart->products )) {
			$products = $NS->cart->products;
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
	
	private function doLogin($customerid) {
		$NS = new Zend_Session_Namespace ( 'Default' );
		$auth = Zend_Auth::getInstance ();
		$auth->setStorage ( new Zend_Auth_Storage_Session ( 'default' ) );
		
		$result = new Zend_Auth_Result ( Zend_Auth_Result::SUCCESS, null );
		$customer = Customers::getAllInfo ( $customerid, "c.customer_id, a.address_id, cts.type_id, l.legalform_id, ct.country_id, cn.contact_id, s.status_id, c.*, a.*, l.*, cn.*, cts.*, s.*" );
		$NS->customer = $customer;
		
		// We're authenticated! 
		$auth->getStorage ()->write ( $customer );
		return $customer;
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
		$NS = new Zend_Session_Namespace ( 'Default' );
		
		if (is_numeric ( $NS->cart->contacts ['customer_id'] )) {
			Customers::del ( $NS->cart->contacts ['customer_id'] );
		}
		unset ( $NS->cart );
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