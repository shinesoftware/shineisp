<?php
class Admin_Form_PanelsForm extends Zend_Form
{
    
    public function init()
    {
        // Set the custom decorator
        $this->addElementPrefixPath('Shineisp_Decorator', 'Shineisp/Decorator/', 'decorator');
        $translate = Shineisp_Registry::get('Zend_Translate');
        
        $this->addElement('text', 'name', array(
            'filters'     => array('StringTrim'),
            'required'    => true,
            'decorators'  => array('Composite'),
            'label'       => $translate->_('Name'),
            'class'       => 'text-input large-input'
        ));
        
        $this->addElement('select', 'isp_id', array(
            'decorators'  => array('Composite'),
            'label'       => $translate->_('ISP Profile'),
            'class'       => 'text-input large-input'
        ));
                
        $this->getElement('isp_id')
                  ->setAllowEmpty(false)
                  ->setRegisterInArrayValidator(false)
                  ->setMultiOptions(Isp::getList());           

        $this->addElement('select', 'active', array(
            'label'      => $translate->_('Active'),
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input',
            'multioptions' => array('0' => 'No', '1'=>'Yes')
        ));        
        
        $this->addElement('hidden', 'panel_id');

    }
    
}