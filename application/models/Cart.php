<?php

/**
 * Cart Management
 * 
 * @package    ShineISP
 * @author     Shine Software <info@shineisp.com>
 */

class Cart {
	
	protected $heading = array ();
	protected $items = array ();
	protected $position = 0;
	protected $subtotal = 0;
	protected $grandtotal = 0;
	protected $taxes = 0;
	protected $setupfee = 0;
	protected $orderid = null;
	
	function __construct() {
		$this->heading = array ();
		$this->items = array ();
	}
	
	/**
	 * @return the $orderid
	 */
	public function getOrderid() {
		return $this->orderid;
	}

	/**
	 * @param NULL $orderid
	 */
	public function setOrderid($orderid) {
		$this->orderid = $orderid;
		return $this;
	}

	// Required by Countable:
	public function count() {
		return count ( $this->items );
	}
	
	// Get cart items
	public function getItems() {
		return $this->items;
	}
	
	/**
	 * @return the $setupfee
	 */
	public function getSetupfee() {
		return $this->setupfee;
	}

	/**
	 * @param number $setupfee
	 */
	public function setSetupfee($setupfee) {
		$this->setupfee = $setupfee;
		return $this;
	}

	/**
	 * @return the $subtotal
	 */
	public function getSubtotal() {
		return $this->subtotal;
	}

	/**
	 * @return the $grandtotal
	 */
	public function getGrandtotal() {
		return $this->grandtotal;
	}

	/**
	 * @return the $taxes
	 */
	public function getTaxes() {
		return $this->taxes;
	}

	/**
	 * @param number $subtotal
	 */
	public function setSubtotal($subtotal) {
		$this->subtotal = $subtotal;
		return $this;
	}

	/**
	 * @param number $grandtotal
	 */
	public function setGrandtotal($grandtotal) {
		$this->grandtotal = $grandtotal;
		return $this;
	}

	/**
	 * @param number $taxes
	 */
	public function setTaxes($taxes) {
		$this->taxes = $taxes;
		return $this;
	}
	
	/**
	 * Returns a Boolean indicating if the cart is empty:
	 */
	public function isEmpty() {
		return (empty ( $this->items ));
	}
	
	/**
	 * Adds a new item to the cart
	 * 
	 * @param CartItem $item
	 * @throws Exception
	 */
	public function addItem(CartItem $item) {
		
		// Need the item id:
		$uid = $item->getUid ();
		
		// Throw an exception if there's no id:
		if (! $uid)
			throw new Exception ( 'The cart requires items with unique ID values.' );
			
		// Add or update:
		if (isset ( $this->items [$uid] )) {
			$this->updateItem ( $item, $this->items [$uid]->getQty () + 1 );
		} else {
			$this->items [$uid] = $item;
		}
		
		// Update the totals
		$this->update();
		
	} 
	  
	/**
	 * Get the a item from the cart by Id
	 * 
	 * @param integer $id
	 * @return CartItem
	 */
	public function getItem($id) {
		
		foreach ( $this->items as $item ) {
			if ($id == $item->getId()) {
				return $item;
			}
		}
		
		return false;
	} 
	  
	/**
	 * Get the a item from the cart by Uid
	 * 
	 * @param integer $Uid
	 * @return CartItem
	 */
	public function getItemByUid($uid) {
		
		foreach ( $this->items as $item ) {
			if ($uid == $item->getUid()) {
				return $item;
			}
		}
		
		return false;
	} 
	  
	/**
	 * Changes an item already in the cart
	 * 
	 * @param CartItem $item
	 * @param integer $qty
	 */
	public function updateItem(CartItem $item, $qty) {
		
		// Delete or update accordingly:
		if ($qty === 0) {
			$this->deleteItem ( $item );
		} elseif (($qty > 0) && ($qty != $item->getQty ())) {
			$item->setQty ( $qty );
		}
	} 
	  
	/**
	 * Add a domain in the cart
	 * 
	 * @param string $domain
	 * @param integer $tld_id
	 * @param string $action [registerDomain, transferDomain]
	 * @return CartItem or Null
	 */
	public function addDomain($domain, $tld_id, $action = "registerDomain", $authcode=null) {
		
		if ( ! empty ( $domain )) {
			
			// Get the price information for the domain product
			$priceInfo = DomainsTlds::getPrice($tld_id, $action);

			// Add the domain in the cart
			$domainitem = new CartItem();
			$domainitem->setName($domain)
					   	->setId($tld_id)
			 		   	->setType('domain')
						->setTerm(3) // annual
						->setTaxId($priceInfo['tax_id'])
						->setCost($priceInfo['cost'])
						->setUnitprice($priceInfo['price'])
						->setSetupfee($priceInfo['setupfee'])
						->addOption('domain', array (
												'name' => $domain,
												'tld' => $tld_id,
												'action' => $action,
												'authcode' => $authcode
										));
			
			$this->addItem($domainitem);
			
			return $domainitem;
			
		}
		
		return NULL;
	}
	  
	/**
	 * Attach a domain to a hosting product and add itself within the cart
	 * 
	 * @param CartItem $item
	 * @param string $domain
	 * @param integer $tld_id
	 * @param string $action [registerDomain, transferDomain]
	 */
	public function attachDomain(CartItem $item, $domain, $tld_id, $action = "registerDomain", $authcode=null) {
		
		if (!empty($item) && ! empty ( $domain )) {
			
			// Set the domain attaching it within the hosting plan selected
			$item->setDomain ($domain);
			$item->addOption('domain', array (
							'name' => $domain,
							'tld' => $tld_id,
							'action' => $action,
							'authcode' => $authcode
					));
			
			self::addDomain($domain, $tld_id, $action);
			
		}
	}
	
	/**
	 * Add the customer in the cart
	 * 
	 * @param integer $customerId
	 */
	public function setCustomer($customerId) {
		if (! empty ( $customerId )) {
			$this->heading ['customer'] ['id'] = $customerId;
		}
	} 
	  
	/**
	 * Add the reseller in the cart
	 * 
	 * @param integer $resellerId
	 */
	public function setReseller($resellerId) {
		if (! empty ( $resellerId )) {
			$this->heading ['reseller'] ['id'] = $resellerId;
		}
	} 
	  
	/**
	 * Remove the reseller from the cart.
	 */
	public function removeCustomer() {
		unset ( $this->heading ['customer'] );
	} 
	  
	/**
	 * Remove the reseller from the cart.
	 */
	public function removeReseller() {
		unset ( $this->heading ['reseller'] );
	} 
	  
	/**
	 * Delete an item from the cart
	 * 
	 * @param CartItem $item
	 * @return boolean
	 */
	public function deleteItem(CartItem $item) {
		
		$uid = $item->getUid ();
		
		if($item->getUid()){
			if(!empty($this->items[$item->getUid()])){
				unset($this->items[$item->getUid()]);
			}
		}
		
		return true;
	}
	  
	/**
	 * Get cart heading
	 * 
	 * @return multitype:
	 */
	public function getHeading() {
		return $this->heading;
	}
	
	/**
	 * Get customer information
	 * 
	 * @return multitype:|NULL
	 */
	public function getCustomer() {
		if (! empty ( $this->heading ['customer'] )) {
			return $this->heading ['customer'];
		} else {
			return null;
		}
	}
	
	/**
	 * Get customer id
	 * 
	 * @return NULL
	 */
	public function getCustomerId() {
		if (! empty ( $this->heading ['customer'] ['id'] )) {
			return $this->heading ['customer'] ['id'];
		} else {
			return null;
		}
	}
	
	/**
	 * Get reseller information
	 * 
	 * @return multitype:
	 */
	public function getReseller() {
		return $this->heading ['reseller'];
	}
	
	/**
	 * Get all the product data information from the database
	 *
	 * @return array
	 */
	public function update() {
		if (! empty ( $this->items ) && is_array ( $this->items )) {
			
			// Reset the subtotal
			$this->setGrandtotal(0);
			$this->setTaxes(0);
			$this->setSubtotal(0);
			$this->setSetupfee(0);
			
			if($this->getCustomerId ()){
				$isVATFree = Customers::isVATFree ( $this->getCustomerId () );
			}else{
				$isVATFree = false;
			}
			
			foreach ( $this->items as $item ) {
				$this->calcSubtotal ( $item, $isVATFree );
				
				$subtotals = $this->getSubtotal();
				$grandtotal = $this->getGrandtotal();
				$subtaxes = $this->getTaxes();
				$setupfee = $this->getSetupfee();
				
				$itemsubtot = $item->getSubtotals();

				$this->setSubtotal($subtotals + $itemsubtot['subtotal']);
				$this->setTaxes($subtaxes + $itemsubtot['taxes']);
				$this->setSetupfee($setupfee + $itemsubtot['setupfee']);
				$this->setGrandtotal($grandtotal + $itemsubtot['price'] + $itemsubtot['taxes']);
			}
			
		}
		return $this->items;
	}
	
	/**
	 * Calculate the subtotal for each product in the cart
	 *
	 * @param integer $id        	
	 * @param integer $qty        	
	 * @param boolean $isvatfree        	
	 * @return ArrayObject
	 */
	private function calcSubtotal(CartItem $item, $isvatfree=false) {
		
		foreach ( $this->items as $item ) {
			
			$isrecurring = false;
			$months = 0;
			$percentage = 0;
			$tax = 0;
			
			if("domain" == $item->getType()){
				$isrecurring = true;
				$months = 12; // 12 months minimum for all domains
				$unitprice = $item->getUnitPrice();
				$setupfee = 0;
				
				// Calculate the price per Quantity
				$subtotal = $unitprice * $item->getQty ();
				
				// check the taxes
				if(Taxes::get_percentage($item->getTaxId())){
					$percentage = Taxes::get_percentage($item->getTaxId());
					$tax = ($subtotal * $percentage) / 100;
					$price = ($subtotal * (100 + $percentage)) / 100;
				}
				
			}else{
				// Get all the product information
				$product = Products::getAllInfo ( $item->getId () );
					
				// Check the type of the product and get the price information
				if ($product ['ProductsAttributesGroups'] ['isrecurring']) {
				
					$isrecurring = true;
					
					// Get the billyng cycle / term / recurring period price
					$priceInfo = $product['ProductsTranches'];
					$keys = array_keys($priceInfo);
					$priceInfo = $priceInfo[$keys[0]];
					
					// Price multiplier
					$months = $priceInfo ['BillingCycle'] ['months'];
				
					$unitprice = $priceInfo ['price'];
					$setupfee = $priceInfo ['setupfee'];
				
					// Calculate the price per the months per Quantity
					$subtotal = ($unitprice * $months) * $item->getQty ();
					
				} else {
					$unitprice = $product ['price_1'];
					$setupfee = $product ['setupfee'];
				
					// Calculate the price per Quantity
					$subtotal= $unitprice * $item->getQty ();
				}

				// check the taxes for each product
				if (! empty ( $product ['tax_id'] ) && ! $isvatfree) {
					if (! empty ( $product ['Taxes'] ['percentage'] ) && is_numeric ( $product ['Taxes'] ['percentage'] )) {
						$percentage = $product ['Taxes'] ['percentage'];
						$tax = ($subtotal * $percentage) / 100;
						$price = ($subtotal * (100 + $percentage)) / 100;
					}
				}
			}
			
			// ... and add the setup fees
			$price = $subtotal + $setupfee;
			
			$item->setSubtotals ( array (
					'isrecurring' => $isrecurring,
					'months' => $months,
					'subtotal' => $subtotal,
					'setupfee' => $setupfee,
					'price' => $price,
					'taxes' => $tax,
					'percentage' => $percentage 
			) );
		}
		
		return $this->items;
	}
	
	/**
	 * Clear all the items from the cart
	 *
	 * @return boolean
	 */
	public function clearAll() {
		if (! empty ( $this->items ) && is_array ( $this->items )) {
			foreach ( $this->items as $item ) {
				$this->deleteItem($item); // delete all the items
			}
			
			// delete the likely orderid in the cart session
			$this->setOrderid(null); 
		}
		return false;
	}
	
	/**
	 * Check if within the cart exist already a hosting product selected
	 *
	 * @return boolean
	 */
	public function checkIfHostingProductIsPresentWithinCart() {
		if (! empty ( $this->items ) && is_array ( $this->items )) {
			foreach ( $this->items as $item ) {
				$type = Products::getProductType ( $item->getId () );
				if ($type == "hosting") {
					return true;
				}
			}
		}
		return false;
	}
	
	/**
	 * Get the first hosting item
	 *
	 * @return CartItem
	 */
	public function getHostingItem() {
		if (! empty ( $this->items ) && is_array ( $this->items )) {
			foreach ( $this->items as $item ) {
				$type = Products::getProductType ( $item->getId () );
				if ($type == "hosting") {
					return $item;
				}
			}
		}
		return false;
	}
	
	/**
	 * Check if within the cart exist already a domain selected
	 *
	 * @return boolean
	 */
	public function hasDomain() {
		if (! empty ( $this->items ) && is_array ( $this->items )) {
			foreach ( $this->items as $item ) {
				if ($item->getDomain ()) {
					return true;
				}
			}
		}
		return false;
	}
	
	/**
	 * Create a new Order
	 *
	 * @return boolean
	 */
	public function createOrder() {
	
		if(!$this->getOrderid()){
			
			$theOrder = Orders::create ( $this->getCustomerId(), Statuses::id('tobepaid', 'orders'), null );
	
			// For each item in the cart
			foreach ($this->getItems() as $item){
				$item = Orders::addOrderItem ($item);
			}
			
			$this->setOrderid($theOrder['order_id']);
			
			// Send the email to confirm the order
			Orders::sendOrder ( $theOrder['order_id'] );
			
			return Orders::getOrder();
			
		}else{
			
			return Orders::find($this->getOrderid());
		}
		
	}
	
	
}