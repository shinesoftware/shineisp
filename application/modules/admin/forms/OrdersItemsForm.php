<?php
class Admin_Form_OrdersItemsForm extends Zend_Form
{   
    public function init()
    {
        // Set the custom decorator
    	$this->addElementPrefixPath('Shineisp_Decorator', 'Shineisp/Decorator/', 'decorator');
    	
    	$this->addElement('text', 'quantity', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => 'Quantity',
            'decorators' => array('Composite'),
            'class'      => 'text-input little-input'
        ));
    	
    	$this->addElement('text', 'setupfee', array(
            'filters'    => array('StringTrim'),
            'label'      => 'Setup fees',
            'decorators' => array('Composite'),
            'class'      => 'text-input little-input'
        ));
    	
    	$this->addElement('text', 'cost', array(
            'filters'    => array('StringTrim'),
            'label'      => 'Cost',
            'decorators' => array('Composite'),
            'class'      => 'text-input little-input'
        ));
        
        $this->addElement('select', 'product_id', array(
            'filters'    => array('StringTrim'),
            'required'   => false,
            'label'      => 'Products',
            'class'      => 'text-input large-input'
        ));
        
        $this->getElement('product_id')
                  ->setAllowEmpty(false)
                  ->setMultiOptions(Products::getList(true));        
        
        $this->addElement('text', 'price', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => 'Price',
            'decorators' => array('Composite'),
            'class'      => 'text-input little-input'
        ));
        
        $this->addElement('select', 'billing_cycle_id', array(
        'label' => 'Billing Cycle',
        'decorators' => array('Composite'),
        'class'      => 'text-input large-input'
        ));
        
        $this->getElement('billing_cycle_id')
                  ->setAllowEmpty(false)
                  ->setMultiOptions(BillingCycle::getList(true));         
        
        $this->addElement('text', 'date_start', array(
            'filters'    => array('StringTrim'),
            'label'      => 'Start Date',
            'required'   => true,
            'decorators' => array('Composite'),
            'class'      => 'text-input little-input date'
        ));
        
        $this->addElement('text', 'date_end', array(
            'filters'    => array('StringTrim'),
            'label'      => 'Expiring date',
            'decorators' => array('Composite'),
            'class'      => 'text-input little-input date'
        ));   
        
        $this->addElement('textarea', 'description', array(
            'filters'    => array('StringTrim'),
            'label'      => 'Description',
            'decorators' => array('Composite'),
        	'rows'		 => 5,
            'class'      => 'text-input large-input'
        ));
        
        $this->addElement('textarea', 'parameters', array(
            'filters'    => array('StringTrim'),
            'label'      => 'Service Panel Configuration',
            'decorators' => array('Composite'),
        	'rows'		 => 5,
            'description' => 'Parameters model accepted: {"domain":"picasa.com","action":"registerDomain"}',
            'class'      => 'text-input large-input'
        ));
        
        $this->addElement('select', 'status_id', array(
			        'label' => 'Status',
			        'required' => true,
			        'decorators' => array('Composite'),
			        'class'      => 'text-input large-input'
			        ));
			        
        $this->getElement('status_id')
                  ->setAllowEmpty(false)
                  ->setMultiOptions(Statuses::getList('orders'));
        
        $this->addElement('select', 'autorenew', array(
			            'decorators'  => array('Composite'),
			            'label'       => 'Autorenew',
			            'class'       => 'text-input large-input',
			        	'multioptions' => array( 0=>'NO', 1=> 'YES')
			        )); 


        $this->addElement('multiselect', 'domains', array(
			        'label' => 'Available Domains',
			        'decorators' => array('Composite'),
			        'class'      => 'text-input large-input tmpitems'
			        ));
        
        $this->getElement('domains')
                  ->setAllowEmpty(false)
                  ->setRegisterInArrayValidator(false)  // Disable the Validator in order to manage a dynamic list.
                  ->setMultiOptions(Domains::getList()); 

        $this->addElement('multiselect', 'domains_selected', array(
			        'label' => 'Selected domains',
			        'decorators' => array('Composite'),
			        'class'      => 'text-input large-input items'
			        ));        

        $this->getElement('domains_selected')
                  ->setRegisterInArrayValidator(false);  // Disable the Validator in order to manage a dynamic list.

        $this->addElement('submit', 'save', array(
			            'required' => false,
			            'label'    => 'Save',
			            'decorators' => array('Composite'),
			            'class'    => 'button'
			        ));
        
        $this->addElement('reset', 'reset', array(
            'required' => false,
            'label'    => 'reset',
            'decorators' => array('Composite'),
            'class'    => 'button'
        ));
        
        $this->addElement('hidden', 'detail_id');
    }
}