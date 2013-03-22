<?php
class Admin_Form_ProductsAttributesForm extends Zend_Form
{   
    public function init()
    {
        // Set the custom decorator
    	$this->addElementPrefixPath('Shineisp_Decorator', 'Shineisp/Decorator/', 'decorator');
    	
    	$this->addElement('text', 'code', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => 'Attribute Code',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input'
        ));
    	
        $this->addElement('select', 'is_visible_on_front', array(
            'decorators'  => array('Composite'),
            'label'       => 'Visible on Product page',
            'class'       => 'text-input large-input'
        ));
        
        $this->getElement('is_visible_on_front')
                  ->setAllowEmpty(false)
                  ->setRegisterInArrayValidator(false)
                  ->setMultiOptions(array('0'=>'No', '1' =>'Yes'));
                  
        $this->addElement('select', 'is_required', array(
            'decorators'  => array('Composite'),
            'label'       => 'Is Required',
            'class'       => 'text-input large-input'
        ));
        
        $this->getElement('is_required')
                  ->setAllowEmpty(false)
                  ->setRegisterInArrayValidator(false)
                  ->setMultiOptions(array('0'=>'No', '1' =>'Yes'));
                  
        $this->addElement('select', 'is_comparable', array(
            'decorators'  => array('Composite'),
            'label'       => 'Is Comparable',
            'class'       => 'text-input large-input'
        ));
        
        $this->getElement('is_comparable')
                  ->setAllowEmpty(false)
                  ->setRegisterInArrayValidator(false)
                  ->setMultiOptions(array('0'=>'No', '1' =>'Yes'));
                  
        $this->addElement('select', 'on_product_listing', array(
            'decorators'  => array('Composite'),
            'label'       => 'Use on Product Listing',
            'class'       => 'text-input large-input'
        ));
        
        $this->getElement('on_product_listing')
                  ->setAllowEmpty(false)
                  ->setRegisterInArrayValidator(false)
                  ->setMultiOptions(array('0'=>'No', '1' =>'Yes'));
                  
                  
        $this->addElement('select', 'active', array(
            'decorators'  => array('Composite'),
            'label'       => 'Active',
            'class'       => 'text-input large-input'
        ));
        
        $this->getElement('active')
                  ->setAllowEmpty(false)
                  ->setRegisterInArrayValidator(false)
                  ->setMultiOptions(array('0'=>'No', '1' =>'Yes'));
                  
                  
        $this->addElement('select', 'system', array(
            'decorators'  => array('Composite'),
            'label'       => 'System',
            'class'       => 'text-input large-input'
        ));
        
        $this->getElement('system')
                  ->setAllowEmpty(false)
                  ->setRegisterInArrayValidator(false)
                  ->setMultiOptions(array('0'=>'No', '1' =>'Yes'));
                  
    	
    	$this->addElement('text', 'position', array(
            'filters'    => array('StringTrim'),
            'label'      => 'Position',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input'
        ));
                  
    	
    	$this->addElement('select', 'system_var', array(
            'label'      => 'System Variable',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input'
        ));
        
        $this->getElement('system_var')
                  ->setAllowEmpty(false)
                  ->setRegisterInArrayValidator(false)
                  ->setMultiOptions(Panels::getOptionsXmlFields(Isp::getPanel()));        
    	
    	$this->addElement('text', 'defaultvalue', array(
            'filters'    => array('StringTrim'),
            'label'      => 'Default Value',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input'
        ));
    	
    	$this->addElement('text', 'code', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => 'Attribute Code',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input'
        ));
        
        $this->addElement('hidden', 'language_id', array(
            'decorators'  => array('Composite')
        ));
    	
    	$this->addElement('text', 'label', array(
            'filters'    => array('StringTrim'),
            'label'      => 'Label',
    		'required'   => true,
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input'
        ));      
    	
    	$this->addElement('text', 'description', array(
            'filters'    => array('StringTrim'),
            'label'      => 'Description',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input'
        ));      
    	
    	$this->addElement('text', 'prefix', array(
            'filters'    => array('StringTrim'),
            'label'      => 'Prefix',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input'
        ));      
    	
    	$this->addElement('text', 'suffix', array(
            'filters'    => array('StringTrim'),
            'label'      => 'Suffix',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input'
        ));      
    	
    	$this->addElement('select', 'type', array(
            'filters'    => array('StringTrim'),
            'label'      => 'Type',
    		'description' => 'If the type is a dropdown selector you have to set the options using the Json structure in the default value textbox.',
    		'required'   => true,
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input'
        ));      
 		
        $this->getElement('type')
                  ->setAllowEmpty(false)
                  ->setRegisterInArrayValidator(false)
                  ->setMultiOptions(array('text'=>'Textbox', 'select' =>'Dropdown Select', 'checkbox' =>'Checkbox'));
                          
        $this->addElement('submit', 'save', array(
            'label'    => 'Save',
            'decorators' => array('Composite'),
            'class'    => 'button'
        ));
                
        $this->addElement('hidden', 'attribute_id');
    }
    
}