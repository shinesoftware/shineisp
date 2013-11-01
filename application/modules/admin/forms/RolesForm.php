<?php
class Admin_Form_RolesForm extends Zend_Form
{
    
    public function init()
    {
        // Set the custom decorator
        $this->addElementPrefixPath('Shineisp_Decorator', 'Shineisp/Decorator/', 'decorator');
        $translate = Shineisp_Registry::get('Zend_Translate');
        
        $this->addElement('text', 'name', array(
        		'filters'     => array('StringTrim'),
	            'decorators'  => array('Bootstrap'),
        		'required'    => true,
	            'label'       => $translate->_('Role Name'),
	            'description' => $translate->_('Write here the name of the role in lowercase'),
	            'class'       => 'form-control'
        ));
        
        $this->addElement('multiselect', 'users', array(
            'decorators'  => array('Bootstrap'),
            'label'       => $translate->_('Users'),
            'title'	     => $translate->_('Select ...'),
    		'data-container' => 'body',
    		'data-selected-text-format' => 'count > 2',
    		'data-size' => 'auto',
    		'data-live-search' => 'true',
            'class'      => 'multiselect show-tick span4'
        ));
        
        $this->getElement('users')
					        ->setAllowEmpty(false)
					        ->setRegisterInArrayValidator(false)
					        ->setMultiOptions(AdminUser::getList());
        
        $this->addElement('hidden', 'role_id');

    }
    
}