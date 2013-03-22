<?php
class Admin_Form_ServicesForm extends Zend_Form
{   
    public function init()
    {
        // Set the custom decorator
    	$this->addElementPrefixPath('Shineisp_Decorator', 'Shineisp/Decorator/', 'decorator');
    	
        $this->addElement('text', 'date_start', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => 'Creation date',
            'decorators' => array('Composite'),
            'class'      => 'text-input little-input date'
        ));
        
        $this->addElement('text', 'date_end', array(
            'filters'    => array('StringTrim'),
            'label'      => 'Expiring date',
            'decorators' => array('Composite'),
            'class'      => 'text-input little-input date'
        ));   

        $this->addElement('text', 'quantity', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => 'Quantity',
            'decorators' => array('Composite'),
            'class'      => 'text-input little-input'
        ));           
        
        $this->addElement('textarea', 'message', array(
            'filters'    => array('StringTrim'),
            'label'      => 'Message',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input'
        ));
        
        $this->addElement('textarea', 'note', array(
            'filters'    => array('StringTrim'),
            'label'      => 'Note',
            'description' => 'Write here a note. An email will be sent to the ISP staff.',
            'decorators' => array('Composite'),
            'class'      => 'textarea'
        ));
        
        $this->addElement('textarea', 'setup', array(
            'filters'    => array('StringTrim'),
            'label'      => 'Setup Configuration',
            'description' => 'Here you can read the service configuration written by the ISP modules. These information are read-only.',
            'decorators' => array('Composite'),
            'class'      => 'textarea'
        ));
        
        $this->addElement('select', 'order_id', array(
        'label' => 'Orders',
        'decorators' => array('Composite'),
        'class'      => 'text-input large-input'
        ));
        
        $this->getElement('order_id')
                  ->setAllowEmpty(false)
                  ->setMultiOptions(Orders::getList(true));
                  
        $this->addElement('multiselect', 'domains', array(
        'label' => 'Available domains',
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

        $this->addElement('select', 'product_id', array(
        'label' => 'Products',
        'decorators' => array('Composite'),
        'class'      => 'text-input large-input'
        ));
        
        $this->getElement('product_id')
                  ->setAllowEmpty(false)
                  ->setMultiOptions(Products::getList(true));

        $this->addElement('select', 'billing_cycle_id', array(
        'label' => 'Billing Cycle',
        'decorators' => array('Composite'),
        'class'      => 'text-input large-input'
        ));
        
        $this->getElement('billing_cycle_id')
                  ->setAllowEmpty(false)
                  ->setMultiOptions(BillingCycle::getList(true));   

        $this->addElement('select', 'status_id', array(
        'label' => 'Status',
        'decorators' => array('Composite'),
        'class'      => 'text-input large-input'
        ));
        
        $this->getElement('status_id')
                  ->setAllowEmpty(false)
                  ->setMultiOptions(Statuses::getList('orders', false, true));                     
        
		$this->addElement('select', 'autorenew', array(
            'filters'     => array('StringTrim'),
            'decorators'  => array('Composite'),
            'label'       => 'Autorenew',
            'description' => 'Enable or disable the automatic renewal of the service',
            'class'       => 'text-input large-input'
        ));
        
        $this->getElement('autorenew')
                  ->setAllowEmpty(false)
                  ->setMultiOptions(array('1'=>'Yes, I would like to renew the service at the expiration date.', '0'=>'No, I am not interested in the service renew.'));
                  
        $this->addElement('submit', 'save', array(
            'required' => false,
            'label'    => 'Save',
            'decorators' => array('Composite'),
            'class'    => 'button'
        ));
        
        $this->addElement('hidden', 'detail_id');
    }
}