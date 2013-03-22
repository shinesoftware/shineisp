<?php
/**
 * Cart helper
 */
class Zend_View_Helper_Cart extends Zend_View_Helper_Abstract
{
    public function cart()
    {
    	$NS = new Zend_Session_Namespace ( 'Default' );
    	if(isset($NS->cart->products)){
            $this->view->cartitems = $NS->cart->products;
    	}
        return $this->view->render ( 'partials/cart.phtml' );
    }
}