<?php
class Admin_Form_BanksForm extends Zend_Form
{   
    public function init()
    {
        // Set the custom decorator
    	$this->addElementPrefixPath('Shineisp_Decorator', 'Shineisp/Decorator/', 'decorator');
    	$translate = Shineisp_Registry::get('Zend_Translate');
    	
        $this->addElement('text', 'name', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => $translate->_('Name'),
            'decorators' => array('Composite'),
            'class'      => 'input-large'
        ));
        
        $this->addElement('text', 'account', array(
            'filters'    => array('StringTrim'),
            'label'      => $translate->_('Account'),
            'decorators' => array('Composite'),
            'class'      => 'input-large'
        ));
        
        $this->addElement('textarea', 'description', array(
            'filters'    => array('StringTrim'),
            'label'      => $translate->_('Description'),
            'decorators' => array('Composite'),
            'class'      => 'textarea wysiwyg'
        ));
        
        $this->addElement('text', 'url_test', array(
            'label'      => $translate->_('URL Test'),
            'decorators' => array('Composite'),
            'class'      => 'input-large'
        ));
        
        $this->addElement('text', 'url_official', array(
            'label'      => $translate->_('URL Official'),
            'decorators' => array('Composite'),
            'class'      => 'input-large'
        ));        
        
        $this->addElement('text', 'classname', array(
            'label'      => $translate->_('Classname'),
            'decorators' => array('Composite'),
            'class'      => 'input-large'
        ));        
        
        $this->addElement('checkbox', 'enabled', array(
            'label'      => $translate->_('Enabled'),
            'decorators' => array('Composite'),
            'class'      => 'checkbox'
        ));        
        
        $this->addElement('checkbox', 'test_mode', array(
            'label'      => $translate->_('Test Mode'),
            'decorators' => array('Composite'),
            'class'      => 'checkbox'
        ));        
        
        $this->addElement('select', 'method_id', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'id'         => 'paymentmethods',
            'label'      => $translate->_('Payment methods'),
            'decorators' => array('Composite'),
            'class'      => 'input-large'
        ));
        
        $this->getElement('method_id')
                  ->setAllowEmpty(false)
                  ->setMultiOptions(PaymentsMethods::getList(true));                  
        
        $this->addElement('submit', 'save', array(
            'required' => false,
            'label'    => $translate->_('Save'),
            'decorators' => array('Composite'),
            'class'    => 'btn'
        ));
        
        $this->addElement('hidden', 'bank_id');
    }
}