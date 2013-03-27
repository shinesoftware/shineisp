<?php
class Admin_Form_ProfileForm extends Zend_Form
{   
    public function init()
    {
    	$registry = Zend_Registry::getInstance ();
    	$auth = Zend_Auth::getInstance ();
    	if ($auth->hasIdentity ()) {
    		$logged_user= $auth->getIdentity ();
    	}
    	
        // Set the custom decorator
    	$this->addElementPrefixPath('Shineisp_Decorator', 'Shineisp/Decorator/', 'decorator');
    	
    	$this->addElement('text', 'firstname', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => 'Firstname',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input'
        ));
    	
    	$this->addElement('text', 'lastname', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => 'Lastname',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input'
        ));
    	
    	// Check if the user is an administrator, if not the select role object will become an hidden field
    	if(AdminRoles::isAdministrator($logged_user['user_id'])){
    		$this->addElement('select', 'role_id', array(
    				'required'   => true,
    				'label'      => 'Role',
    				'decorators' => array('Composite'),
    				'class'      => 'text-input large-input'
    		));
    		
    		$this->getElement('role_id')
					    		->setAllowEmpty(false)
					    		->setRegisterInArrayValidator(false)
					    		->setMultiOptions(AdminRoles::getList());
    		
    		$this->addElement('select', 'isp_id', array(
    				'required'   => true,
    				'label'      => 'Isp Company',
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
            'label'      => 'Email',
            'class'      => 'text-input large-input'
        ));
        
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
        
        $this->addElement('password', 'password', array(
        		'filters'    => array('StringTrim'),
        		'decorators' => array('Composite'),
        		'validators' => array(
        				array('regex', false, '/^[a-zA-Z0-9\-\_\.\%\!\$]{6,20}$/')
        		),
        		'label'      => 'Password',
        		'class'      => 'text-input large-input'
        ));
        
        $this->addElement('hidden', 'user_id');
    }
}