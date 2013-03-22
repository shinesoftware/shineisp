<?php

class Admin_Form_WikiCategoriesForm extends Zend_Form
{   
    public function init()
    {
        // Set the custom decorator
    	$this->addElementPrefixPath('Shineisp_Decorator', 'Shineisp/Decorator/', 'decorator');
    	
        $this->addElement('text', 'category', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => 'Category',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input'
        ));

        $this->addElement('select', 'public', array(
        		'decorators'  => array('Composite'),
        		'label'       => 'Public',
        		'class'       => 'text-input large-input',
        		'multioptions' => array( 0 => 'NO', 1 => 'YES')
        ));
        
        $this->addElement('submit', 'save', array(
            'required' => false,
            'label'    => 'Save',
            'decorators' => array('Composite'),
            'class'    => 'button'
        ));
        
        $this->addElement('hidden', 'category_id');
    }
}