<?php
class Default_Form_ResellerForm extends Zend_Form
{   
    public function init()
    {
        // Set the custom decorator
    	$this->addElementPrefixPath('Shineisp_Decorator', 'Shineisp/Decorator/', 'decorator');
    	$translate = Shineisp_Registry::get('Zend_Translate');
    	
        $this->addElement('select', 'customers', array(
        'label' => $translate->_('Customers'),
        'decorators' => array('Composite'),
        'description'      => 'Select the company',
        'class'      => 'text-input large-input'
        ));
        
        $this->addElement('submit', 'submit', array(
            'required' => false,
            'label'      => $translate->_('Save'),
            'decorators' => array('Composite'),
            'class'    => 'button'
        ));
        
        $this->addElement('hidden', 'customer_id');
    }
}