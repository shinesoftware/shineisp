<?php
class Admin_Form_ProductsAttributesGroupsForm extends Zend_Form
{   
    public function init()
    {
        // Set the custom decorator
    	$this->addElementPrefixPath('Shineisp_Decorator', 'Shineisp/Decorator/', 'decorator');
    	
    	$this->addElement('text', 'name', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => 'Feature Name',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input'
        ));

    	$this->addElement('multiselect', 'attributes', array(
            'label'      => 'Attributes',
            'decorators' => array('Composite'),
    		'size'	     => '10x',
            'class'      => 'multiselect'
        ));
        
        $this->getElement('attributes')
                  ->setAllowEmpty(false)
                  ->setRegisterInArrayValidator(false) // Disable the Validator in order to manage a dynamic products list.
                  ->setMultiOptions(ProductsAttributes::getAttributes());
                  
    	$this->addElement('checkbox', 'isrecurring', array(
            'label'      => 'Is Recurring',
            'decorators' => array('Composite')
        ));
    	
    	$this->addElement('checkbox', 'iscomparable', array(
            'label'      => 'Is Comparable',
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