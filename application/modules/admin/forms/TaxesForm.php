<?php
class Admin_Form_TaxesForm extends Zend_Form
{   
    public function init()
    {
        // Set the custom decorator
    	$this->addElementPrefixPath('Shineisp_Decorator', 'Shineisp/Decorator/', 'decorator');
    	
        $this->addElement('text', 'name', array(
            'filters'    => array('StringTrim'),
            'label'      => 'Tax Name',
            'required'   => true,
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input'
        ));

        $this->addElement('text', 'percentage', array(
            'filters'    => array('StringTrim'),
            'label'      => 'Percentage',
            'required'   => true,
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input'
        ));  
        
        $this->addElement('hidden', 'tax_id');
    }
}