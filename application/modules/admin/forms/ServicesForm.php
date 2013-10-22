<?php
class Admin_Form_ServicesForm extends Zend_Form
{   
    public function init()
    {
        // Set the custom decorator
    	$this->addElementPrefixPath('Shineisp_Decorator', 'Shineisp/Decorator/', 'decorator');
    	$translate = Shineisp_Registry::get('Zend_Translate');
    	
        $this->addElement('text', 'date_start', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => $translate->_('Creation date'),
            'decorators' => array('Composite'),
            'class'      => 'little-input date'
        ));
        
        $this->addElement('text', 'date_end', array(
            'filters'    => array('StringTrim'),
            'label'      => $translate->_('Expiry Date'),
            'decorators' => array('Composite'),
            'class'      => 'little-input date'
        ));   

        $this->addElement('text', 'quantity', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => $translate->_('Quantity'),
            'decorators' => array('Composite'),
            'class'      => 'little-input'
        ));           
        
        $this->addElement('textarea', 'message', array(
            'filters'    => array('StringTrim'),
            'label'      => $translate->_('Message'),
            'decorators' => array('Composite'),
            'class'      => 'input-large'
        ));
        
        $this->addElement('textarea', 'note', array(
            'filters'    => array('StringTrim'),
            'label'      => $translate->_('Note'),
            'description' => $translate->_('Write here a note. An email will be sent to the ISP staff.'),
            'decorators' => array('Composite'),
            'class'      => 'textarea'
        ));
        
        $this->addElement('textarea', 'setup', array(
            'filters'    => array('StringTrim'),
            'label'      => $translate->_('Setup Configuration'),
            'description' => $translate->_('Here you can read the service configuration written by the ISP modules. These information are read-only.'),
            'decorators' => array('Composite'),
            'class'      => 'textarea'
        ));
        
        $this->addElement('select', 'order_id', array(
        'label' => $translate->_('Orders'),
        'decorators' => array('Composite'),
        'class'      => 'input-large'
        ));
        
        $this->getElement('order_id')
                  ->setAllowEmpty(false)
                  ->setMultiOptions(Orders::getList(true));
                  
        $this->addElement('multiselect', 'domains', array(
        'label' => $translate->_('Available domains'),
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

        $this->addElement('select', 'product_id', array(
        'label' => $translate->_('Products'),
        'decorators' => array('Composite'),
        'class'      => 'input-large'
        ));
        
        $this->getElement('product_id')
                  ->setAllowEmpty(false)
                  ->setMultiOptions(Products::getList(true));

        $this->addElement('select', 'billing_cycle_id', array(
        'label' => $translate->_('Billing Cycle'),
        'decorators' => array('Composite'),
        'class'      => 'input-large'
        ));
        
        $this->getElement('billing_cycle_id')
                  ->setAllowEmpty(false)
                  ->setMultiOptions(BillingCycle::getList(true));   

        $this->addElement('select', 'status_id', array(
        'label' => $translate->_('Status'),
        'decorators' => array('Composite'),
        'class'      => 'input-large'
        ));
        
        $this->getElement('status_id')
                  ->setAllowEmpty(false)
                  ->setMultiOptions(Statuses::getList('orders', false, true));                     
        
		$this->addElement('select', 'autorenew', array(
            'filters'     => array('StringTrim'),
            'decorators'  => array('Composite'),
            'label'       => $translate->_('Auto Renewal'),
            'description' => $translate->_('Enable or disable the automatic renewal of the service'),
            'class'       => 'input-large'
        ));
        
        $this->getElement('autorenew')
                  ->setAllowEmpty(false)
                  ->setMultiOptions(array('1'=> $translate->_('Yes, I would like to renew the service at the expiration date.'), '0'=> $translate->_('No, I am not interested in the service renew.')));
        
        $this->addElement('hidden', 'detail_id');
    }
}