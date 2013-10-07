<?php
class Default_Form_PaymentForm extends Zend_Form{
    
    public function init()
    {
        // Set the custom decorator
        $this->addElementPrefixPath('Shineisp_Decorator', 'Shineisp/Decorator/', 'decorator');
        
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
            'id'    => 'orderit',
            'class'    => 'button success'
        ));
        
    }
}