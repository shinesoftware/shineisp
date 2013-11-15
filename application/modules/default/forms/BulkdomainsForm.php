<?php
class Default_Form_BulkdomainsForm extends Zend_Form
{
    
    public function init()
    {
    	// Set the custom decorator
        $this->addElementPrefixPath('Shineisp_Decorator', 'Shineisp/Decorator/', 'decorator');
        $translate = Shineisp_Registry::get('Zend_Translate');
        
        $this->addElement('textarea', 'domains', array(
            'filters'     => array('StringTrim'),
            'required'    => true,
            'label'      => $translate->_('Domains'),
            'class'       => 'form-control'
        ));
                
        $this->addElement('submit', 'bulksearch', array(
            'label'      => $translate->_('Check Now'),
            'decorators' => array('Bootstrap'),
            'class'    => 'btn btn-primary bigbtn'
        ));

    }
    
}