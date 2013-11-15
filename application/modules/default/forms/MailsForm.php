<?php
class Default_Form_MailsForm extends Zend_Form
{
    
    public function init()
    {
    	// Set the custom decorator
        $this->addElementPrefixPath('Shineisp_Decorator', 'Shineisp/Decorator/', 'decorator');
        $translate = Shineisp_Registry::get('Zend_Translate');
        
        $this->addElement('text', 'name', array(
            'filters'     => array('StringTrim'),
            'required'    => true,
        	'maxLength'	  => 50,
            'decorators'  => array('Simple'),
            'label'      => $translate->_('Account'),
            'class'       => 'form-control large-input'
        ));
        
        $this->addElement('password', 'password', array(
            'required'    => true,
            'decorators'  => array('Bootstrap'),
            'label'      => $translate->_('Password'),
        	'maxLength'	  => 20,
            'description' => $translate->_('Write here the password. Choose a secure password. [min 6 chars - max 20 chars]'),
            'class'       => 'form-control medium-input password-strength',
        	'validators' => array(array('regex', false, '/^[a-zA-Z0-9\-\_\.\%\!\$]{6,20}$/'))
            )
        );
        
        $this->addElement('checkbox', 'active', array(
            'decorators'  => array('Bootstrap'),
            'label'      => $translate->_('Active'),
            'class'       => 'form-control medium-input'
        ));
        
        $this->addElement('select', 'domain_id', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => $translate->_('Domain'),
            'decorators' => array('Simple'),
            'class'      => 'form-control large-input'
        ));
        
        
        $this->addElement('textarea', 'autoresponder_text', array(
            'filters'     => array('StringTrim'),
            'decorators'  => array('Bootstrap'),
            'label'      => $translate->_('Message'),
            'description' => $translate->_('Write here your own autoresponder message'),
            'class'       => 'textarea'
        ));      

        $this->addElement('checkbox', 'autoresponder_active', array(
            'decorators'  => array('Bootstrap'),
            'label'      => $translate->_('Active'),
            'class'       => 'form-control medium-input'
        ));  

        $this->addElement('text', 'autoresponder_start', array(
            'filters'     => array('StringTrim'),
            'decorators'  => array('Bootstrap'),
            'label'      => $translate->_('Start on'),
            'class'       => 'form-control '
        ));        

        $this->addElement('text', 'autoresponder_end', array(
            'filters'     => array('StringTrim'),
            'decorators'  => array('Bootstrap'),
            'label'      => $translate->_('End on'),
            'class'       => 'form-control '
        ));        
        
        
        $this->addElement('checkbox', 'disableimap', array(
            'decorators'  => array('Bootstrap'),
            'label'      => $translate->_('Disable IMAP'),
            'class'       => 'form-control medium-input'
        ));          
        
        $this->addElement('checkbox', 'disablepop3', array(
            'decorators'  => array('Bootstrap'),
            'label'      => $translate->_('Disable POP3'),
            'class'       => 'form-control medium-input'
        ));          
        
        $this->addElement('checkbox', 'disabledeliver', array(
            'decorators'  => array('Bootstrap'),
            'label'      => $translate->_('Disable Deliver'),
            'class'       => 'form-control medium-input'
        ));          
        
        $this->addElement('checkbox', 'disablesmtp', array(
            'decorators'  => array('Bootstrap'),
            'label'      => $translate->_('Disable SMTP'),
            'class'       => 'form-control medium-input'
        ));          
        
        $this->addElement('submit', 'submit', array(
            'required' => false,
            'label'      => $translate->_('Save'),
            'decorators' => array('Bootstrap'),
            'class'    => 'btn btn-primary'
        ));
        
        $id = $this->addElement('hidden', 'mail_id');

    }
    
}