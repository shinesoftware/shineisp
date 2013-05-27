<?php
class Admin_Form_ApplicationsForm extends Zend_Form
{
    
    public function init()
    {
        // Set the custom decorator
        $this->addElementPrefixPath('Shineisp_Decorator', 'Shineisp/Decorator/', 'decorator');
        
        $this->addElement('text', 'redirect_uri', array(
            'filters'     => array('StringTrim'),
            'required'    => true,
            'decorators'  => array('Composite'),
            'label'       => 'Redirect URI',
            'class'       => 'text-input large-input'
        ));
		
		$this->addElement('hidden', 'id');
    }
    
}