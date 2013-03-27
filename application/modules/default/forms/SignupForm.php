<?php
class Default_Form_SignupForm extends Zend_Form
{   
    public function init()
    {
        // Set the custom decorator
        $this->addElementPrefixPath('Shineisp_Decorator', 'Shineisp/Decorator/', 'decorator');
        $this->addElementPrefixPath('Default', APPLICATION_PATH.'/modules/default/forms/validate/','validate');
        
        $this->addElement('text', 'company', array(
        		'filters'    => array('StringTrim'),
        		'label'      => 'Company Name',
        		'decorators' => array('Composite'),
        		'description'      => 'Write here your company name.',
        		'class'      => 'text-input large-input'
        ));
        
        $this->addElement('text', 'firstname', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => 'Firstname',
            'description' => 'Write here your firstname.',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input'
        ));
        
        $this->addElement('text', 'lastname', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => 'Lastname',
            'description'      => 'Write here your lastname.',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input'
        ));
        
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
        
        $email->addValidator('UniqueEmail',false, array(new Customers()));
        $this->addElement($email);
         
        // Password manager
        $passwordConfirmation = new Shineisp_Validate_PasswordConfirmation();
        
        $password = $this->addElement('password', 'password', array(
        		'filters' => array('StringTrim'),
        		'decorators'  => array('Composite'),
        		'description'       => 'Write here your password. (min.8 chars - max.20 chars)',
        		'validators' => array(
        				$passwordConfirmation,
        				array('regex', false, '/^[a-zA-Z0-9\-\_\.\%\!\$]{6,20}$/')
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
        				array('regex', false, '/^[a-zA-Z0-9\-\_\.\%\!\$]{6,20}$/')
        		),
        		'class'       => 'text-input large-input',
        		'required' => true,
        		'label' => 'Confirm Password',
        ));
        
        #$vatValidator = new Shineisp_Validate_Vat();
        $this->addElement('text', 'vat', array(
        		'filters'    => array('StringTrim'),
        		'label'      => 'VAT Number',
        		'decorators' => array('Composite'),
        		'class'      => 'text-input large-input',
        		'description'      => 'Write here the VAT code. Eg: IT123456789'
        ));
        #$this->getElement('vat')->addValidator($vatValidator);        
        
        $this->addElement('select', 'company_type_id', array(
        		'label' => 'Company Type',
        		'decorators' => array('Composite'),
        		'description'      => 'Select the company type',
        		'class'      => 'text-input large-input'
        ));
        
        $this->getElement('company_type_id')
        ->setAllowEmpty(false)
        ->setMultiOptions(CompanyTypes::getList(true));
        
        $this->addElement('select', 'legalform', array(
        		'label' => 'Legalform',
        		'required'   => true,
        		'decorators' => array('Composite'),
        		'description'      => 'Select the type of company.',
        		'class'      => 'text-input large-input'
        ));
        
        $this->getElement('legalform')
        ->setAllowEmpty(false)
        ->setMultiOptions(Legalforms::getList(true))
        ->addValidator( new Shineisp_Validate_Customer( ) );        
        
        $fiscalcodeValidator = new Shineisp_Validate_Fiscalcode();
        $this->addElement('text', 'taxpayernumber', array(
        		'filters'    => array('StringTrim'),
        		'required'   => true,
        		'label'      => 'Tax payer number',
        		'decorators' => array('Composite'),
        		'class'      => 'text-input large-input',
        		'description'      => 'Write the tax payer number.'
        ));
        
        # TODO: check both fiscalcode for generic person or giuridic person. (XYZYXZ88D22C123X and 1234567890)
        #$this->getElement('taxpayernumber')->addValidator($fiscalcodeValidator);  
                
        $this->addElement('submit', 'signup', array(
            'required' => false,
            'label'    => 'Create my account',
            'decorators' => array('Composite'),
            'class'    => 'blue-button'
        ));
        
        $privKey = Settings::findbyParam('recaptcha_private_key');
        $pubKey = Settings::findbyParam('recaptcha_public_key');
        
        if(!empty($pubKey) && !empty($privKey)){
	         
	        $recaptcha = new Zend_Service_ReCaptcha($pubKey, $privKey);
	        $captcha = new Zend_Form_Element_Captcha('captcha',
	            array(
	                'label' => 'Captcha Check',
	                'description' => 'Type the characters you see in the picture below.',
	                'captcha' =>  'ReCaptcha',
	                'captchaOptions'        => array(
	                    'captcha'   => 'ReCaptcha',
	                    'service'   => $recaptcha
	                )
	            )
	        );
	
	    	$this->addElement($captcha);
        }        
    }
}