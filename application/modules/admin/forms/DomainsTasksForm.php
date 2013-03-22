<?php
class Admin_Form_DomainsTasksForm extends Zend_Form
{
    
    public function init()
    {
        // Set the custom decorator
        $this->addElementPrefixPath('Shineisp_Decorator', 'Shineisp/Decorator/', 'decorator');
        
        $this->addElement('text', 'action', array(
            'filters'     => array('StringTrim'),
            'required'    => true,
            'decorators'  => array('Composite'),
            'label'       => 'Action',
            'class'       => 'text-input large-input'
        ));
        
        $this->addElement('text', 'startdate', array(
            'filters'     => array('StringTrim'),
            'required'    => true,
            'decorators'  => array('Composite'),
            'label'       => 'Start',
            'class'       => 'text-input large-input'
        ));
        
        $this->addElement('text', 'enddate', array(
            'filters'     => array('StringTrim'),
            'decorators'  => array('Composite'),
            'label'       => 'End',
            'class'       => 'text-input large-input'
        ));
        
        $this->addElement('textarea', 'log', array(
            'filters'     => array('StringTrim'),
            'decorators'  => array('Composite'),
            'label'       => 'Log',
            'class'       => 'text-input large-input'
        ));
        
        $this->addElement('select', 'status_id', array(
            'filters'     => array('StringTrim'),
            'required'    => true,
            'decorators'  => array('Composite'),
            'label'       => 'Status',
            'class'       => 'text-input large-input'
        ));
        
        $this->getElement('status_id')
					        ->setAllowEmpty(false)
					        ->setRegisterInArrayValidator(false)
					        ->setMultiOptions(Statuses::getList('domains_tasks'));
        
        $this->addElement('select', 'domain_id', array(
            'filters'     => array('StringTrim'),
            'required'    => true,
            'decorators'  => array('Composite'),
            'label'       => 'Domain',
            'class'       => 'text-input large-input'
        ));
        
        $this->getElement('domain_id')
					        ->setAllowEmpty(false)
					        ->setRegisterInArrayValidator(false)
					        ->setMultiOptions(Domains::getList());
        
        $this->addElement('select', 'registrars_id', array(
            'filters'     => array('StringTrim'),
            'required'    => true,
            'decorators'  => array('Composite'),
            'label'       => 'Registrar',
            'class'       => 'text-input large-input'
        ));
        
        $this->getElement('registrars_id')
				        ->setAllowEmpty(false)
				        ->setRegisterInArrayValidator(false)
				        ->setMultiOptions(Registrars::getList());
        
        $this->addElement('hidden', 'task_id');

    }
    
}