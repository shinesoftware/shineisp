<?php
/**
 * Cart helper
 */
class Zend_View_Helper_Cart extends Zend_View_Helper_Abstract
{
    public function cart()
    {
    	$NS = new Zend_Session_Namespace ( 'Default' );
    	if(isset($NS->cart)){
            $this->view->cartobj = $NS->cart;
    	}
        return $this->view->render ( 'partials/cart.phtml' );
    }
}