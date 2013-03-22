<?php
class Default_Form_CartsummaryForm extends Zend_Form
{
    
    public function init()
    {
        // Set the custom decorator
        $this->addElementPrefixPath('Shineisp_Decorator', 'Shineisp/Decorator/', 'decorator');
        
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
                  ->setMultiOptions(Banks::getActive()); 
                  
        $this->addElement('textarea', 'note', array(
            'filters'     => array('StringTrim'),
            'decorators'  => array('Composite'),
            'label'       => 'Note',
            'required'    => false,
            'description' => 'Write here a note.',
            'rows' => '10',
            'class'       => 'textarea'
        ));
        
        $this->addElement('submit', 'submit', array(
            'required' => false,
            'label'    => 'Order Now',
            'decorators' => array('Composite'),
            'class'    => 'button bigbtn'
        ));
    }
}