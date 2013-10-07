<?php
class Default_Form_PaymentForm extends Zend_Form{
    
    public function init()
    {
        // Set the custom decorator
        $this->addElementPrefixPath('Shineisp_Decorator', 'Shineisp/Decorator/', 'decorator');
        
        // get the bank payment gateway
        $banks = Banks::findAllActive ( "name, description, classname", true );
        
        foreach ( $banks as $bank ) {
        	if (! empty ( $bank ['classname'] ) && class_exists ( $bank ['classname'] )) {
        		$payments[$bank['bank_id']]['name'] = $bank['name'];
        		$payments[$bank['bank_id']]['description'] = $bank['description'];
        	}
        }
        
        $this->addElement('radio', 'payment', array(
            'filters'     => array('StringTrim'),
            'required'    => true,
            'decorators'  => array('Composite'),
            'label'       => 'Payment Mode',
            'class'       => 'textarea'
        ));
                
        $payment = $this->addElement('select', 'payment', array(
	        'label' => 'Payment',
	        'decorators' => array('Composite'),
	        'class'      => 'text-input large-input'
        ));
        
        $payment = $this->getElement('payment')
                  ->setAllowEmpty(false)
                  ->setMultiOptions($payments); 
                  
        $this->addElement('textarea', 'note', array(
            'filters'     => array('StringTrim'),
            'decorators'  => array('Composite'),
            'label'       => 'Note',
            'description' => 'Write here a note.',
            'rows' => '10',
            'class'       => 'textarea'
        ));
        
        $this->addElement('submit', 'submit', array(
            'label'    => 'Order Now',
            'decorators' => array('Composite'),
            'class'    => 'button bigbtn'
        ));
    }
}