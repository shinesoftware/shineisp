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
            'decorators'  => array('Bootstrap'),
            'label'       => $translate->_('Action'),
            'class'       => 'form-control input-lg'
        ));
        
        $this->addElement('text', 'start', array(
            'filters'     => array('StringTrim'),
            'required'    => true,
            'decorators'  => array('Bootstrap'),
            'label'       => $translate->_('Start Date'),
            'class'       => 'form-control input-lg date'
        ));
        
        $this->addElement('text', 'end', array(
            'filters'     => array('StringTrim'),
            'decorators'  => array('Bootstrap'),
            'label'       => $translate->_('End Date'),
            'class'       => 'form-control input-lg date'
        ));
        
        $this->addElement('textarea', 'log', array(
            'filters'     => array('StringTrim'),
            'decorators'  => array('Bootstrap'),
            'label'       => $translate->_('Log'),
            'class'       => 'form-control input-lg'
        ));
        
        $this->addElement('textarea', 'parameters', array(
            'filters'     => array('StringTrim'),
            'decorators'  => array('Bootstrap'),
            'label'       => $translate->_('Parameters'),
            'description' => $translate->_('Json encoded attribute parameters'),
            'rows'       => '5',
            'class'       => 'form-control input-lg'
        ));
        
        $this->addElement('select', 'panel_id', array(
            'filters'     => array('StringTrim'),
            'decorators'  => array('Bootstrap'),
            'label'       => $translate->_('Panel'),
            'class'       => 'form-control input-lg'
        ));
        
        $this->getElement('panel_id')
				        ->setAllowEmpty(false)
				        ->setRegisterInArrayValidator(false)
				        ->setMultiOptions(Panels::getList());
        
        $this->addElement('select', 'status_id', array(
            'filters'     => array('StringTrim'),
            'decorators'  => array('Bootstrap'),
            'label'       => $translate->_('Status'),
            'class'       => 'form-control input-lg'
        ));
        
        $this->getElement('status_id')
				        ->setAllowEmpty(false)
				        ->setRegisterInArrayValidator(false)
				        ->setMultiOptions(Statuses::getList('domains'));
        
        $this->addElement('hidden', 'action_id');

    }
    
}