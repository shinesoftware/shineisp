<?php
class Admin_Form_ProductsAttributesForm extends Zend_Form
{   
    public function init()
    {
        // Set the custom decorator
    	$this->addElementPrefixPath('Shineisp_Decorator', 'Shineisp/Decorator/', 'decorator');
    	$translate = Shineisp_Registry::get('Zend_Translate');
    	
    	$this->addElement('text', 'code', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => $translate->_('Attribute Code'),
            'decorators' => array('Bootstrap'),
            'class'      => 'input-large'
        ));
    			
        $this->addElement('select', 'is_visible_on_front', array(
            'decorators'  => array('Bootstrap'),
            'label'       => $translate->_('Visible on Product page'),
            'class'       => 'input-large'
        ));
        
        $this->getElement('is_visible_on_front')
                  ->setAllowEmpty(false)
                  ->setRegisterInArrayValidator(false)
                  ->setMultiOptions(array('0'=>'No', '1' =>'Yes'));
                  
        $this->addElement('select', 'is_required', array(
            'decorators'  => array('Bootstrap'),
            'label'       => $translate->_('Is Required'),
            'class'       => 'input-large'
        ));
        
        $this->getElement('is_required')
                  ->setAllowEmpty(false)
                  ->setRegisterInArrayValidator(false)
                  ->setMultiOptions(array('0'=>'No', '1' =>'Yes'));
                  
        $this->addElement('select', 'is_comparable', array(
            'decorators'  => array('Bootstrap'),
            'label'       => $translate->_('Is Comparable'),
            'class'       => 'input-large'
        ));
        
        $this->getElement('is_comparable')
                  ->setAllowEmpty(false)
                  ->setRegisterInArrayValidator(false)
                  ->setMultiOptions(array('0'=>'No', '1' =>'Yes'));
                  
        $this->addElement('select', 'on_product_listing', array(
            'decorators'  => array('Bootstrap'),
            'label'       => $translate->_('Use on Product Listing'),
            'class'       => 'input-large'
        ));
        
        $this->getElement('on_product_listing')
                  ->setAllowEmpty(false)
                  ->setRegisterInArrayValidator(false)
                  ->setMultiOptions(array('0'=>'No', '1' =>'Yes'));
                  
                  
        $this->addElement('select', 'active', array(
            'decorators'  => array('Bootstrap'),
            'label'       => $translate->_('Active'),
            'class'       => 'input-large'
        ));
        
        $this->getElement('active')
                  ->setAllowEmpty(false)
                  ->setRegisterInArrayValidator(false)
                  ->setMultiOptions(array('0'=>'No', '1' =>'Yes'));
                  
                  
        $this->addElement('select', 'system', array(
            'decorators'  => array('Bootstrap'),
            'label'       => $translate->_('System'),
            'class'       => 'input-large'
        ));
        
        $this->getElement('system')
                  ->setAllowEmpty(false)
                  ->setRegisterInArrayValidator(false)
                  ->setMultiOptions(array('0'=>'No', '1' =>'Yes'));
                  
    	
    	$this->addElement('text', 'position', array(
            'filters'    => array('StringTrim'),
            'label'      => $translate->_('Position'),
            'decorators' => array('Bootstrap'),
            'class'      => 'input-large'
        ));
                  
    	
    	$this->addElement('select', 'system_var', array(
            'label'      => $translate->_('System Variable'),
            'decorators' => array('Bootstrap'),
            'class'      => 'input-large'
        ));
        
        $this->getElement('system_var')
                  ->setAllowEmpty(false)
                  ->setRegisterInArrayValidator(false)
                  ->setMultiOptions(Panels::getOptionsXmlFields(Isp::getPanel()));        
    	
    	$this->addElement('text', 'defaultvalue', array(
            'filters'    => array('StringTrim'),
            'label'      => $translate->_('Default Value'),
            'description'      => $translate->_('When the type of the object is a selectbox you have to use the Json code. eg: {"1": "True", "0": "False"}'),
            'decorators' => array('Bootstrap'),
            'class'      => 'input-large'
        ));
    	
        $this->addElement('hidden', 'language_id', array(
            'decorators'  => array('Bootstrap')
        ));
    	
    	$this->addElement('text', 'label', array(
            'filters'    => array('StringTrim'),
            'label'      => $translate->_('Label'),
    		'required'   => true,
            'decorators' => array('Bootstrap'),
            'class'      => 'input-large'
        ));      
    	
    	$this->addElement('text', 'description', array(
            'filters'    => array('StringTrim'),
            'label'      => $translate->_('Description'),
            'decorators' => array('Bootstrap'),
            'class'      => 'input-large'
        ));      
    	
    	$this->addElement('text', 'prefix', array(
            'filters'    => array('StringTrim'),
            'label'      => $translate->_('Prefix'),
            'decorators' => array('Bootstrap'),
            'class'      => 'input-large'
        ));      
    	
    	$this->addElement('text', 'suffix', array(
            'filters'    => array('StringTrim'),
            'label'      => $translate->_('Suffix'),
            'decorators' => array('Bootstrap'),
            'class'      => 'input-large'
        ));      
    	
    	$this->addElement('select', 'type', array(
            'filters'    => array('StringTrim'),
            'label'      => $translate->_('Type'),
    		'description' => $translate->_('If the type is a dropdown selector you have to set the options using the Json structure in the default value textbox.'),
    		'required'   => true,
            'decorators' => array('Bootstrap'),
            'class'      => 'input-large'
        ));      
 		
        $this->getElement('type')
                  ->setAllowEmpty(false)
                  ->setRegisterInArrayValidator(false)
                  ->setMultiOptions(array('text'=>'Textbox', 'select' =>'Dropdown Select', 'checkbox' =>'Checkbox'));
                          
                
        $this->addElement('hidden', 'attribute_id');
    }
    
}