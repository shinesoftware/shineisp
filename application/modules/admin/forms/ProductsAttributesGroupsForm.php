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
            'decorators' => array('Bootstrap'),
            'class'      => 'form-control'
        ));

    	$this->addElement('multiselect', 'attributes', array(
            'label'      => $translate->_('Attributes'),
            'decorators' => array('Bootstrap'),
    		'title'	     => $translate->_('Select ...'),
    		'data-header'    => $translate->_('Select the product attributes...'),
    		'data-container' => 'body',
    		'data-selected-text-format' => 'count > 2',
    		'data-size' => 'auto',
    		'data-live-search' => 'true',
            'class'      => 'multiselect show-tick col-md-4'
        ));
        
        $this->getElement('attributes')
                  ->setAllowEmpty(false)
                  ->setRegisterInArrayValidator(false) // Disable the Validator in order to manage a dynamic products list.
                  ->setMultiOptions(ProductsAttributes::getAttributes());
                  
    	$this->addElement('checkbox', 'isrecurring', array(
            'label'      => $translate->_('Is Recurring'),
            'decorators' => array('Bootstrap')
        ));
    	
    	$this->addElement('checkbox', 'iscomparable', array(
            'label'      => $translate->_('Is Comparable'),
            'decorators' => array('Bootstrap')
        ));
  
        $this->addElement('hidden', 'group_id');
    }
    
}