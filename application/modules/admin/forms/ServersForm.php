<?php
class Admin_Form_ServersForm extends Zend_Form
{   
    public function init()
    {
        // Set the custom decorator
    	$this->addElementPrefixPath('Shineisp_Decorator', 'Shineisp/Decorator/', 'decorator');
    	$translate = Shineisp_Registry::get('Zend_Translate');
    	
    	$this->addElement('select', 'isp_id', array(
                'label' => $translate->_('Isp'),
                'decorators' => array('Bootstrap'),
                'class'      => 'form-control input-lg'
        ));
        
        $this->getElement('isp_id')
                  ->setAllowEmpty(false)
                  ->setMultiOptions(Isp::getList())
                  ->setRequired(true);
                  
        $this->addElement('select', 'type_id', array(
                'label' => $translate->_('Server Type'),
                'decorators' => array('Bootstrap'),
                'class'      => 'form-control input-lg'
        ));
        
        $this->getElement('type_id')
                  ->setAllowEmpty(false)
                  ->setMultiOptions(Servers_Types::getList())
                  ->setRequired(true);
                  
        $this->addElement('select', 'status_id', array(
                'label' => $translate->_('Status'),
                'decorators' => array('Bootstrap'),
                'class'      => 'form-control input-lg'
        ));
        
        $this->getElement('status_id')
                  ->setAllowEmpty(false)
                  ->setMultiOptions(Statuses::getList('servers'))
                  ->setRequired(true);
    	
    	$this->addElement('text', 'name', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => $translate->_('Server Name'),
            'decorators' => array('Bootstrap'),
            'class'      => 'form-control input-lg'
        ));
        
        $this->addElement('text', 'ip', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => $translate->_('IP'),
            'decorators' => array('Bootstrap'),
            'class'      => 'form-control input-lg'
        ));
        
        $this->addElement('text', 'netmask', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => $translate->_('Netmask'),
            'decorators' => array('Bootstrap'),
            'class'      => 'form-control input-lg'
        ));
        
        $this->addElement('text', 'host', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => $translate->_('Host'),
            'decorators' => array('Bootstrap'),
            'class'      => 'form-control input-lg'
        ));
        
        $this->addElement('text', 'domain', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => $translate->_('Domain'),
            'decorators' => array('Bootstrap'),
            'class'      => 'form-control input-lg'
        ));
        
        $this->addElement('textarea', 'description', array(
            'required'   => true,
            'label'      => $translate->_('Description'),
            'decorators' => array('Bootstrap'),
            'class'      => 'col-lg-12 form-control input-lg'
        ));
        
		
        $this->addElement('select', 'panel_id', array(
            'filters'    => array('StringTrim'),
            'label'      => $translate->_('Control Panel'),
            'decorators' => array('Bootstrap'),
            'class'      => 'form-control input-lg'
        ));
        $this->getElement('panel_id')
                  ->setAllowEmpty(false)
                  ->setMultiOptions(Panels::getListInstalled(true));            
		
        $this->addElement('text', 'datacenter', array(
            'filters'    => array('StringTrim'),
            'required'   => false,
            'label'      => $translate->_('Datacenter'),
            'decorators' => array('Bootstrap'),
            'class'      => 'form-control input-lg'
        ));
		
        $this->addElement('text', 'cost', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => $translate->_('Server cost'),
            'decorators' => array('Bootstrap'),
            'class'      => 'form-control input-lg'
        ));
		
        $this->addElement('text', 'max_accounts', array(
            'filters'    => array('StringTrim'),
            'required'   => false,
            'label'      => $translate->_('Max accounts'),
            'decorators' => array('Bootstrap'),
            'class'      => 'form-control input-lg'
        ));
		
        $this->addElement('text', 'buy_date', array(
            'filters'     => array('StringTrim'),
            'label'       => $translate->_('Buy Date'),
            'description' => $translate->_('Purchase date of this server'),
            'decorators'  => array('Bootstrap'),
            'class'       => 'form-control input-lg date'
        ));		
		
        $this->addElement('checkbox', 'is_default', array(
            'filters'     => array('StringTrim'),
            'required'    => false,
            'label'       => $translate->_('Default server'),
            'description' => $translate->_('Default server for the group'),
            'decorators'  => array('Bootstrap'),
            'class'       => 'form-control input-lg'
        ));

        $this->addElement('hidden', 'server_id');
    }
}