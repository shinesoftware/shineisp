<?php
class Admin_Form_CustomersGroupsForm extends Zend_Form
{
    
    public function init()
    {
        // Set the custom decorator
        $this->addElementPrefixPath('Shineisp_Decorator', 'Shineisp/Decorator/', 'decorator');
        
        $this->addElement('text', 'name', array(
            'filters'     => array('StringTrim'),
            'required'    => true,
            'decorators'  => array('Composite'),
            'label'       => 'Name',
            'class'       => 'text-input large-input'
        ));
        
        $this->addElement('hidden', 'group_id');

    }
    
}