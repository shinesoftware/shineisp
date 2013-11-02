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
            'decorators'  => array('Bootstrap'),
            'label'       => $translate->_('Name'),
            'class'       => 'form-control input-lg'
        ));
        
        $this->addElement('select', 'isp_id', array(
            'decorators'  => array('Bootstrap'),
            'label'       => $translate->_('ISP Profile'),
            'class'       => 'form-control input-lg'
        ));
                
        $this->getElement('isp_id')
                  ->setAllowEmpty(false)
                  ->setRegisterInArrayValidator(false)
                  ->setMultiOptions(Isp::getList());           

        $this->addElement('select', 'active', array(
            'label'      => $translate->_('Active'),
            'decorators' => array('Bootstrap'),
            'class'      => 'form-control input-lg',
            'multioptions' => array('0' => 'No', '1'=>'Yes')
        ));        
        
        $this->addElement('hidden', 'panel_id');

    }
    
}