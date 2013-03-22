<?php
class Admin_Form_RegistrarsForm extends Zend_Form
{   
    public function init()
    {
        // Set the custom decorator
    	$this->addElementPrefixPath('Shineisp_Decorator', 'Shineisp/Decorator/', 'decorator');
    	
        $this->addElement('text', 'name', array(
            'filters'    => array('StringTrim'),
            'label'      => 'Registrant Name',
            'required'   => true,
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input'
        ));

        $this->addElement('text', 'wsdl', array(
            'filters'    => array('StringTrim'),
            'label'      => 'WSDL',
            'required'   => true,
            'decorators' => array('Composite'),
            'description'      => 'Type here the URL of the WSDL Service',
            'class'      => 'text-input large-input'
        ));  

        $this->addElement('text', 'class', array(
            'filters'    => array('StringTrim'),
            'label'      => 'ShineISP Class',
            'required'   => true,
            'value'   => 'Shineisp_Api_Registrars_[yourclassname]',
            'decorators' => array('Composite'),
            'description'      => 'Name of the class saved within the Library/Shineisp/Api/Registrars',
            'class'      => 'text-input large-input'
        ));          

        $this->addElement('text', 'username', array(
            'filters'    => array('StringTrim'),
            'label'      => 'Username',
            'required'   => true,
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input'
        )); 

        $this->addElement('text', 'password', array(
            'filters'    => array('StringTrim'),
            'label'      => 'Password',
            'required'   => true,
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input'
        ));   

        $this->addElement('text', 'credit', array(
            'filters'    => array('StringTrim'),
            'label'      => 'Credit Available',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input'
        ));   

        $this->addElement('select', 'testmode', array(
            'filters'    => array('StringTrim'),
            'label'      => 'Test Mode',
            'decorators' => array('Composite'),
            'description'      => 'The test mode helps you to test the Registrar connection',
            'class'      => 'text-input large-input'
        ));  
        
        $this->getElement('testmode')
				        ->setAllowEmpty(false)
				        ->setRegisterInArrayValidator(false)
				        ->setMultiOptions(array('0'=>'No', '1' =>'Yes'));

        $this->addElement('select', 'active', array(
            'filters'    => array('StringTrim'),
            'label'      => 'Active',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input'
        ));          
        
        $this->getElement('active')
				        ->setAllowEmpty(false)
				        ->setRegisterInArrayValidator(false)
				        ->setMultiOptions(array('0'=>'No', '1' =>'Yes'));

        $this->addElement('submit', 'save', array(
            'required' => false,
            'label'    => 'Save',
            'decorators' => array('Composite'),
            'class'    => 'button'
        ));
        
        $this->addElement('reset', 'reset', array(
            'required' => false,
            'label'    => 'reset',
            'decorators' => array('Composite'),
            'class'    => 'button'
        ));
        
        $this->addElement('hidden', 'registrars_id');
    }
}