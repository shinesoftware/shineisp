<?php
class Admin_Form_ProfileForm extends Zend_Form
{   
    public function init()
    {
    	$registry = Shineisp_Registry::getInstance ();
    	$auth = Zend_Auth::getInstance ();
    	if ($auth->hasIdentity ()) {
    		$logged_user= $auth->getIdentity ();
    	}
    	
        // Set the custom decorator
    	$this->addElementPrefixPath('Shineisp_Decorator', 'Shineisp/Decorator/', 'decorator');
    	$translate = Shineisp_Registry::get('Zend_Translate');
    	
    	$this->addElement('text', 'firstname', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => $translate->_('Firstname'),
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input'
        ));
    	
    	$this->addElement('text', 'lastname', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => $translate->_('Lastname'),
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input'
        ));
    	
    	// Check if the user is an administrator, if not the select role object will become an hidden field
    	if(AdminRoles::isAdministrator($logged_user['user_id'])){
    		$this->addElement('select', 'role_id', array(
    				'required'   => true,
    				'label'      => $translate->_('Role'),
    				'decorators' => array('Composite'),
    				'class'      => 'text-input large-input'
    		));
    		
    		$this->getElement('role_id')
					    		->setAllowEmpty(false)
					    		->setRegisterInArrayValidator(false)
					    		->setMultiOptions(AdminRoles::getList());
    		
    		$this->addElement('select', 'isp_id', array(
    				'required'   => true,
    				'label'      => $translate->_('Isp Company'),
    				'decorators' => array('Composite'),
    				'class'      => 'text-input large-input'
    		));
    		
    		$this->getElement('isp_id')
					    		->setAllowEmpty(false)
					    		->setRegisterInArrayValidator(false)
					    		->setMultiOptions(Isp::getList());
    	}else{
    		$this->addElement('hidden', 'role_id');
    		$this->addElement('hidden', 'isp_id');
    	}
    	
        $this->addElement('text', 'email', array(
            'filters'    => array('StringTrim', 'StringToLower'),
            'decorators' => array('Composite'),
        	'validators' => array(
        						array('validator' => 'EmailAddress'),  
        					),
            'required'   => true,
            'label'      => $translate->_('Email'),
            'class'      => 'text-input large-input'
        ));
        
        $this->addElement('password', 'password', array(
        		'filters'    => array('StringTrim'),
        		'decorators' => array('Composite'),
        		'validators' => array(
        				array('regex', false, '/^[a-zA-Z0-9\-\_\.\%\!\$]{6,20}$/')
        		),
        		'label'      => $translate->_('Password'),
        		'class'      => 'text-input large-input'
        ));
        
        $this->addElement('hidden', 'user_id');
    }
}