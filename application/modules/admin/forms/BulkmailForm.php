<?php
class Admin_Form_BulkmailForm extends Zend_Form
{
    
    public function init()
    {
        // Set the custom decorator
        $this->addElementPrefixPath('Shineisp_Decorator', 'Shineisp/Decorator/', 'decorator');
        $translate = Shineisp_Registry::get('Zend_Translate');
        
        $this->addElement('text', 'subject', array(
            'filters'     => array('StringTrim'),
            'required'    => false,
            'decorators'  => array('Bootstrap'),
            'label'       => $translate->_('Subject'),
            'class'       => 'medium-input'
        ));
        
        $this->addElement('textarea', 'body', array(
            'filters'     => array('StringTrim'),
            'required'    => true,
            'label'       => $translate->_('Body'),
            'description' => 'Write here the email message to send to all your customers.',
            'class'       => 'span12 wysiwyg'
        ));
        
        $this->addElement('submit', 'send', array(
            'required' => false,
            'label'    => $translate->_('Save and Send Message'),
            'decorators' => array('Bootstrap'),
            'class'    => 'btn'
        ));
                
        $this->addElement('hidden', 'mail_id');

    }
    
}