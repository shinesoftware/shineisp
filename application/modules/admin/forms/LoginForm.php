<?php
class Admin_Form_LoginForm extends Zend_Form
{
    public function init()
    {
    	// Set the custom decorator
        $this->addElementPrefixPath('Shineisp_Decorator', 'Shineisp/Decorator/', 'decorator');
        
        $this->addElement('text', 'email', array(
            'filters'    => array('StringTrim', 'StringToLower'),
            'validators' => array(
                'EmailAddress',
            ),
            'decorators' => array('Composite'),
            'required'   => true,
            'description'      => 'Write your own email',
            'label'      => 'Email',
            'class'      => 'text-input large-input'
        ));
        
        $this->addElement('password', 'password', array(
            'filters'    => array('StringTrim'),
            'validators' => array(
                'Alnum',
                array('StringLength', false, array(6, 20)),
            ),
            'decorators' => array('Composite'),
            'description'      => 'Write your own password',
            'required'   => true,
            'label'      => 'Password',
            'class'      => 'text-input large-input'
        ));

        $this->addElement('checkbox', 'rememberme', array(
            'label'    => 'Remember Me',
            'decorators' => array('Composite')
        ));

        $this->addElement('submit', 'login', array(
            'label'    => 'Login',
            'decorators' => array('Composite'),
        	'class'      => 'button'
        ));
        
    }
}