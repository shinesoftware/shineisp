<?php
class Admin_Form_PasswordForm extends Zend_Form
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

        $this->addElement('submit', 'submit', array(
            'label'    => 'Submit',
            'decorators' => array('Composite'),
        	'class'      => 'button'
        ));
        
    }
}