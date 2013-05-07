<?php
class Admin_Form_ServersForm extends Zend_Form
{   
    public function init()
    {
        // Set the custom decorator
    	$this->addElementPrefixPath('Shineisp_Decorator', 'Shineisp/Decorator/', 'decorator');
    	
    	$this->addElement('select', 'isp_id', array(
                'label' => 'isp',
                'decorators' => array('Composite'),
                'class'      => 'text-input large-input'
        ));
        
        $this->getElement('isp_id')
                  ->setAllowEmpty(false)
                  ->setMultiOptions(Isp::getList())
                  ->setRequired(true);
                  
        $this->addElement('select', 'type_id', array(
                'label' => 'servertype',
                'decorators' => array('Composite'),
                'class'      => 'text-input large-input'
        ));
        
        $this->getElement('type_id')
                  ->setAllowEmpty(false)
                  ->setMultiOptions(Servers_Types::getList())
                  ->setRequired(true);
                  
        $this->addElement('select', 'status_id', array(
                'label' => 'status',
                'decorators' => array('Composite'),
                'class'      => 'text-input large-input'
        ));
        
        $this->getElement('status_id')
                  ->setAllowEmpty(false)
                  ->setMultiOptions(Statuses::getList('servers'))
                  ->setRequired(true);
    	
    	$this->addElement('text', 'name', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => 'servername',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input'
        ));
        
        $this->addElement('text', 'ip', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => 'ip',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input'
        ));
        
        $this->addElement('text', 'netmask', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => 'netmask',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input'
        ));
        
        $this->addElement('text', 'host', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => 'host',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input'
        ));
        
        $this->addElement('text', 'domain', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => 'domain',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input'
        ));
        
        $this->addElement('textarea', 'description', array(
            'required'   => true,
            'label'      => 'description',
            'decorators' => array('Composite'),
            'class'      => 'textarea large-input'
        ));
        
		
        $this->addElement('select', 'panel_id', array(
            'filters'    => array('StringTrim'),
            'label'      => 'Control Panel',
            'class'      => 'text-input large-input'
        ));
        $this->getElement('panel_id')
                  ->setAllowEmpty(false)
                  ->setMultiOptions(Panels::getListInstalled(true));            
		
        $this->addElement('text', 'datacenter', array(
            'filters'    => array('StringTrim'),
            'required'   => false,
            'label'      => 'Datacenter',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input'
        ));
		
        $this->addElement('text', 'cost', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => 'Server cost',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input'
        ));
		
        $this->addElement('text', 'max_accounts', array(
            'filters'    => array('StringTrim'),
            'required'   => false,
            'label'      => 'Max accounts',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input'
        ));
		
        $this->addElement('text', 'buy_date', array(
            'filters'     => array('StringTrim'),
            'label'       => 'Buy Date',
            'description' => 'Purchase date of this server',
            'decorators'  => array('Composite'),
            'class'       => 'text-input large-input date'
        ));		
		
        $this->addElement('checkbox', 'is_default', array(
            'filters'     => array('StringTrim'),
            'required'    => false,
            'label'       => 'Default server',
            'description' => 'Default server for the group',
            'decorators'  => array('Composite'),
            'class'       => 'text-input large-input'
        ));

        $this->addElement('hidden', 'server_id');
    }
}