<?php
class Admin_Form_ServersGroupsForm extends Zend_Form
{   
    public function init()
    {
        // Set the custom decorator
    	$this->addElementPrefixPath('Shineisp_Decorator', 'Shineisp/Decorator/', 'decorator');
    	
    	$this->addElement('text', 'name', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => 'Group name',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input'
        ));

    	$this->addElement('select', 'fill_type', array(
            'label'      => 'Fill type',
            'decorators' => array('Composite')
        ));
        $this->getElement('fill_type')
                  ->setAllowEmpty(false)
                  ->setMultiOptions(array(
                    	'1' => 'Create services on the least full server',
                    	'2' => 'Fill default server until full then switch to next least used', 
                    	'3' => 'Fill servers starting from the newest to the older',
                    	'4' => 'Fill servers starting from the older to the newest',
                    	'5' => 'Fill servers randomly',
                    	'6' => 'Fill manually. Only default server will be used.'
                    )
				);		
		

    	$this->addElement('checkbox', 'active', array(
            'label'      => 'Active',
            'decorators' => array('Composite')
        ));
        
        $this->addElement('submit', 'save', array(
            'label'    => 'Save',
            'decorators' => array('Composite'),
            'class'    => 'button'
        ));
                
        $this->addElement('hidden', 'group_id');
    }
    
}