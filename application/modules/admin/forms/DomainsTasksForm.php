<?php
class Admin_Form_DomainsTasksForm extends Zend_Form
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
            'class'       => 'form-control'
        ));
        
        $this->addElement('text', 'startdate', array(
            'filters'     => array('StringTrim'),
            'required'    => true,
            'decorators'  => array('Bootstrap'),
            'label'       => $translate->_('Start'),
            'class'       => 'form-control'
        ));
        
        $this->addElement('text', 'enddate', array(
            'filters'     => array('StringTrim'),
            'decorators'  => array('Bootstrap'),
            'label'       => $translate->_('End'),
            'class'       => 'form-control'
        ));
        
        $this->addElement('textarea', 'log', array(
            'filters'     => array('StringTrim'),
            'decorators'  => array('Bootstrap'),
            'label'       => $translate->_('Log'),
            'class'       => 'form-control'
        ));
        
        $this->addElement('select', 'status_id', array(
            'required'    => true,
            'decorators'  => array('Bootstrap'),
            'label'       => $translate->_('Status'),
            'class'       => 'form-control'
        ));
        
        $this->getElement('status_id')
					        ->setAllowEmpty(false)
					        ->setRegisterInArrayValidator(false)
					        ->setMultiOptions(Statuses::getList('domains_tasks'));
        
        $this->addElement('select', 'domain_id', array(
            'required'    => true,
            'decorators'  => array('Bootstrap'),
            'label'       => $translate->_('Domain'),
            'class'       => 'form-control'
        ));
        
        $this->getElement('domain_id')
					        ->setAllowEmpty(false)
					        ->setRegisterInArrayValidator(false)
					        ->setMultiOptions(Domains::getList());
        
        $this->addElement('select', 'registrars_id', array(
            'required'    => true,
            'decorators'  => array('Bootstrap'),
            'label'       => $translate->_('Registrar'),
            'class'       => 'form-control'
        ));
        
        $this->getElement('registrars_id')
				        ->setAllowEmpty(false)
				        ->setRegisterInArrayValidator(false)
				        ->setMultiOptions(Registrars::getList());
        
        $this->addElement('hidden', 'task_id');

    }
    
}