<?php
class Default_Form_ProductForm extends Zend_Form
{   
    public function init()
    {
        // Set the custom decorator
    	$this->addElementPrefixPath('Shineisp_Decorator', 'Shineisp/Decorator/', 'decorator');
    	$translate = Shineisp_Registry::get('Zend_Translate');
    	
        $this->addElement('submit', 'submit', array(
            'required' => false,
            'label'      => $translate->_('Add to Cart'),
            'decorators' => array('Bootstrap'),
            'class'    => 'btn btn-success'
        ));
        
        $this->addElement('hidden', 'product_id', array('decorators' => array('Bootstrap')));
    }
}