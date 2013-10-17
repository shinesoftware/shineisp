<?php
/**
 * Cart helper
 */
class Zend_View_Helper_Cart extends Zend_View_Helper_Abstract
{
    public function cart()
    {
    	$session = new Zend_Session_Namespace ( 'Default' );
    	if(isset($session->cart) && is_a($session->cart, 'Cart')){
            $this->view->cartobj = $session->cart;
    	}
        return $this->view->render ( 'partials/cart.phtml' );
    }
}