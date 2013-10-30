<?php
class Admin_Form_PasswordForm extends Zend_Form
{
    public function init()
    {
    	// Set the custom decorator
        $this->addElementPrefixPath('Shineisp_Decorator', 'Shineisp/Decorator/', 'decorator');
        $translate = Shineisp_Registry::get('Zend_Translate');
        
        $this->addElement('text', 'email', array(
            'filters'    => array('StringTrim', 'StringToLower'),
            'validators' => array(
                'EmailAddress',
            ),
            'decorators' => array('Bootstrap'),
            'required'   => true,
            'description'      => $translate->_('Write your own email'),
            'label'      => $translate->_('Email'),
            'class'      => 'input-large'
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

        $this->addElement('submit', 'submit', array(
            'label'    => 'Submit',
            'decorators' => array('Bootstrap'),
        	'class'      => 'button'
        ));
        
    }
}