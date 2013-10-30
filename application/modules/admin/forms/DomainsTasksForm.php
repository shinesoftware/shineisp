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
            'class'       => 'input-large'
        ));
        
        $this->addElement('text', 'startdate', array(
            'filters'     => array('StringTrim'),
            'required'    => true,
            'decorators'  => array('Bootstrap'),
            'label'       => $translate->_('Start'),
            'class'       => 'input-large'
        ));
        
        $this->addElement('text', 'enddate', array(
            'filters'     => array('StringTrim'),
            'decorators'  => array('Bootstrap'),
            'label'       => $translate->_('End'),
            'class'       => 'input-large'
        ));
        
        $this->addElement('textarea', 'log', array(
            'filters'     => array('StringTrim'),
            'decorators'  => array('Bootstrap'),
            'label'       => $translate->_('Log'),
            'class'       => 'input-large'
        ));
        
        $this->addElement('select', 'status_id', array(
            'required'    => true,
            'decorators'  => array('Bootstrap'),
            'label'       => $translate->_('Status'),
            'class'       => 'input-large'
        ));
        
        $this->getElement('status_id')
					        ->setAllowEmpty(false)
					        ->setRegisterInArrayValidator(false)
					        ->setMultiOptions(Statuses::getList('domains_tasks'));
        
        $this->addElement('select', 'domain_id', array(
            'required'    => true,
            'decorators'  => array('Bootstrap'),
            'label'       => $translate->_('Domain'),
            'class'       => 'input-large'
        ));
        
        $this->getElement('domain_id')
					        ->setAllowEmpty(false)
					        ->setRegisterInArrayValidator(false)
					        ->setMultiOptions(Domains::getList());
        
        $this->addElement('select', 'registrars_id', array(
            'required'    => true,
            'decorators'  => array('Bootstrap'),
            'label'       => $translate->_('Registrar'),
            'class'       => 'input-large'
        ));
        
        $this->getElement('registrars_id')
				        ->setAllowEmpty(false)
				        ->setRegisterInArrayValidator(false)
				        ->setMultiOptions(Registrars::getList());
        
        $this->addElement('hidden', 'task_id');

    }
    
}