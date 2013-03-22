<?php
class Admin_Form_BanksForm extends Zend_Form
{   
    public function init()
    {
        // Set the custom decorator
    	$this->addElementPrefixPath('Shineisp_Decorator', 'Shineisp/Decorator/', 'decorator');
    	
        $this->addElement('text', 'name', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => 'Name',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input'
        ));
        
        $this->addElement('text', 'account', array(
            'filters'    => array('StringTrim'),
            'label'      => 'Account',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input'
        ));
        
        $this->addElement('textarea', 'description', array(
            'filters'    => array('StringTrim'),
            'label'      => 'Account',
            'decorators' => array('Composite'),
            'class'      => 'textarea wysiwyg'
        ));
        
        $this->addElement('text', 'url_test', array(
            'label'      => 'URL Test',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input'
        ));
        
        $this->addElement('text', 'url_official', array(
            'label'      => 'URL Official',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input'
        ));        
        
        $this->addElement('text', 'classname', array(
            'label'      => 'Classname',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input'
        ));        
        
        $this->addElement('checkbox', 'enabled', array(
            'label'      => 'Enabled',
            'decorators' => array('Composite'),
            'class'      => 'checkbox'
        ));        
        
        $this->addElement('checkbox', 'test_mode', array(
            'label'      => 'Test Mode',
            'decorators' => array('Composite'),
            'class'      => 'checkbox'
        ));        
        
        $this->addElement('select', 'method_id', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'id'         => 'paymentmethods',
            'label'      => 'Payment methods',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input'
        ));
        
        $this->getElement('method_id')
                  ->setAllowEmpty(false)
                  ->setMultiOptions(PaymentsMethods::getList(true));                  
        
        $this->addElement('submit', 'save', array(
            'required' => false,
            'label'    => 'Save',
            'decorators' => array('Composite'),
            'class'    => 'button'
        ));
        
        $this->addElement('hidden', 'bank_id');
    }
}