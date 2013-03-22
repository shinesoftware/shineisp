<?php
/**
 * Google Graph helper
 * 
	array(4) {
	  ["axis"] => array(2) {
	    ["x"] => string(6) "Months"
	    ["y"] => string(4) "Euro"
	  }
	  ["labels"] => array(6) {
	    [0] => array(2) {
	      ["type"] => string(6) "string"
	      ["title"] => string(6) "Months"
	    }
	    [1] => array(2) {
	      ["type"] => string(6) "number"
	      ["title"] => string(11) "Grand Total"
	    }
	    [2] => array(2) {
	      ["type"] => string(6) "number"
	      ["title"] => string(20) "Entrate (Imponibile)"
	    }
	    [3] => array(2) {
	      ["type"] => string(6) "number"
	      ["title"] => string(34) "Entrate Nette (Imponibile - Costi)"
	    }
	    [4] => array(2) {
	      ["type"] => string(6) "number"
	      ["title"] => string(5) "Costo"
	    }
	    [5] => array(2) {
	      ["type"] => string(6) "number"
	      ["title"] => string(3) "Vat"
	    }
	  }
	  ["records"] => array(3) {
	    ["January"] => array(1) {
	      [0] => array(5) {
	        ["grandtotal"] => string(7) "4981.11"
	        ["total"] => string(7) "4116.60"
	        ["totalnet"] => float(3103.24)
	        ["cost"] => string(7) "1013.36"
	        ["vat"] => float(-700.27)
	      }
	    }
	    .
	    .
	    .
	    .
	    .
	  }
	  ["title"] => string(51) "ShineISP Performances in 2012 - 5000"
	}
 * 
 * 
 */
class Admin_View_Helper_Googlegraph extends Zend_View_Helper_Abstract
{
    public function googlegraph(array $data, $year = null)
    {
        $auth = Zend_Auth::getInstance();
        if ($auth->hasIdentity()) {
        	if(!empty($data)){
            	$this->view->datagraph = $data;
        	}
        }
        return $this->view->render ( 'partials/googlegraph.phtml' );
    }
}