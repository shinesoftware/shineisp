<?php

class Default_Form_PaymentForm extends Zend_Form{
    
    public function init()
    {
        // Set the custom decorator
        $this->addElementPrefixPath('Shineisp_Decorator', 'Shineisp/Decorator/', 'decorator');
        $translate = Shineisp_Registry::get('Zend_Translate');
        
        $this->addElement('textarea', 'note', array(
            'filters'     => array('StringTrim'),
            'decorators'  => array('Bootstrap'),
            'label'      => $translate->_('Note'),
            'description' => $translate->_('Write here a note.'),
            'style' => 'height:180px',
            'class'       => 'form-control'
        ));
        
        $this->addElement('submit', 'submit', array(
            'label'      => $translate->_('Order Now'),
            'decorators' => array('Bootstrap'),
            'id'    => 'orderit',
            'class'    => 'btn btn-success btn-lg'
        ));
        
    }
}