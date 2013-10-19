<?php

class Admin_Form_WikiCategoriesForm extends Zend_Form
{   
    public function init()
    {
        // Set the custom decorator
    	$this->addElementPrefixPath('Shineisp_Decorator', 'Shineisp/Decorator/', 'decorator');
    	$translate = Shineisp_Registry::get('Zend_Translate');
    	
        $this->addElement('text', 'category', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => $translate->_('Category'),
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input'
        ));

        $this->addElement('select', 'public', array(
        		'decorators'  => array('Composite'),
        		'label'       => $translate->_('Public'),
        		'class'       => 'text-input large-input',
        		'multioptions' => array( 0 => $translate->_('NO'), 1 => $translate->_('YES'))
        ));
        
        $this->addElement('hidden', 'category_id');
    }
}