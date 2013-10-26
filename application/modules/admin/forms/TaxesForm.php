<?php
class Admin_Form_TaxesForm extends Zend_Form
{   
    public function init()
    {
        // Set the custom decorator
    	$this->addElementPrefixPath('Shineisp_Decorator', 'Shineisp/Decorator/', 'decorator');
    	$translate = Shineisp_Registry::get('Zend_Translate');
    	
        $this->addElement('text', 'name', array(
            'filters'    => array('StringTrim'),
            'label'      => $translate->_('Tax Name'),
            'required'   => true,
            'decorators' => array('Bootstrap'),
            'class'      => 'input-large'
        ));

        $this->addElement('text', 'percentage', array(
            'filters'    => array('StringTrim'),
            'label'      => $translate->_('Percentage'),
            'required'   => true,
            'decorators' => array('Bootstrap'),
            'class'      => 'input-large'
        ));  
        
        $this->addElement('hidden', 'tax_id');
    }
}