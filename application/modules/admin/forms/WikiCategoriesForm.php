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
            'decorators' => array('Bootstrap'),
            'class'      => 'form-control'
        ));

        $this->addElement('select', 'public', array(
        		'decorators'  => array('Bootstrap'),
        		'label'       => $translate->_('Public'),
        		'class'       => 'form-control',
        		'multioptions' => array( 0 => $translate->_('NO'), 1 => $translate->_('YES'))
        ));
        
        $this->addElement('hidden', 'category_id');
    }
}