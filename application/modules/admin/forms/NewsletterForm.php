<?php
class Admin_Form_NewsletterForm extends Zend_Form
{
    
    public function init()
    {
        // Set the custom decorator
        $this->addElementPrefixPath('Shineisp_Decorator', 'Shineisp/Decorator/', 'decorator');
        $translate = Shineisp_Registry::get('Zend_Translate');
        
        $this->addElement('text', 'subject', array(
            'filters'     => array('StringTrim'),
            'required'    => true,
            'decorators'  => array('Composite'),
            'label'       => $translate->_('Subject'),
            'class'       => 'text-input large-input'
        ));
        
        $this->addElement('text', 'sendat', array(
            'filters'     => array('StringTrim'),
            'required'    => false,
            'decorators'  => array('Composite'),
            'label'       => $translate->_('Send At'),
            'class'       => 'text-input little-input date'
        ));
        
        $this->addElement('text', 'sent', array(
            'filters'     => array('StringTrim'),
            'decorators'  => array('Composite'),
            'label'       => $translate->_('Sent'),
            'class'       => 'text-input little-input date'
        ));
        
        $this->addElement('textarea', 'message', array(
            'filters'     => array('StringTrim'),
            'decorators'  => array('Composite'),
        	'required'    => true,
            'label'       => $translate->_('Message'),
            'class'       => 'textarea wysiwyg'
        ));
        
        $this->addElement('select', 'sendagain', array(
	        'label' => $translate->_('Send it again'),
	        'decorators' => array('Composite'),
	        'class'      => 'text-input large-input'
        ));
        
        $this->getElement('sendagain')
                  ->setAllowEmpty(true)
                  ->setMultiOptions(array('0'=> $translate->_('No, only once'), '1'=> $translate->_('Yes, send again')));        
        
        $this->addElement('hidden', 'news_id');

    }
    
}