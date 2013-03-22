<?php
/**
 * Prices helper
 */
class Zend_View_Helper_Prices extends Zend_View_Helper_Abstract
{
    public $view;

    public function setView(Zend_View_Interface $view)
    {
        $this->view = $view;
    }

    /**
     * Get the class helper
     * 
     * @return Zend_View_Helper_Prices
     */
    public function prices() {
    	return $this;
    }
    
    /**
     * Get all the information about the price
     * @param integer $productid
     * @param boolean $showallinfo
     * @return string
     */
    public function getAllPricesInfo($productid, $showallinfo = true)
    {
    	$this->view->data = Products::getPrices($productid);
    	$this->view->showallinfo = $showallinfo;
        return $this->view->render ( 'partials/prices.phtml' );
    }

    /**
     * Get the price including the VAT tax rate
     * @param integer $productid
     * @return string
     */
    public function getPriceIncludedVat($productid)
    {
    	$currency = new Zend_Currency();
    	$translator = Zend_Registry::getInstance ()->Zend_Translate;
    	$measurement = "";
    	
    	$data = Products::getPrices($productid);

    	if ($data ['type'] == "multiple") {
    		if (! empty ( $data ['minvalue'] )) {
    	
    			// Get the minimum value from the group of the prices
    			$pricetax = $data ['minvaluewithtaxes'];
    			$pricetax = $currency->toCurrency($pricetax, array('currency' => Settings::findbyParam('currency')));
    	
    			// Get the recurring period label
    			if(!empty($data['tranches'][0] ['measurement'])){
    				$measurement = '<span class="frequency">'.$translator->translate($data['tranches'][0] ['measurement']).'</span>';
    			}
    	
    			// Print the price
    			return $pricetax . $measurement;
    		}
    	} else {
    		
    		$pricetax = $data ['taxincluded'];
    		
    		// Print the price
    		return $pricetax;
    	}
    	
    }
}