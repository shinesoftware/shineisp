<?php
class Admin_Form_LegalformsForm extends Zend_Form
{
    
    public function init()
    {
        // Set the custom decorator
        $this->addElementPrefixPath('Shineisp_Decorator', 'Shineisp/Decorator/', 'decorator');
        $translate = Shineisp_Registry::get('Zend_Translate');
        
        $this->addElement('text', 'name', array(
            'filters'     => array('StringTrim'),
            'required'    => true,
            'decorators'  => array('Bootstrap'),
            'label'       => $translate->_('Name'),
            'class'       => 'form-control'
        ));
        
        $this->addElement('hidden', 'legalform_id');

    }
    
}