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
            'decorators' => array('Bootstrap'),
            'class'      => 'form-control'
        ));
        
        $this->getElement('parameter_id')
                  ->setAllowEmpty(false)
                  ->setMultiOptions(SettingsParameters::getList('admin'));  
       
        $this->addElement('textarea', 'value', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => $translate->_('value'),
            'decorators' => array('Bootstrap'),
            'class'      => 'form-control'
        ));        
        
        $this->addElement('select', 'isp_id', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => $translate->_('Isp'),
            'decorators' => array('Bootstrap'),
            'class'      => 'form-control'
        ));
        
        $this->getElement('isp_id')
                  ->setAllowEmpty(false)
                  ->setMultiOptions(Isp::getList());
                    
        $this->addElement('hidden', 'setting_id');
    }
}