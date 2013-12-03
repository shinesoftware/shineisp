<?php
class Default_Form_SignupForm extends Zend_Form
{   
    public function init()
    {
        // Set the custom decorator
        $this->addElementPrefixPath('Shineisp_Decorator', 'Shineisp/Decorator/', 'decorator');
        $this->addElementPrefixPath('Default', APPLICATION_PATH.'/modules/default/forms/validate/','validate');
        $translate = Shineisp_Registry::get('Zend_Translate');
        
        $this->addElement('text', 'company', array(
        		'filters'    => array('StringTrim'),
        		'label'      => $translate->_('Company Name'),
        		'decorators' => array('Bootstrap'),
        		'description'      => $translate->_('Write here your company name.'),
        		'class'      => 'form-control'
        ));
        
        $this->addElement('text', 'firstname', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => $translate->_('First name'),
            'description' => $translate->_('Write here your first name.'),
            'decorators' => array('Bootstrap'),
            'class'      => 'form-control'
        ));
        
        $this->addElement('text', 'lastname', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => $translate->_('Last name'),
            'description'      => $translate->_('Write here your lastname.'),
            'decorators' => array('Bootstrap'),
            'class'      => 'form-control'
        ));
        
        $email = $this->createElement('text', 'email', array(
            'filters'    => array('StringTrim', 'StringToLower'),
            'decorators' => array('Bootstrap'),
            'validators' => array(
                'EmailAddress'
            ),
            'required'       => true,
            'label'          => $translate->_('Email'),
            'description'    => $translate->_('Write here your email'),
            'class'          => 'form-control'
        ));
        
        $email->addValidator('UniqueEmail',false, array(new Customers()));
        $this->addElement($email);
         
        // Password manager
        $passwordConfirmation = new Shineisp_Validate_PasswordConfirmation();
        
        $password = $this->addElement('password', 'password', array(
        		'filters' => array('StringTrim'),
        		'decorators'  => array('Bootstrap'),
        		'description'       => $translate->_('Write here your password. (min.8 chars - max.20 chars)'),
        		'validators' => array(
        				$passwordConfirmation,
        				array('regex', false, '/^[a-zA-Z0-9\-\_\.\%\!\$]{6,20}$/')
        		),
        		'class'       => 'form-control',
        		'required' => true,
        		'label' => $translate->_('Password'),
        ));
        
        $password_confirm = $this->addElement('password', 'password_confirm', array(
        		'filters' => array('StringTrim'),
        		'decorators'  => array('Bootstrap'),
        		'description'       => $translate->_('Please repeat the password'),
        		'validators' => array(
        				$passwordConfirmation,
        				array('regex', false, '/^[a-zA-Z0-9\-\_\.\%\!\$]{6,20}$/')
        		),
        		'class'       => 'form-control',
        		'required' => true,
        		'label' => 'Confirm Password',
        ));
        
        #$vatValidator = new Shineisp_Validate_Vat();
        $this->addElement('text', 'vat', array(
        		'filters'    => array('StringTrim'),
        		'label'      => $translate->_('VAT Number'),
        		'decorators' => array('Bootstrap'),
        		'class'      => 'form-control',
        		'description'      => $translate->_('Write here the VAT code. Eg: IT123456789')
        ));
        #$this->getElement('vat')->addValidator($vatValidator);        
        
        $this->addElement('select', 'company_type_id', array(
        		'label' => 'Company Type',
        		'decorators' => array('Bootstrap'),
        		'description'      => $translate->_('Select the company type'),
        		'class'      => 'form-control'
        ));
        
        $this->getElement('company_type_id')
        ->setAllowEmpty(false)
        ->setMultiOptions(CompanyTypes::getList(true));
        
        $this->addElement('select', 'legalform', array(
        		'label' => $translate->_('Legal form'),
        		'required'   => true,
        		'decorators' => array('Bootstrap'),
        		'description'      => $translate->_('Select the type of company.'),
        		'class'      => 'form-control'
        ));
        
        $this->getElement('legalform')
        ->setAllowEmpty(false)
        ->setMultiOptions(Legalforms::getList(true))
        ->addValidator( new Shineisp_Validate_Customer( ) );        
        
        $fiscalcodeValidator = new Shineisp_Validate_Fiscalcode();
        $this->addElement('text', 'taxpayernumber', array(
        		'filters'    => array('StringTrim'),
        		'label'      => $translate->_('Tax payer number'),
        		'decorators' => array('Bootstrap'),
        		'class'      => 'form-control',
        		'description'      => $translate->_('Write the tax payer number.')
        ));
        
        # TODO: check both fiscalcode for generic person or giuridic person. (XYZYXZ88D22C123X and 1234567890)
        #$this->getElement('taxpayernumber')->addValidator($fiscalcodeValidator);  
                
        $this->addElement('submit', 'signup', array(
            'label'      => $translate->_('Create my account'),
            'decorators' => array('Bootstrap'),
            'class'    => 'btn btn-success btn-lg'
        ));
        
        $privKey = Settings::findbyParam('recaptcha_private_key');
        $pubKey = Settings::findbyParam('recaptcha_public_key');
        
        if(!empty($pubKey) && !empty($privKey)){
	         
	        $recaptcha = new Zend_Service_ReCaptcha($pubKey, $privKey);
	        $captcha = new Zend_Form_Element_Captcha('captcha',
	            array(
	                'label' => $translate->_('Captcha Check'),
	                'description' => $translate->_('Type the characters you see in the picture below.'),
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