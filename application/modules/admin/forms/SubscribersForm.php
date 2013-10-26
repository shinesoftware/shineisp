<?php
class Admin_Form_SubscribersForm extends Zend_Form
{
    
    public function init()
    {
        // Set the custom decorator
        $this->addElementPrefixPath('Shineisp_Decorator', 'Shineisp/Decorator/', 'decorator');
        $translate = Shineisp_Registry::get('Zend_Translate');
        
        $this->addElement('text', 'email', array(
            'filters'     => array('StringTrim'),
            'required'    => true,
            'decorators'  => array('Bootstrap'),
            'label'       => $translate->_('Email'),
            'class'       => 'input-large'
        ));
        
        $this->addElement('text', 'subscriptiondate', array(
            'filters'     => array('StringTrim'),
            'required'    => false,
            'decorators'  => array('Bootstrap'),
            'label'       => $translate->_('Subscribed At'),
            'class'       => 'little-input date'
        ));
        
        $this->addElement('hidden', 'subscriber_id');

    }
    
}