<?php

class CompareController extends Shineisp_Controller_Default {
	protected $translator;
	
	/**
	 * preDispatch
	 * Starting of the module
	 * (non-PHPdoc)
	 * @see library/Zend/Controller/Shineisp_Controller_Default#preDispatch()
	 */
	
	public function preDispatch() {
		$this->translator = Shineisp_Registry::getInstance ()->Zend_Translate;
	}
	
	
	/**
	 * compareAction
	 * Compare the services between themselves
	 */
	public function productsAction() {
		
		$code = $this->getRequest()->getParam('code');
		
		if ($code)
			$this->view->headTitle()->prepend ( $this->translator->_("Compare %s", $code) );
		
	 		// Get all the data from the database
			$data = Products::GetProductsByGroupCode($code);
			
			// Check the existence of the mandatories attributes
			if (!empty($data['attributes'][0]))
				$this->view->attributes = $data['attributes'];
				
				// Check if there are values set for the group of the product selected
				if (!empty($data['attributes_values'][0]))
					$this->view->values = $data['attributes_values'];
					
					// Get the products
					if (!empty($data['products'][0]))
						$this->view->products = $data['products'];
			
		// Render the page
		$this->_helper->viewRenderer('index');
	}
}