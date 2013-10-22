<?php
class Admin_Form_SettingsForm extends Zend_Form
{   
    public function init()
    {
        // Set the custom decorator
    	$this->addElementPrefixPath('Shineisp_Decorator', 'Shineisp/Decorator/', 'decorator');
    	$translate = Shineisp_Registry::get('Zend_Translate');
    	
        $this->addElement('select', 'parameter_id', array(
            'filters'    => array('StringTrim'),
            'label'      => $translate->_('parameter'),
            'decorators' => array('Composite'),
            'class'      => 'input-large'
        ));
        
        $this->getElement('parameter_id')
                  ->setAllowEmpty(false)
                  ->setMultiOptions(SettingsParameters::getList('admin'));  
       
        $this->addElement('textarea', 'value', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => $translate->_('value'),
            'decorators' => array('Composite'),
            'class'      => 'little-input'
        ));        
        
        $this->addElement('select', 'isp_id', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => $translate->_('Isp'),
            'decorators' => array('Composite'),
            'class'      => 'input-large'
        ));
        
        $this->getElement('isp_id')
                  ->setAllowEmpty(false)
                  ->setMultiOptions(Isp::getList());
                    
        $this->addElement('hidden', 'setting_id');
    }
}