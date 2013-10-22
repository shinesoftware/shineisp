<?php
class Admin_Form_PanelsActionsForm extends Zend_Form
{
    
    public function init()
    {
        // Set the custom decorator
        $this->addElementPrefixPath('Shineisp_Decorator', 'Shineisp/Decorator/', 'decorator');
        $translate = Shineisp_Registry::get('Zend_Translate');
        
        $this->addElement('text', 'action', array(
            'filters'     => array('StringTrim'),
            'required'    => true,
            'decorators'  => array('Composite'),
            'label'       => $translate->_('Action'),
            'class'       => 'text-input large-input'
        ));
        
        $this->addElement('text', 'start', array(
            'filters'     => array('StringTrim'),
            'required'    => true,
            'decorators'  => array('Composite'),
            'label'       => $translate->_('Start Date'),
            'class'       => 'text-input large-input date'
        ));
        
        $this->addElement('text', 'end', array(
            'filters'     => array('StringTrim'),
            'decorators'  => array('Composite'),
            'label'       => $translate->_('End Date'),
            'class'       => 'text-input large-input date'
        ));
        
        $this->addElement('textarea', 'log', array(
            'filters'     => array('StringTrim'),
            'decorators'  => array('Composite'),
            'label'       => $translate->_('Log'),
            'class'       => 'text-input large-input'
        ));
        
        $this->addElement('textarea', 'parameters', array(
            'filters'     => array('StringTrim'),
            'decorators'  => array('Composite'),
            'label'       => $translate->_('Parameters'),
            'description' => $translate->_('Json encoded attribute parameters'),
            'rows'       => '5',
            'class'       => 'text-input large-input'
        ));
        
        $this->addElement('select', 'panel_id', array(
            'filters'     => array('StringTrim'),
            'decorators'  => array('Composite'),
            'label'       => $translate->_('Panel'),
            'class'       => 'text-input large-input'
        ));
        
        $this->getElement('panel_id')
				        ->setAllowEmpty(false)
				        ->setRegisterInArrayValidator(false)
				        ->setMultiOptions(Panels::getList());
        
        $this->addElement('select', 'status_id', array(
            'filters'     => array('StringTrim'),
            'decorators'  => array('Composite'),
            'label'       => $translate->_('Status'),
            'class'       => 'text-input large-input'
        ));
        
        $this->getElement('status_id')
				        ->setAllowEmpty(false)
				        ->setRegisterInArrayValidator(false)
				        ->setMultiOptions(Statuses::getList('domains'));
        
        $this->addElement('hidden', 'action_id');

    }
    
}