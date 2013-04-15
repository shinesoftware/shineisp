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
                'description'      => 'desc_isp',
                'class'      => 'text-input large-input'
        ));
        
        $this->getElement('isp_id')
                  ->setAllowEmpty(false)
                  ->setMultiOptions(Isp::getList())
                  ->setRequired(true);
                  
        $this->addElement('select', 'type_id', array(
                'label' => 'servertype',
                'decorators' => array('Composite'),
                'description'      => 'desc_servertype',
                'class'      => 'text-input large-input'
        ));
        
        $this->getElement('type_id')
                  ->setAllowEmpty(false)
                  ->setMultiOptions(Servers_Types::getList())
                  ->setRequired(true);
                  
        $this->addElement('select', 'status_id', array(
                'label' => 'status',
                'decorators' => array('Composite'),
                'description'      => 'desc_status',
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
            'description' => 'desc_servername',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input'
        ));
        
        $this->addElement('text', 'ip', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => 'ip',
            'description'      => 'desc_ip',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input'
        ));
        
        $this->addElement('text', 'netmask', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => 'netmask',
            'description'      => 'desc_netmask',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input'
        ));
        
        $this->addElement('text', 'host', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => 'host',
            'description'      => 'desc_host',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input'
        ));
        
        $this->addElement('text', 'domain', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => 'domain',
            'description'      => 'desc_domain',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input'
        ));
        
        $this->addElement('textarea', 'description', array(
            'required'   => true,
            'label'      => 'description',
            'description'      => 'desc_description',
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
            'description'      => 'desc_datacenter',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input'
        ));
		
        $this->addElement('text', 'cost', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => 'Server cost',
            'description'      => 'desc_cost',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input'
        ));
		
        $this->addElement('text', 'max_accounts', array(
            'filters'    => array('StringTrim'),
            'required'   => false,
            'label'      => 'Max accounts',
            'description'      => 'desc_maxaccounts',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input'
        ));
		

        $this->addElement('submit', 'save', array(
            'required' => false,
            'label'    => 'Save',
            'decorators' => array('Composite'),
            'class'    => 'button'
        ));
        
        $this->addElement('reset', 'reset', array(
            'required' => false,
            'label'    => 'reset',
            'decorators' => array('Composite'),
            'class'    => 'button'
        ));
        
        $this->addElement('hidden', 'server_id');
    }
}