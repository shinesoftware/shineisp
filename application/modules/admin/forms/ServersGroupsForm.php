<?php
class Admin_Form_ServersGroupsForm extends Zend_Form
{   
    public function init()
    {
        // Set the custom decorator
    	$this->addElementPrefixPath('Shineisp_Decorator', 'Shineisp/Decorator/', 'decorator');
    	$translate = Shineisp_Registry::get('Zend_Translate');
    	
    	$this->addElement('text', 'name', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => $translate->_('Group name'),
            'decorators' => array('Bootstrap'),
            'class'      => 'form-control'
        ));

    	$this->addElement('select', 'fill_type', array(
            'label'      => $translate->_('Fill type'),
            'decorators' => array('Bootstrap')
        ));
        $this->getElement('fill_type')
                  ->setAllowEmpty(false)
                  ->setMultiOptions(array(
                    	'1' => $translate->_('Create services on the least full server'),
                    	'2' => $translate->_('Fill default server until full then switch to next least used'), 
                    	'3' => $translate->_('Fill servers starting from the newest to the older'),
                    	'4' => $translate->_('Fill servers starting from the older to the newest'),
                    	'5' => $translate->_('Fill servers randomly'),
                    	'6' => $translate->_('Fill manually. Only default server will be used.'),
                    	'7' => $translate->_('Fill servers starting from the cheaper to the expensive.'),
                    	'8' => $translate->_('Fill servers starting from the expensive to the cheaper.'),
                    )
				);		
		

		$this->addElement('multiselect', 'servers', array(
            'label'      => $translate->_('Servers'),
            'decorators' => array('Bootstrap'),
            'title'	     => $translate->_('Select ...'),
    		'data-container' => 'body',
    		'data-selected-text-format' => 'count > 2',
    		'data-size' => 'auto',
    		'data-live-search' => 'true',
            'class'      => 'multiselect show-tick col-md-4'
        ));
        
        $this->getElement('servers')
                  ->setAllowEmpty(false)
                  ->setRegisterInArrayValidator(false) // Disable the Validator in order to manage a dynamic products list.
                  ->setMultiOptions(Servers::getServers());

    	$this->addElement('checkbox', 'active', array(
            'label'      => $translate->_('Active'),
            'decorators' => array('Bootstrap')
        ));
        
        $this->addElement('hidden', 'group_id');
    }
    
}