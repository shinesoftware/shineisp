<?php
class Admin_Form_SettingsForm extends Zend_Form
{   
    public function init()
    {
        // Set the custom decorator
    	$this->addElementPrefixPath('Shineisp_Decorator', 'Shineisp/Decorator/', 'decorator');
    	
        $this->addElement('select', 'parameter_id', array(
            'filters'    => array('StringTrim'),
            'label'      => 'parameter',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input'
        ));
        
        $this->getElement('parameter_id')
                  ->setAllowEmpty(false)
                  ->setMultiOptions(SettingsParameters::getList('admin'));  
       
        $this->addElement('textarea', 'value', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => 'value',
            'decorators' => array('Composite'),
            'class'      => 'text-input little-input'
        ));        
        
        $this->addElement('select', 'isp_id', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => 'isp',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input'
        ));
        
        $this->getElement('isp_id')
                  ->setAllowEmpty(false)
                  ->setMultiOptions(Isp::getList());
                          
        $this->addElement('submit', 'save', array(
            'required' => false,
            'label'    => 'Save',
            'decorators' => array('Composite'),
            'class'    => 'button'
        ));
        
        $this->addElement('reset', 'reset', array(
            'required' => false,
            'label'    => 'reset',
            'decorators' => array('Composite'),
            'class'    => 'button'
        ));
        
        $this->addElement('hidden', 'setting_id');
    }
}