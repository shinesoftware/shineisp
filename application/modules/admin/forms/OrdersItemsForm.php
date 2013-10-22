<?php
class Admin_Form_OrdersItemsForm extends Zend_Form
{   
    public function init()
    {
        // Set the custom decorator
    	$this->addElementPrefixPath('Shineisp_Decorator', 'Shineisp/Decorator/', 'decorator');
    	$translate = Shineisp_Registry::get('Zend_Translate');
    	
    	$this->addElement('text', 'quantity', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => $translate->_('Quantity'),
            'decorators' => array('Composite'),
            'class'      => 'little-input'
        ));
    	
    	$this->addElement('text', 'setupfee', array(
            'filters'    => array('StringTrim'),
            'label'      => $translate->_('Setup fees'),
            'decorators' => array('Composite'),
            'class'      => 'little-input'
        ));
    	
    	$this->addElement('text', 'cost', array(
            'filters'    => array('StringTrim'),
            'label'      => $translate->_('Cost'),
            'decorators' => array('Composite'),
            'class'      => 'little-input'
        ));
        
        $this->addElement('select', 'product_id', array(
            'filters'    => array('StringTrim'),
            'required'   => false,
            'label'      => $translate->_('Products'),
            'class'      => 'input-large'
        ));
        
        $this->getElement('product_id')
                  ->setAllowEmpty(false)
                  ->setMultiOptions(Products::getList(true));        
        
        $this->addElement('text', 'price', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => $translate->_('Price'),
            'decorators' => array('Composite'),
            'class'      => 'little-input'
        ));
        
        $this->addElement('select', 'billing_cycle_id', array(
        'label' => $translate->_('Billing Cycle'),
        'decorators' => array('Composite'),
        'class'      => 'input-large'
        ));
        
        $this->getElement('billing_cycle_id')
                  ->setAllowEmpty(false)
                  ->setMultiOptions(BillingCycle::getList(true));         
        
        $this->addElement('text', 'date_start', array(
            'filters'    => array('StringTrim'),
            'label'      => $translate->_('Start Date'),
            'required'   => true,
            'decorators' => array('Composite'),
            'class'      => 'little-input date'
        ));
        
        $this->addElement('text', 'date_end', array(
            'filters'    => array('StringTrim'),
            'label'      => $translate->_('Expiry Date'),
            'decorators' => array('Composite'),
            'class'      => 'little-input date'
        ));   
        
        $this->addElement('textarea', 'description', array(
            'filters'    => array('StringTrim'),
            'label'      => $translate->_('Description'),
            'decorators' => array('Composite'),
        	'rows'		 => 5,
            'class'      => 'input-large'
        ));
        
        $this->addElement('textarea', 'parameters', array(
            'filters'    => array('StringTrim'),
            'label'      => $translate->_('Service Panel Configuration'),
            'decorators' => array('Composite'),
        	'rows'		 => 5,
            'description' => $translate->_('Parameters model accepted: {"domain":"mydomain.com","action":"registerDomain"}'),
            'class'      => 'input-large'
        ));
        
        $this->addElement('select', 'status_id', array(
			        'label' => $translate->_('Status'),
			        'required' => true,
			        'decorators' => array('Composite'),
			        'class'      => 'input-large'
			        ));
			        
        $this->getElement('status_id')
                  ->setAllowEmpty(false)
                  ->setMultiOptions(Statuses::getList('orders'));
        
        $this->addElement('select', 'autorenew', array(
			            'decorators'  => array('Composite'),
			            'label'       => $translate->_('Auto Renewal'),
			            'class'       => 'input-large',
			        	'multioptions' => array( 0=>'NO', 1=> 'YES')
			        )); 


        $this->addElement('multiselect', 'domains', array(
			        'label' => $translate->_('Available Domains'),
			        'decorators' => array('Composite'),
			        'class'      => 'input-large tmpitems'
			        ));
        
        $this->getElement('domains')
                  ->setAllowEmpty(false)
                  ->setRegisterInArrayValidator(false)  // Disable the Validator in order to manage a dynamic list.
                  ->setMultiOptions(Domains::getList()); 

        $this->addElement('multiselect', 'domains_selected', array(
			        'label' => $translate->_('Selected domains'),
			        'decorators' => array('Composite'),
			        'class'      => 'input-large items'
			        ));        

        $this->getElement('domains_selected')
                  ->setRegisterInArrayValidator(false);  // Disable the Validator in order to manage a dynamic list.

        
        $this->addElement('hidden', 'detail_id');
    }
}