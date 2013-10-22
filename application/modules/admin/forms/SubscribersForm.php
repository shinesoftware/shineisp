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
            'decorators'  => array('Composite'),
            'label'       => $translate->_('Email'),
            'class'       => 'text-input large-input'
        ));
        
        $this->addElement('text', 'subscriptiondate', array(
            'filters'     => array('StringTrim'),
            'required'    => false,
            'decorators'  => array('Composite'),
            'label'       => $translate->_('Subscribed At'),
            'class'       => 'text-input little-input date'
        ));
        
        $this->addElement('hidden', 'subscriber_id');

    }
    
}