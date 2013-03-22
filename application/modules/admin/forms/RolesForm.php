<?php
class Admin_Form_RolesForm extends Zend_Form
{
    
    public function init()
    {
        // Set the custom decorator
        $this->addElementPrefixPath('Shineisp_Decorator', 'Shineisp/Decorator/', 'decorator');
        
        $this->addElement('text', 'name', array(
        		'filters'     => array('StringTrim'),
	            'decorators'  => array('Composite'),
        		'required'    => true,
	            'label'       => 'Role Name',
	            'description' => 'Write here the name of the role in lowercase',
	            'class'       => 'text-input large-input'
        ));
        
        $this->addElement('multiselect', 'users', array(
            'decorators'  => array('Composite'),
            'label'       => 'Users',
            'class'       => 'text-input large-input'
        ));
        
        $this->getElement('users')
					        ->setAllowEmpty(false)
					        ->setRegisterInArrayValidator(false)
					        ->setMultiOptions(AdminUser::getList());
        
        $this->addElement('submit', 'save', array(
            'required' => false,
            'label'    => 'Save',
            'decorators' => array('Composite'),
            'class'    => 'button'
        ));
        
        $this->addElement('hidden', 'role_id');

    }
    
}