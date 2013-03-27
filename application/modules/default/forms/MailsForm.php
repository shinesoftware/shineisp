<?php
class Default_Form_MailsForm extends Zend_Form
{
    
    public function init()
    {
    	// Set the custom decorator
        $this->addElementPrefixPath('Shineisp_Decorator', 'Shineisp/Decorator/', 'decorator');
        
        $this->addElement('text', 'name', array(
            'filters'     => array('StringTrim'),
            'required'    => true,
        	'maxLength'	  => 50,
            'decorators'  => array('Simple'),
            'label'       => 'Account',
            'class'       => 'text-input large-input'
        ));
        
        $this->addElement('password', 'password', array(
            'required'    => true,
            'decorators'  => array('Composite'),
            'label'       => 'Password',
        	'maxLength'	  => 20,
            'description' => 'Write here the password. Choose a secure password. [min 6 chars - max 20 chars]',
            'class'       => 'text-input medium-input password-strength',
        	'validators' => array(array('regex', false, '/^[a-zA-Z0-9\-\_\.\%\!\$]{6,20}$/')),
            ),
        ));
        
        $this->addElement('checkbox', 'active', array(
            'decorators'  => array('Composite'),
            'label'       => 'Active',
            'class'       => 'text-input medium-input'
        ));
        
        $this->addElement('select', 'domain_id', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => 'Domain',
            'decorators' => array('Simple'),
            'class'      => 'text-input large-input'
        ));
        
        
        $this->addElement('textarea', 'autoresponder_text', array(
            'filters'     => array('StringTrim'),
            'decorators'  => array('Composite'),
            'label'       => 'Message',
            'description' => 'Write here your own autoresponder message',
            'class'       => 'textarea'
        ));      

        $this->addElement('checkbox', 'autoresponder_active', array(
            'decorators'  => array('Composite'),
            'label'       => 'Active',
            'class'       => 'text-input medium-input'
        ));  

        $this->addElement('text', 'autoresponder_start', array(
            'filters'     => array('StringTrim'),
            'decorators'  => array('Composite'),
            'label'       => 'Start on',
            'class'       => 'text-input small-input'
        ));        

        $this->addElement('text', 'autoresponder_end', array(
            'filters'     => array('StringTrim'),
            'decorators'  => array('Composite'),
            'label'       => 'End by',
            'class'       => 'text-input small-input'
        ));        
        
        
        $this->addElement('checkbox', 'disableimap', array(
            'decorators'  => array('Composite'),
            'label'       => 'Disable IMAP',
            'class'       => 'text-input medium-input'
        ));          
        
        $this->addElement('checkbox', 'disablepop3', array(
            'decorators'  => array('Composite'),
            'label'       => 'Disable POP3',
            'class'       => 'text-input medium-input'
        ));          
        
        $this->addElement('checkbox', 'disabledeliver', array(
            'decorators'  => array('Composite'),
            'label'       => 'Disable Deliver',
            'class'       => 'text-input medium-input'
        ));          
        
        $this->addElement('checkbox', 'disablesmtp', array(
            'decorators'  => array('Composite'),
            'label'       => 'Disable SMTP',
            'class'       => 'text-input medium-input'
        ));          
        
        $this->addElement('submit', 'submit', array(
            'required' => false,
            'label'    => 'Save',
            'decorators' => array('Composite'),
            'class'    => 'button'
        ));
        
        $id = $this->addElement('hidden', 'mail_id');

    }
    
}