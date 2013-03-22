<?php
class Default_Form_BulkdomainsForm extends Zend_Form
{
    
    public function init()
    {
    	// Set the custom decorator
        $this->addElementPrefixPath('Shineisp_Decorator', 'Shineisp/Decorator/', 'decorator');
        
        $this->addElement('textarea', 'domains', array(
            'filters'     => array('StringTrim'),
            'required'    => true,
            'label'       => 'Domains',
            'class'       => 'bulktextarea'
        ));
                
        $this->addElement('submit', 'bulksearch', array(
            'label'    => 'Check Now',
            'decorators' => array('Composite'),
            'class'    => 'button bigbtn'
        ));

    }
    
}