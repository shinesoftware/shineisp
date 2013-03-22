<?php
class Admin_Form_DomainsForm extends Zend_Form
{
    
    public function init()
    {
        // Set the custom decorator
        $this->addElementPrefixPath('Shineisp_Decorator', 'Shineisp/Decorator/', 'decorator');
        
        $this->addElement('text', 'domain', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'decorators' => array('Composite'),
            'label'      => 'Domain',
            'description' => 'Write down the name of the domain without any extension, white spaces, or symbols.',
            'class'      => 'text-input large-input updatechkdomain'
        ));
      
        $this->addElement('select', 'tld_id', array(
                'label' => 'TLD',
                'description' => 'Select the TLD from the list',
                'decorators' => array('Composite'),
                'class'      => 'text-input large-input updatechkdomain'
        ));
        $this->getElement('tld_id')
                  ->setAllowEmpty(false)
                  ->setMultiOptions(DomainsTlds::getList())
                  ->setRequired(true);
                  
        $this->addElement('select', 'registrars_id', array(
                'label' => 'Registrar',
                'decorators' => array('Composite'),
                'class'      => 'text-input large-input'
        ));
        $this->getElement('registrars_id')
                ->setAllowEmpty(true)
                ->setMultiOptions(Registrars::getList());
                  
        $this->addElement('text', 'creation_date', array(
            'filters'    => array('StringTrim'),
            'label'      => 'Creation date',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input date'
        ));
        
        $this->addElement('text', 'expiring_date', array(
            'filters'    => array('StringTrim'),
            'label'      => 'Expiring Date',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input date'
        ));
        
        $this->addElement('text', 'authinfocode', array(
            'filters'    => array('StringTrim'),
            'label'      => 'AUTHINFO CODE',
            'description'      => 'Write down the Authinfo code in order to transfer the domain to this ISP',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input'
        ));
      
        $this->addElement('select', 'autorenew', array(
            'filters'     => array('StringTrim'),
            'decorators'  => array('Composite'),
            'label'       => 'Autorenew',
            'class'       => 'text-input large-input'
        ));
        
        $this->getElement('autorenew')
                  ->setAllowEmpty(false)
                  ->setMultiOptions(array('0'=>'no', '1'=>'yes'));        
        
        $this->addElement('select', 'customer_id', array(
                            'label' => 'Customer',
                            'decorators' => array('Composite'),
                            'class'      => 'text-input large-input'
        ));
        
        $this->getElement('customer_id')
                  ->setAllowEmpty(false)
                  ->setMultiOptions(Customers::getList())
                  ->setRequired(true); 
//                  
        $note = $this->addElement('textarea', 'note', array(
            'filters'    => array('StringTrim'),
            'required'   => false,
            'decorators' => array('Composite'),
            'label'      => 'Private Notes',
            'class'      => 'textarea'
        ));
        
        $note = $this->addElement('textarea', 'message', array(
            'filters'    => array('StringTrim'),
            'required'   => false,
            'decorators' => array('Composite'),
            'label'      => 'Message',
            'class'      => 'textarea'
        ));        

        $status = $this->addElement('select', 'status_id', array(
        'label' => 'Status',
        'required'    => true,
        'decorators' => array('Composite'),
        'class'      => 'text-input large-input'
        ));
        
        // DNS SECTION
        // ==============
         $this->addElement('text', 'subdomain', array(
            'filters'    => array('StringTrim'),
            'decorators' => array('Composite'),
            'label'      => 'Subdomain',
            'class'      => 'text-input large-input'
        ));
        
        $this->addElement('text', 'target', array(
            'filters'    => array('StringTrim'),
            'decorators' => array('Composite'),
            'label'      => 'Target',
            'class'      => 'text-input large-input'
        ));
        
        $this->addElement('select', 'zone', array(
                'label' => 'Zone',
                'decorators' => array('Composite'),
                'class'      => 'text-input large-input'
        ));
        
        $this->getElement('zone')
                  ->setAllowEmpty(false)
                  ->setMultiOptions(Dns_Zones_Types::getZones());
        
      
        
        $status = $this->getElement('status_id')
                  ->setAllowEmpty(false)
                  ->setMultiOptions(Statuses::getList('domains'));
                  
        $save = $this->addElement('submit', 'save', array(
            'required' => false,
            'label'    => 'Save',
            'decorators' => array('Composite'),
            'class'    => 'button'
        ));
        
        $id = $this->addElement('hidden', 'domain_id');

    }
    
}