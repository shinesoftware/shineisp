<?php
class Default_Form_ContactsForm extends Zend_Form
{   
	
    public function init()
    {
        // Set the custom decorator
        $this->addElementPrefixPath('Shineisp_Decorator', 'Shineisp/Decorator/', 'decorator');
        $this->addElementPrefixPath('Default', APPLICATION_PATH.'/modules/default/forms/validate/','validate');
        
        $this->addElement('text', 'fullname', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => 'Fullname',
            'title' => 'Write here your firstname and lastname.',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input'
        ));
        
        $this->addElement('text', 'company', array(
            'filters'    => array('StringTrim'),
            'label'      => 'Company',
            'title'      => 'Write here your company name.',
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
            'title'      => 'Write here your email',
            'class'      => 'text-input large-input'
        ));
        
        $this->addElement($email);
        
        $status = $this->addElement('select', 'subject', array(
        'label' => 'Subject',
        'decorators' => array('Composite'),
        'class'      => 'text-input large-input'
        ));
        
        $status = $this->getElement('subject')
                  ->setAllowEmpty(false)
                  ->setMultiOptions(array('Sales Question' => 'Sales Question','Billing Question' => 'Billing Question', 'Partnership Inquiry' => 'Partnership Inquiry', 'Website Feedback' => 'Website Feedback'));          
        
        $this->addElement('textarea', 'message', array(
            'filters'    => array('StringTrim'),
            'decorators' => array('Composite'),
            'required'   => true,
            'rows'		=> 5,
            'description'      => 'Write here your message.',
            'label'      => 'Message',
            'class'      => 'textarea'
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
	    	
        }else{
        	
        	$captcha = new Zend_Form_Element_Captcha(
        			'captcha', // This is the name of the input field
        			array('label' => 'Write the chars to the field',
        					'captcha' => array( // Here comes the magic...
        							// First the type...
        							'captcha' => 'Image',
        							// Length of the word...
        							'wordLen' => 6,
        							// Captcha timeout, 5 mins
        							'timeout' => 300,
        							// What font to use...
        							'font' => PUBLIC_PATH . '/resources/fonts/arial.ttf',
        							// Where to put the image
        							'imgDir' => PUBLIC_PATH . '/tmp',
        							// URL to the images
        							// This was bogus, here's how it should be... Sorry again :S
        							'imgUrl' => '/tmp/',
        					)));
        	$this->addElement($captcha);
        }        
        
        $this->addElement('submit', 'submit', array(
            'required' => false,
            'label'    => 'Submit your request',
            'decorators' => array('Composite'),
            'class'    => 'button bigbtn'
        ));
        
        
    }
}