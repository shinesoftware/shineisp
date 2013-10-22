<?php

class Default_Form_PaymentForm extends Zend_Form{
    
    public function init()
    {
        // Set the custom decorator
        $this->addElementPrefixPath('Shineisp_Decorator', 'Shineisp/Decorator/', 'decorator');
        $translate = Shineisp_Registry::get('Zend_Translate');
        
        $this->addElement('textarea', 'note', array(
            'filters'     => array('StringTrim'),
            'decorators'  => array('Composite'),
            'label'      => $translate->_('Note'),
            'description' => $translate->_('Write here a note.'),
            'style' => 'height:180px',
            'class'       => 'textarea'
        ));
        
        $this->addElement('submit', 'submit', array(
            'label'      => $translate->_('Order Now'),
            'decorators' => array('Composite'),
            'id'    => 'orderit',
            'class'    => 'button success'
        ));
        
    }
}