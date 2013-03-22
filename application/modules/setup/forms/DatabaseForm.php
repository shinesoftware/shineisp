<?php
class Setup_Form_DatabaseForm extends Zend_Form
{   
	
    public function init()
    {
        $this->addElementPrefixPath('Shineisp_Decorator', 'Shineisp/Decorator/', 'decorator');
        
        $this->addElement('text', 'hostname', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => 'Hostname',
            'description' => 'Write here the hostname eg. localhost. You can specify server port, ex: localhost:3307',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input'
        ));
        
        $this->addElement('text', 'database', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => 'Database',
            'description' => 'Write here the database name.',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input'
        ));
        
        $this->addElement('text', 'username', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => 'Username',
            'description'      => 'Write here the database username.',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input'
        ));

        $this->addElement('password', 'password', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => 'Password',
            'description'      => 'Write here the database password.',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input'
        ));        

        $this->addElement('submit', 'submit', array(
            'label'    => 'Continue',
            'decorators' => array('Composite'),
            'class'    => 'blue-button'
        ));
        
        
    }
}