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
            'decorators'  => array('Bootstrap'),
            'label'       => $translate->_('Subject'),
            'class'       => 'form-control'
        ));
        
        $this->addElement('text', 'sendat', array(
            'filters'     => array('StringTrim'),
            'required'    => false,
            'decorators'  => array('Bootstrap'),
            'label'       => $translate->_('Send At'),
            'class'       => 'little-input date'
        ));
        
        $this->addElement('text', 'sent', array(
            'filters'     => array('StringTrim'),
            'decorators'  => array('Bootstrap'),
            'label'       => $translate->_('Sent'),
            'class'       => 'little-input date'
        ));
        
        $this->addElement('textarea', 'message', array(
            'filters'     => array('StringTrim'),
            'decorators'  => array('Bootstrap'),
        	'required'    => true,
            'label'       => $translate->_('Message'),
            'class'       => 'col-lg-12 form-control wysiwyg'
        ));
        
        $this->addElement('select', 'sendagain', array(
	        'label' => $translate->_('Send it again'),
	        'decorators' => array('Bootstrap'),
	        'class'      => 'form-control'
        ));
        
        $this->getElement('sendagain')
                  ->setAllowEmpty(true)
                  ->setMultiOptions(array('0'=> $translate->_('No, only once'), '1'=> $translate->_('Yes, send again')));        
        
        $this->addElement('hidden', 'news_id');

    }
    
}