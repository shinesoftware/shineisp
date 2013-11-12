<?php
class Setup_Form_PreferencesForm extends Zend_Form
{   
	
    public function init()
    {
        $this->addElementPrefixPath('Shineisp_Decorator', 'Shineisp/Decorator/', 'decorator');
        
        $email = $this->createElement('text', 'email', array(
        		'filters'    => array('StringTrim', 'StringToLower'),
        		'decorators' => array('Bootstrap'),
        		'validators' => array(
        				'EmailAddress'
        		),
        		'required'   => true,
        		'label'      => 'Email',
        		'description'      => 'Write here your email',
        		'class'      => 'form-control'
        ));
        
        
        $this->addElement('select', 'sampledata', array(
        		'decorators'  => array('Bootstrap'),
        		'label'       => 'Install Sample Data',
        		'class'       => 'form-control',
        		'multioptions' => array(  1=> 'YES, please install the sample data', 0=>'NO, thanks I don\'t need them')
        ));
        
        $this->addElement($email);
        
        // Password manager
        $passwordConfirmation = new Shineisp_Validate_PasswordConfirmation();
        
        $password = $this->addElement('password', 'password', array(
        		'filters' => array('StringTrim'),
        		'decorators'  => array('Bootstrap'),
        		'description'       => 'Write here your password. (min.8 chars - max.20 chars)',
        		'validators' => array(
        				$passwordConfirmation,
        				array('StringLength', false, array(8, 100)),
        		),
        		'class'       => 'form-control',
        		'required' => true,
        		'label' => 'Password',
        ));
        
        $password_confirm = $this->addElement('password', 'password_confirm', array(
        		'filters' => array('StringTrim'),
        		'decorators'  => array('Bootstrap'),
        		'description'       => 'Please repeat the password',
        		'validators' => array(
        				$passwordConfirmation,
        				array('StringLength', false, array(8, 100)),
        		),
        		'class'       => 'form-control',
        		'required' => true,
        		'label' => 'Confirm Password',
        ));
        

        $this->addElement('text', 'company', array(
        		'filters'    => array('StringTrim'),
        		'decorators' => array('Bootstrap'),
        		'label'      => 'Company', 
        		'required' => true,
        		'class'       => 'form-control'
        ));

        $this->addElement('text', 'vatnumber', array(
        		'filters'    => array('StringTrim'),
        		'decorators' => array('Bootstrap'),
        		'label'      => 'VAT Number',
        		'required' => true,
        		'class'       => 'form-control'
        ));

        $this->addElement('text', 'firstname', array(
        		'filters'    => array('StringTrim'),
        		'decorators' => array('Bootstrap'),
        		'label'      => 'First Name',
        		'required' => true,
        		'class'       => 'form-control'
        ));

        $this->addElement('text', 'lastname', array(
        		'filters'    => array('StringTrim'),
        		'decorators' => array('Bootstrap'),
        		'label'      => 'Last Name',
        		'required' => true,
        		'class'       => 'form-control'
        ));

        $this->addElement('submit', 'submit', array(
            'label'    => 'Continue',
            'decorators' => array('Bootstrap'),
            'class'    => 'btn btn-primary btn-lg'
        ));
        
        
    }
}