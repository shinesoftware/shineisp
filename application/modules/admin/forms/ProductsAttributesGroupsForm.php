<?php
class Admin_Form_ProductsAttributesGroupsForm extends Zend_Form
{   
    public function init()
    {
        // Set the custom decorator
    	$this->addElementPrefixPath('Shineisp_Decorator', 'Shineisp/Decorator/', 'decorator');
    	$translate = Shineisp_Registry::get('Zend_Translate');
    	
    	$this->addElement('text', 'name', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => $translate->_('Feature Name'),
            'decorators' => array('Composite'),
            'class'      => 'input-large'
        ));

    	$this->addElement('multiselect', 'attributes', array(
            'label'      => $translate->_('Attributes'),
            'decorators' => array('Composite'),
    		'size'	     => '10x',
            'class'      => 'multiselect'
        ));
        
        $this->getElement('attributes')
                  ->setAllowEmpty(false)
                  ->setRegisterInArrayValidator(false) // Disable the Validator in order to manage a dynamic products list.
                  ->setMultiOptions(ProductsAttributes::getAttributes());
                  
    	$this->addElement('checkbox', 'isrecurring', array(
            'label'      => $translate->_('Is Recurring'),
            'decorators' => array('Composite')
        ));
    	
    	$this->addElement('checkbox', 'iscomparable', array(
            'label'      => $translate->_('Is Comparable'),
            'decorators' => array('Composite')
        ));
  
        $this->addElement('hidden', 'group_id');
    }
    
}