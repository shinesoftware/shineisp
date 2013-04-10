<?php

class Admin_Form_PaymentsForm extends Zend_Form
{   
    public function init()
    {
        // Set the custom decorator
    	$this->addElementPrefixPath('Shineisp_Decorator', 'Shineisp/Decorator/', 'decorator');
    	
        $this->addElement('text', 'paymentdate', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => 'Payment date',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input date'
        ));
    	
        $this->addElement('text', 'reference', array(
            'filters'    => array('StringTrim'),
            'label'      => 'Transaction Reference',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input'
        ));
    	
        $this->addElement('text', 'income', array(
            'filters'    => array('StringTrim'),
            'label'      => 'Income',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input'
        ));
    	
        $this->addElement('text', 'outcome', array(
            'filters'    => array('StringTrim'),
            'label'      => 'Outcome',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input'
        ));

        $this->addElement('select', 'confirmed', array(
        		'filters'    => array('StringTrim'),
        		'label'      => 'Does the Transaction has been confirmed?',
        		'decorators' => array('Composite'),
        		'class'      => 'text-input large-input'
        ));
        
        $this->getElement('confirmed')
        ->setAllowEmpty(false)
        ->setMultiOptions(array('0' => "No, it has been not", '1' => "Yes, it has been" ));

        $this->addElement('select', 'bank_id', array(
        		'decorators'  => array('Composite'),
        		'label'       => 'Method of Payments',
        		'class'       => 'text-input large-input',
        ));
        
        $this->getElement('bank_id')
			        ->setAllowEmpty(false)
			        ->setRegisterInArrayValidator(false)
			        ->setMultiOptions(Banks::getList());

        $this->addElement('select', 'order_id', array(
        		'decorators'  => array('Composite'),
        		'label'       => 'Order',
        		'class'       => 'text-input large-input',
        ));
        
        $this->getElement('order_id')
			        ->setAllowEmpty(false)
			        ->setRegisterInArrayValidator(false)
			        ->setMultiOptions(Orders::getList());

        $this->addElement('select', 'customer_id', array(
        		'decorators'  => array('Composite'),
        		'label'       => 'Customer',
        		'class'       => 'text-input large-input',
        ));
        
        $this->getElement('customer_id')
			        ->setAllowEmpty(false)
			        ->setRegisterInArrayValidator(false)
			        ->setMultiOptions(Customers::getList());
        
        $this->addElement('textarea', 'description', array(
        		'filters'    => array('StringTrim'),
        		'label'      => 'Description',
        		'id'         => 'description',
        		'rows'         => '3',
        		'decorators' => array('Composite'),
        		'class'      => 'textarea large-input'
        ));
        
        $this->addElement('hidden', 'payment_id');
    }
}