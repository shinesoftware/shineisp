<?php
class Default_Form_ProductForm extends Zend_Form
{   
    public function init()
    {
        // Set the custom decorator
    	$this->addElementPrefixPath('Shineisp_Decorator', 'Shineisp/Decorator/', 'decorator');
        
        $this->addElement('submit', 'submit', array(
            'required' => false,
            'label'    => 'Add to Cart',
            'decorators' => array('Composite'),
            'class'    => 'button radius success'
        ));
        
        $this->addElement('hidden', 'product_id', array('decorators' => array('Composite')));
    }
}