<?php
class Admin_Form_PurchasescategoriesForm extends Zend_Form
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
            'class'      => 'form-control input-lg'
        ));   
        
        $this->addElement('hidden', 'category_id');
    }
}