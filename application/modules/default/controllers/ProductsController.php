<?php

class ProductsController extends Zend_Controller_Action {
	protected $translator;
	
	/**
	 * preDispatch
	 * Starting of the module
	 * (non-PHPdoc)
	 * @see library/Zend/Controller/Zend_Controller_Action#preDispatch()
	 */
	
	public function preDispatch() {
		$this->translator = Zend_Registry::getInstance ()->Zend_Translate;
	}
	
	/**
	 * indexAction
	 * Redirect the user to the list action
	 * @return unknown_type
	 */
	public function indexAction() {
		$this->_helper->redirector ( 'list', 'categories', 'default' );
	}
	
	public function getAction() {
		$ns = new Zend_Session_Namespace ( 'Default' );
		$product = array ();
		$uri = $this->getRequest ()->getParam ( 'q' );
		
		if (! empty ( $uri )) {
			
			$fields = "p.*,pd.productdata_id as productdata_id, 
                           pd.name as name, 
                           pd.shortdescription as shortdescription,
                           pd.description as description,
                           pd.metakeywords as metakeywords,
                           pd.metadescription as metadescription,
                           pai.*";
			
			$data = Products::getProductbyUriID ( $uri, $fields, $ns->langid );
			
			if (!empty($data) && $data['enabled']) {
				
				if(!empty($data['blocks'])){
					$this->getHelper ( 'layout' )->setLayout ( '2columns-right' );
				}
				
				$this->view->group = Products::GetAttributeGroupByProductID($data ['product_id']);
				
				$ns->cart->lastproduct = $uri;
				
				$refund	= false;
				if( is_array($ns->upgrade) ) {
					//Check if the product is OK for upgrade and if OK take refund
					foreach( $ns->upgrade as $orderid => $upgradeProduct ) {
						if( in_array( $data ['product_id'], $upgradeProduct) ) {
							$refundInfo		= OrdersItems::getRefundInfo($orderid);
							$refund			= $refundInfo['refund'];
							
							break;
						}
					}
				}
				
				$form = $this->CreateProductForm ( );
				$items = ProductsTranches::getList ( $data ['product_id'],$refund );
				
				// Check the default quantity value
				$qta = ProductsTranches::getDefaultItem ( $data ['product_id'] );
				if (! empty ( $qta )) {
					$data ['quantity'] = $qta;
				} else {
					$data ['quantity'] = 1;
				}
				
				if (count ( $items ) > 0) {
					$form->addElement ( 'select', 'quantity', array ('label' => $this->translator->translate ( 'Billing Cycle' ), 'required' => true, 'multiOptions' => $items, 'decorators' => array ('Composite' ), 'class' => 'text-input large-input select-billing-cycle' ) );
					$form->addElement ( 'hidden', 'isrecurring', array ('value' => '1' ) );
				} else {
					$form->addElement ( 'text', 'quantity', array ('label' => $this->translator->translate ( 'Quantity' ), 'required' => true, 'value' => '1', 'decorators' => array ('Composite' ), 'class' => 'text-input small-input' ) );
					$form->addElement ( 'hidden', 'isrecurring', array ('value' => '0' ) );
				}
				// Adding the product attributes
				$attributes = ProductsAttributesIndexes::getAttributebyProductID($data ['product_id'], $ns->langid);
				if (count ( $attributes ) > 0) {
					$this->view->placeholder ( "features" )->append ( $this->view->partial ( 'partials/attributes.phtml', array ('attributes' => $attributes ) ) );
				}
				
				// Adding the related products
				$related = ProductsRelated::get_products($data ['product_id'], $ns->langid);
				if(count($related) > 0){
					$this->view->placeholder ( "right" )->append ( $this->view->partial ( 'products/related.phtml', array ('products' => $related ) ) );
					$this->getHelper ( 'layout' )->setLayout ( '2columns-right' );
				}				
				
				// Attaching the WIKI Pages
				$wikipages = Wikilinks::getWikiPages($data ['product_id'], "products", $ns->langid);
				if(count($wikipages) > 0){
					$this->view->placeholder ( "right" )->append ( $this->view->partial ( 'products/wikipages.phtml', array ('wikipages' => $wikipages) ) );
					$this->getHelper ( 'layout' )->setLayout ( '2columns-right' );
				}				
				
				$this->view->reviewsdata = Reviews::getbyProductId ( $data ['product_id'] );
				
				// Set the Metatag information
				$this->view->headTitle()->prepend ( $data ['name'] );
				
				if (! empty ( $data ['metakeywords'] )) {
					$this->view->headMeta ()->setName ( 'keywords', $data ['metakeywords'] );
				}
				
				if (! empty ( $data ['metadescription'] )) {
					$this->view->headMeta ()->setName ( 'description', $data ['metadescription'] );
				}
				
				// Send the variables to the view
				$this->view->headertitle = $data ['name'];
				$this->view->product = $data;
				$this->view->prices = Products::getPrices($data ['product_id'],$refund);
				$this->view->form = $form;
				
				$form->populate ( $data );
			} else {
				// Check if there is an url rewrite redirection 
				$newuri = UrlRewrite::getTarget($uri);
				if($newuri){
					header( "HTTP/1.1 301 Moved Permanently" ); 
					header( "Location: $newuri" );
					die; 
				}else{
					return $this->_helper->redirector ( 'index', 'notfound', 'default', array ('uri' => $uri ) );	
				}
			}
		
		} else {
			
			return $this->_helper->redirector ( 'index', 'index', 'default', array ('mex' => 'The request is not correct.', 'status' => 'error' ) );
		}
		$this->_helper->viewRenderer ( 'details' );
	}
	
	/**
	 * addreviewAction
	 * Create the form for the submittion of the review
	 */
	public function addreviewAction() {
		$ns = new Zend_Session_Namespace ( 'Default' );
		$this->getHelper ( 'layout' )->setLayout ( '1column' );
		$request = $this->getRequest ();
		$id = $request->getParam ( 'id' );
		
		if (is_numeric ( $id )) {
			$product = Products::getAllInfo ( $id, $ns->langid );
			if (is_array ( $product )) {
				$this->view->product = $product;
				
				$form = $this->ReviewForm ( $id );
				
				// Check if we have a POST request
				if ($request->isPost ()) {
					if ($form->isValid ( $request->getPost () )) {
						if (Reviews::saveData ( $form->getValues (), true )) {
							return $this->_helper->redirector ( 'get', 'products', 'default', array ('q' => $product ['uri'] ) );
						}
					}
				}
				
				$form->populate ( array ('product_id' => $id ) );
				$this->view->reviewform = $form;
			
			}
		} else {
			return $this->_helper->redirector ( 'index', 'index', 'default' );
		}
		
		$this->_helper->viewRenderer ( 'reviews' );
	}
	
	/*
	 * CreateProductForm
	 */
	private function CreateProductForm() {
		return new Default_Form_ProductForm ( array ('action' => "/cart/add", 'method' => 'post' ) );
	}
	
	/*
	 * ReviewForm
	 */
	private function ReviewForm($product_id) {
		return new Default_Form_ReviewsForm ( array ('action' => "/products/addreview/id/$product_id", 'method' => 'post' ) );
	}
	
	/*
	 * Get the price of the product
	 */
	public function getpriceAction() {
		$currency = Zend_Registry::get ( 'Zend_Currency' );
    	$translator = Zend_Registry::get ( 'Zend_Translate' );
		
		$id 		= $this->getRequest ()->getParam ( 'id' );
		$refund 	= $this->getRequest ()->getParam ( 'refund' );
		$data = array ();
		
		if (is_numeric ( $id )) {
			$tranche = ProductsTranches::getTranchebyId ( $id );
			
			// JAY 20130409 - Add refund if exist
			$NS = new Zend_Session_Namespace ( 'Default' );
			if( is_array($NS->upgrade) ) {
				//Check if the product is OK for upgrade and if OK take refund
				foreach( $NS->upgrade as $orderid => $upgradeProduct ) {
					if( $orderid != 0 ) {
						if( in_array( $id, $upgradeProduct) ) {
							$refundInfo		= OrdersItems::getRefundInfo($orderid);
							$refund			= $refundInfo['refund'];
							$idBillingCircle		= $tranche['BillingCycle']['billing_cycle_id'];
							$monthBilling			= BillingCycle::getMonthsNumber($idBillingCircle);
							if( $monthBilling > 0 ) {
								$priceToPay				= $tranche['price'] * $monthBilling;
								$priceToPayWithRefund	= $priceToPay - $refund;
								if( $priceToPayWithRefund < 0 ) {
									$priceToPayWithRefund	= $priceToPay;
								}
								$tranche['price']	= round( $priceToPayWithRefund / $monthBilling,2 );
							} else {
								$priceToPayWithRefund	= $tranche['price'] - $refund;
								if( $priceToPayWithRefund > 0 ) {
									$tranche['price']	= $priceToPayWithRefund;
								}								
							}
							
							break;
						}
					}
				}
			}
			
			$includes    = 	ProductsTranchesIncludes::getIncludeForTrancheId( $id );
            $textIncludes    = array();
            if( array_key_exists('domains', $includes) ) {
                $textIncludes[]    = $this->translator->translate('Domains Included') . ": ".implode(", ",$includes['domains']);
            }
            
            $textInclude    = "";
            if( ! empty($textIncludes) ) {
                $textInclude = implode("<br/>",$textIncludes);    
            }

			// Prepare the data to send to the json
			$data['price'] = $tranche['price'];
			
			if(!empty($tranche['Products']['Taxes']['percentage']) && is_numeric($tranche['Products']['Taxes']['percentage'])){
				$data['pricetax'] = ($tranche['price'] * ($tranche['Products']['Taxes']['percentage'] + 100) / 100);
			}else{
				$data['pricetax'] = $tranche['price'];
			}
			
			$data['pricelbl']           = $currency->toCurrency($data['pricetax'], array('currency' => Settings::findbyParam('currency')));
			$data['months']             = $tranche['BillingCycle']['months'];
			$data['pricepermonths']     = $data['pricetax'] * $tranche['BillingCycle']['months'];
			$data['name']               = $this->translator->translate ($tranche['BillingCycle']['name']);
			$data['pricetax']           = $currency->toCurrency($data['pricetax'], array('currency' => Settings::findbyParam('currency')));
			$data['pricepermonths']     = $currency->toCurrency($data['pricepermonths'], array('currency' => Settings::findbyParam('currency')));
			$data['setupfee']           = $currency->toCurrency($tranche['setupfee'], array('currency' => Settings::findbyParam('currency')));
            $data['includes']           = $textInclude;
		}

		die ( json_encode ( $data ) );
	}
	
	/**
	 * checkdomainAction
	 * Check domains availability
	 * @return json string
	 */
	public function checkdomainAction() {
		try {
			$config = Registrars::findActiveRegistrars ();
			$domain = $this->getRequest ()->getParam ( 'fulldomain' );
			if (isset ( $config [0] )) {
				$registrant_class = $config [0] ['class'];
				$reg = new $registrant_class ();
				$reg->setConfig ( $config );
				$check = $reg->domainCheck ( $domain );
				echo json_encode ( $check );
			}
		} catch ( Exception $e ) {
			echo json_encode ( $e->getMessage () );
		}
		die ();
	}
}

