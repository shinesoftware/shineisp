<?php
class Default_Form_LoginForm extends Zend_Form
{
    public function init()
    {
    	// Set the custom decorator
        $this->addElementPrefixPath('Shineisp_Decorator', 'Shineisp/Decorator/', 'decorator');
        $translate = Shineisp_Registry::get('Zend_Translate');
        
        $this->addElement('text', 'email', array(
            'filters'    => array('StringTrim', 'StringToLower'),
            'validators' => array(
                'EmailAddress',
            ),
            'decorators' => array('Bootstrap'),
            'required'   => true,
            'description'      => $translate->_('Write your own email'),
            'label'      => $translate->_('Email'),
            'class'      => 'form-control large-input'
        ));
        
        $this->addElement('password', 'password', array(
            'filters'    => array('StringTrim'),
            'validators' => array(
                array('regex', false, '/^[a-zA-Z0-9\-\_\.\%\!\$]{6,20}$/')
            ),
            'decorators' => array('Bootstrap'),
            'description'      => $translate->_('Write your own password'),
            'required'   => true,
            'label'      => $translate->_('Password'),
            'class'      => 'form-control large-input'
        ));

        $this->addElement('submit', 'login', array(
            'label'      => $translate->_('Login'),
            'class'      => 'btn btn-primary'
        ));
        
    }
}