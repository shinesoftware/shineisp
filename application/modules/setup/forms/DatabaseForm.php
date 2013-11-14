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
            'decorators' => array('Bootstrap'),
            'class'      => 'form-control'
        ));
        
        $this->addElement('text', 'database', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => 'Database',
            'description' => 'Write here the database name.',
            'decorators' => array('Bootstrap'),
            'class'      => 'form-control'
        ));
        
        $this->addElement('text', 'username', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => 'Username',
            'description'      => 'Write here the database username.',
            'decorators' => array('Bootstrap'),
            'class'      => 'form-control'
        ));

        $this->addElement('password', 'password', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => 'Password',
            'description'      => 'Write here the database password.',
            'decorators' => array('Bootstrap'),
            'class'      => 'form-control'
        ));        

        $this->addElement('submit', 'submit', array(
            'label'    => 'Continue',
            'decorators' => array('Bootstrap'),
            'class'    => 'btn btn-primary btn-lg'
        ));
        
        
    }
}