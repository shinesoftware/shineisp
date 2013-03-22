<?php
class Setup_Form_PreferencesForm extends Zend_Form
{   
	
    public function init()
    {
        $this->addElementPrefixPath('Shineisp_Decorator', 'Shineisp/Decorator/', 'decorator');
        
        $email = $this->createElement('text', 'email', array(
        		'filters'    => array('StringTrim', 'StringToLower'),
        		'decorators' => array('Composite'),
        		'validators' => array(
        				'EmailAddress'
        		),
        		'required'   => true,
        		'label'      => 'Email',
        		'description'      => 'Write here your email',
        		'class'      => 'text-input large-input'
        ));
        
        
        $this->addElement('select', 'sampledata', array(
        		'decorators'  => array('Composite'),
        		'label'       => 'Install Sample Data',
        		'class'       => 'text-input large-input',
        		'multioptions' => array(  1=> 'YES, please install the sample data', 0=>'NO, thanks I don\'t need them')
        ));
        
        $this->addElement($email);
        
        // Password manager
        $passwordConfirmation = new Shineisp_Validate_PasswordConfirmation();
        
        $password = $this->addElement('password', 'password', array(
        		'filters' => array('StringTrim'),
        		'decorators'  => array('Composite'),
        		'description'       => 'Write here your password. (min.8 chars - max.20 chars)',
        		'validators' => array(
        				$passwordConfirmation,
        				array('StringLength', false, array(8, 100)),
        		),
        		'class'       => 'text-input large-input',
        		'required' => true,
        		'label' => 'Password',
        ));
        
        $password_confirm = $this->addElement('password', 'password_confirm', array(
        		'filters' => array('StringTrim'),
        		'decorators'  => array('Composite'),
        		'description'       => 'Please repeat the password',
        		'validators' => array(
        				$passwordConfirmation,
        				array('StringLength', false, array(8, 100)),
        		),
        		'class'       => 'text-input large-input',
        		'required' => true,
        		'label' => 'Confirm Password',
        ));
        

        $this->addElement('text', 'company', array(
        		'filters'    => array('StringTrim'),
        		'decorators' => array('Composite'),
        		'label'      => 'Company', 
        		'required' => true,
        		'class'       => 'text-input large-input'
        ));

        $this->addElement('text', 'vatnumber', array(
        		'filters'    => array('StringTrim'),
        		'decorators' => array('Composite'),
        		'label'      => 'VAT Number',
        		'required' => true,
        		'class'       => 'text-input large-input'
        ));

        $this->addElement('text', 'firstname', array(
        		'filters'    => array('StringTrim'),
        		'decorators' => array('Composite'),
        		'label'      => 'Firstname',
        		'required' => true,
        		'class'       => 'text-input large-input'
        ));

        $this->addElement('text', 'lastname', array(
        		'filters'    => array('StringTrim'),
        		'decorators' => array('Composite'),
        		'label'      => 'Lastname',
        		'required' => true,
        		'class'       => 'text-input large-input'
        ));

        $this->addElement('submit', 'submit', array(
            'label'    => 'Continue',
            'decorators' => array('Composite'),
            'class'    => 'blue-button'
        ));
        
        
    }
}