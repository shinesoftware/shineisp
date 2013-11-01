<?php

class Admin_Form_PaymentsForm extends Zend_Form
{   
    public function init()
    {
        // Set the custom decorator
    	$this->addElementPrefixPath('Shineisp_Decorator', 'Shineisp/Decorator/', 'decorator');
    	$translate = Shineisp_Registry::get('Zend_Translate');
    	
        $this->addElement('text', 'paymentdate', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => $translate->_('Payment date'),
            'decorators' => array('Bootstrap'),
            'class'      => 'form-control date'
        ));
    	
        $this->addElement('text', 'reference', array(
            'filters'    => array('StringTrim'),
            'label'      => $translate->_('Transaction Reference'),
            'decorators' => array('Bootstrap'),
            'class'      => 'form-control'
        ));
    	
        $this->addElement('text', 'income', array(
            'filters'    => array('StringTrim'),
            'label'      => $translate->_('Income'),
            'decorators' => array('Bootstrap'),
            'class'      => 'form-control'
        ));
    	
        $this->addElement('text', 'outcome', array(
            'filters'    => array('StringTrim'),
            'label'      => $translate->_('Expense'),
            'decorators' => array('Bootstrap'),
            'class'      => 'form-control'
        ));

        $this->addElement('select', 'confirmed', array(
        		'filters'    => array('StringTrim'),
        		'label'      => $translate->_('Does the Transaction has been confirmed?'),
        		'decorators' => array('Bootstrap'),
        		'class'      => 'form-control'
        ));
        
        $this->getElement('confirmed')
        ->setAllowEmpty(false)
        ->setMultiOptions(array('0' => "No, it has been not", '1' => "Yes, it has been" ));

        $this->addElement('select', 'bank_id', array(
        		'decorators'  => array('Bootstrap'),
        		'label'       => 'Method of Payments',
        		'class'       => 'form-control',
        ));
        
        $this->getElement('bank_id')
			        ->setAllowEmpty(false)
			        ->setRegisterInArrayValidator(false)
			        ->setMultiOptions(Banks::getList());

        $this->addElement('select', 'order_id', array(
        		'decorators'  => array('Bootstrap'),
        		'label'       => 'Order',
        		'class'       => 'form-control',
        ));
        
        $this->getElement('order_id')
			        ->setAllowEmpty(false)
			        ->setRegisterInArrayValidator(false)
			        ->setMultiOptions(Orders::getList());

        $this->addElement('select', 'customer_id', array(
        		'decorators'  => array('Bootstrap'),
        		'label'       => 'Customer',
        		'class'       => 'form-control',
        ));
        
        $this->getElement('customer_id')
			        ->setAllowEmpty(false)
			        ->setRegisterInArrayValidator(false)
			        ->setMultiOptions(Customers::getList());
        
        $this->addElement('textarea', 'description', array(
        		'filters'    => array('StringTrim'),
        		'label'      => $translate->_('Description'),
        		'id'         => 'description',
        		'rows'         => '3',
        		'decorators' => array('Bootstrap'),
        		'class'      => 'col-lg-12 form-control'
        ));
        
        $this->addElement('hidden', 'payment_id');
    }
}