<?php

/**
 * Cart
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
class Cart implements Iterator, Countable {
	
	// Array stores the heading of the cart:
	protected $heading = array ();
	
	// Array stores the list of items in the cart:
	protected $items = array ();
	
	// For tracking iterations:
	protected $position = 0;
	protected $subtotal = 0;
	protected $grandtotal = 0;
	protected $taxes = 0;
	protected $setupfee = 0;
	
	// For storing the IDs, as a convenience:
	protected $ids = array ();
	
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

	// Constructor just sets the object up for usage:
	function __construct() {
		$this->heading = array ();
		$this->items = array ();
		$this->ids = array ();
	}
	
	// Returns a Boolean indicating if the cart is empty:
	public function isEmpty() {
		return (empty ( $this->items ));
	}
	
	// Adds a new item to the cart:
	public function addItem(CartItem $item) {
		
		// Need the item id:
		$id = $item->getId ();
		
		// Throw an exception if there's no id:
		if (! $id)
			throw new Exception ( 'The cart requires items with unique ID values.' );
			
			// Add or update:
		if (isset ( $this->items [$id] )) {
			$this->updateItem ( $item, $this->items [$id]->getQty () + 1 );
		} else {
			$this->items [$id] = $item;
			$this->ids [] = $id; // Store the id, too!
		}
	} // End of addItem() method.
	  
	/**
	 * Get the a item from the cart by Id
	 * 
	 * @param integer $id
	 * @return CartItem
	 */
	public function getItem($id) {
		
		if (isset ( $this->items [$id] )) {
			return $this->items[$id];
		}
		
		return false;
	} 
	  
	// Changes an item already in the cart:
	public function updateItem(CartItem $item, $qty) {
		
		// Need the unique item id:
		$id = $item->getId ();
		
		// Delete or update accordingly:
		if ($qty === 0) {
			$this->deleteItem ( $item );
		} elseif (($qty > 0) && ($qty != $item->getQty ())) {
			$this->items [$id]->setQty ( $qty );
		}
	} // End of updateItem() method.
	  
	// Add a domain in the cart:
	public function addDomain(CartItem $item, $domain, $tld_id, $mode = "registration") {
		
		// Need the unique item id:
		$id = $item->getId ();
		
		if (! empty ( $domain )) {
			$this->items [$id]->setDomain ( array (
					'domain' => $domain,
					'tld' => $tld_id,
					'price' => DomainsTlds::getPrice($tld_id, $mode),
					'mode' => $mode 
			) );
			
			$domainitem = new CartItem();
			$domainitem->setName($domain)->setId("domain")->setType('domain')->setTerm(1)->setUnitprice(DomainsTlds::getPrice($tld_id, $mode));
			$this->addItem($domainitem);
			
		}
	} // Add a domain in the cart.
	  
	// Add the customer in the cart:
	public function setCustomer($customerId) {
		if (! empty ( $customerId )) {
			$this->heading ['customer'] ['id'] = $customerId;
		}
	} // Add the customer in the cart.
	  
	// Add the reseller in the cart:
	public function setReseller($resellerId) {
		if (! empty ( $resellerId )) {
			$this->heading ['reseller'] ['id'] = $resellerId;
		}
	} // Add the reseller in the cart.
	  
	// Remove the reseller from the cart.
	public function removeCustomer() {
		unset ( $this->heading ['customer'] );
	} // Remove the reseller from the cart.
	  
	// Remove the reseller from the cart.
	public function removeReseller() {
		unset ( $this->heading ['reseller'] );
	} // Remove the reseller from the cart.
	  
	// Removes an item from the cart:
	public function deleteItem(CartItem $item) {
		
		// Need the unique item id:
		$id = $item->getId ();
		
		// Remove it:
		if (isset ( $this->items [$id] )) {
			unset ( $this->items [$id] );
			
			// Remove the stored id, too:
			$index = array_search ( $id, $this->ids );
			unset ( $this->ids [$index] );
			
			// Recreate that array to prevent holes:
			$this->ids = array_values ( $this->ids );
			
			return true;
		}
		
		return false;
	} // End of deleteItem() method.
	  
	// Required by Iterator; returns the current value:
	public function current() {
		
		// Get the index for the current position:
		$index = $this->ids [$this->position];
		
		// Return the item:
		return $this->items [$index];
	} // End of current() method.
	  
	// Required by Iterator; returns the current key:
	public function key() {
		return $this->position;
	}
	
	// Required by Iterator; increments the position:
	public function next() {
		$this->position ++;
	}
	
	// Required by Iterator; returns the position to the first spot:
	public function rewind() {
		$this->position = 0;
	}
	
	// Required by Iterator; returns a Boolean indiating if a value is indexed
	// at this position:
	public function valid() {
		return (isset ( $this->ids [$this->position] ));
	}
	
	// Required by Countable:
	public function count() {
		return count ( $this->items );
	}
	
	// Get cart items
	public function getItems() {
		return $this->items;
	}
	
	// Get cart heading
	public function getHeading() {
		return $this->heading;
	}
	
	// Get customer information
	public function getCustomer() {
		if (! empty ( $this->heading ['customer'] )) {
			return $this->heading ['customer'];
		} else {
			return null;
		}
	}
	
	// Get customer information
	public function getCustomerId() {
		if (! empty ( $this->heading ['customer'] ['id'] )) {
			return $this->heading ['customer'] ['id'];
		} else {
			return null;
		}
	}
	
	// Get reseller information
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
			
			$isVATFree = Customers::isVATFree ( $this->getCustomerId () );
			
			foreach ( $this->items as $item ) {
				$this->calcSubtotal ( $item, $isVATFree );
				
				$subtotals = $this->getSubtotal();
				$grandtotal = $this->getGrandtotal();
				$subtaxes = $this->getTaxes();
				$setupfee = $this->getSetupfee();
				
				$itemsubtot = $item->getSubtotal();

				$this->setSubtotal($subtotals + $itemsubtot['subtotal']);
				$this->setGrandtotal($grandtotal + $itemsubtot['price']);
				$this->setTaxes($subtaxes + $itemsubtot['taxes']);
				$this->setSetupfee($setupfee + $itemsubtot['setupfee']);
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
	private function calcSubtotal(CartItem $item, $isvatfree) {
		foreach ( $this->items as $item ) {
			
			$isrecurring = false;
			$months = 0;
			
			// Get all the product information
			$product = Products::getAllInfo ( $item->getId () );
			
			// Check the type of the product and get the price information
			if ($product ['ProductsAttributesGroups'] ['isrecurring']) {
				
				$isrecurring = true;
				
				// Get the billyng cycle / term / recurring period price
				$priceInfo = ProductsTranches::getTranchebyId ( $item->getTerm () );
				
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
			
			// ... and add the setup fees
			$price = $subtotal + $setupfee;
			
			$percentage = 0;
			$tax = 0;
			
			// check the taxes for each product
			if (! empty ( $product ['tax_id'] ) && ! $isvatfree) {
				if (! empty ( $product ['Taxes'] ['percentage'] ) && is_numeric ( $product ['Taxes'] ['percentage'] )) {
					$percentage = $product ['Taxes'] ['percentage'];
					$tax = ($price * $percentage) / 100;
					$price = ($price * (100 + $percentage)) / 100;
				}
			}
			
			$item->setSubtotal ( array (
					'unitprice' => $unitprice,
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
				$this->deleteItem($item);
			}
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
	 *
	 * @return multitype:unknown |boolean
	 */
	public function findHostingWithoutSite() {
		$NS = new Zend_Session_Namespace ( 'Default' );
		
		$lastproduct = "";
		if (isset ( $NS->cart->lastproduct )) {
			$lastproduct = $NS->cart->lastproduct;
		}
		
		foreach ( $NS->cart->products as $key => $products ) {
			if ($products ['type'] == 'hosting') {
				if ($products ['uri'] == $lastproduct && (! isset ( $products ['domain'] ) || $products ['domain'] != false)) {
					return array (
							'key' => $key,
							'value' => $products 
					);
				} elseif (! isset ( $products ['domain'] ) || $products ['domain'] != false) {
					return array (
							'key' => $key,
							'value' => $products 
					);
				}
			}
		}
		
		return false;
	}
	
	/*
	 *
	 * ############################################################################
	 */
	
	/**
	 * Check if within the cart exist already a hosting product selected
	 *
	 * @param unknown_type $selproduct        	
	 * @return boolean
	 */
	public function checkIfProductIsHostingAndPresentWithinCart($selproduct) {
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
	
	/**
	 * Add an item in the cart
	 */
	public static function addItem2($productId, $qtyId = 1) {
		
		// Check the Id of the product
		if (empty ( $productId ) || ! is_numeric ( $productId )) {
			return false;
		}
		
		// Get all the info about the product selected
		$product = Products::getAllInfo ( $productId );
		
		// Is a recurring product ?
		if (Products::isRecurring ( $productId )) {
			
			// Get the tranche selected
			$tranche = ProductsTranches::getTranchebyId ( $qtyId );
			
			$product ['quantity'] = $tranche ['quantity'];
			$product ['trancheid'] = $tranche ['tranche_id'];
			$product ['billingid'] = $tranche ['billing_cycle_id'];
			$product ['setupfee'] = $tranche ['setupfee'];
			
			$product ['isrecurring'] = true;
			
			$BillingCycleMonth = (intval ( $tranche ['BillingCycle'] ['months'] ) > 0) ? $tranche ['BillingCycle'] ['months'] : 1;
			
			$product ['price_1'] = $tranche ['price'] * $BillingCycleMonth;
		} else {
		}
		
		// Get the categories
		$product ['cleancategories'] = ProductsCategories::getCategoriesInfo ( $product ['categories'] );
		
		$product ['parent_orderid'] = "";
		
		Zend_Debug::dump ( $BillingCycleMonth );
		Zend_Debug::dump ( $product );
		
		return $product;
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
					// Match the product cycled with the hosting product
					// previously inserted in the cart
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
				// No products have been found. Adding the new hosting product
				// in the cart
				$NS->cart->products [] = $newproduct;
			}
		}
	}
	private function checkIfDomainIncluse($nameDomain) {
		$NS = new Zend_Session_Namespace ( 'Default' );
		foreach ( $NS->cart->products as $product ) {
			if ($product ['type'] == 'hosting' && isset ( $product ['site'] ) && $product ['site'] == $nameDomain) {
				return $product;
			}
		}
		
		return false;
	}
	private function getPricesWithRefundIfIsRecurring($orderid, $price, $billing_cicle_id) {
		$refundInfo = OrdersItems::getRefundInfo ( $orderid );
		if ($refundInfo != false) {
			$refund = $refundInfo ['refund'];
			$idBillingCircle = $billing_cicle_id;
			$monthBilling = BillingCycle::getMonthsNumber ( $idBillingCircle );
			$priceToPay = $price * $monthBilling;
			$priceToPayWithRefund = $priceToPay - $refund;
			if ($priceToPayWithRefund < 0) {
				$priceToPayWithRefund = $priceToPay;
			}
			
			return round ( $priceToPayWithRefund / $monthBilling, 2 );
		}
		
		return false;
	}
	private function getPriceWithRefund($orderid, $price) {
		$refundInfo = OrdersItems::getRefundInfo ( $orderid );
		if ($refundInfo != false) {
			$refund = $refundInfo ['refund'];
			$priceToPayWithRefund = $price - $refund;
			if ($priceToPayWithRefund > 0) {
				return $priceToPayWithRefund;
			}
			
			return $price;
		}
		
		return false;
	}
	private function checkIfIsUpgrade($productid) {
		$NS = new Zend_Session_Namespace ( 'Default' );
		if (is_array ( $NS->upgrade )) {
			// Check if the product is OK for upgrade and if OK take refund
			foreach ( $NS->upgrade as $orderid => $upgradeProduct ) {
				if (in_array ( $productid, $upgradeProduct )) {
					return $orderid;
				}
			}
		}
		return false;
	}
	
	/*
	 * Check if within the cart exist already the product selected
	 */
	public function checkIfProductIsPresentWithinCart() {
		if (! empty ( $cart->items ) && is_array ( $cart->items )) {
			foreach ( $cart->items as $product ) {
				if ($product ['product_id'] == $selproduct ['product_id']) {
					return true;
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
	 * Changing of the product
	 */
	private function changeProduct($newproduct) {
		$index = $this->getIndexProduct ( $newproduct );
		if ($index === false) {
			$NS->cart->products [] = $newproduct;
		} else {
			$products = $NS->cart->products;
			$product = $products [$index];
			// Delete the old product
			unset ( $products [$index] );
			
			// Reorder the indexes
			$NS->cart->products = array_values ( $products );
			
			// Adding the new hosting product in the cart
			$NS->cart->products [] = $newproduct;
		}
	}
	
	/**
	 * Get index product in the cart
	 * **
	 */
	private function getIndexProduct($newproduct) {
		$NS = new Zend_Session_Namespace ( 'Default' );
		if (! empty ( $NS->cart->products ) && is_array ( $NS->cart->products )) {
			$products = $NS->cart->products;
			$index = 0;
			
			if (is_array ( $products ) && count ( $products ) > 0) {
				
				// Read all the product added in the cart
				foreach ( $products as $product ) {
					// Match the product cycled with the hosting product
					// previously inserted in the cart
					if ($product ['product_id'] == $newproduct ['product_id']) {
						return $index;
					}
					$index ++;
				}
			}
		}
		
		return false;
	}
}