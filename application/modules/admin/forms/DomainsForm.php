<?php
class Admin_Form_DomainsForm extends Zend_Form
{
    
    public function init()
    {
        // Set the custom decorator
        $this->addElementPrefixPath('Shineisp_Decorator', 'Shineisp/Decorator/', 'decorator');
        $translate = Shineisp_Registry::get('Zend_Translate');
        
        $this->addElement('text', 'domain', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'decorators' => array('Bootstrap'),
            'label'      => $translate->_('Domain'),
            'description' => $translate->_('Write down the name of the domain without any extension, white spaces, or symbols.'),
            'class'      => 'input-large updatechkdomain'
        ));
      
        $this->addElement('select', 'tld_id', array(
                'label' => $translate->_('TLD'),
                'description' => $translate->_('Select the TLD from the list'),
                'decorators' => array('Bootstrap'),
                'class'      => 'input-large updatechkdomain'
        ));
        $this->getElement('tld_id')
                  ->setAllowEmpty(false)
                  ->setMultiOptions(DomainsTlds::getList())
                  ->setRequired(true);
                  
        $this->addElement('select', 'registrars_id', array(
                'label' => $translate->_('Registrar'),
                'decorators' => array('Bootstrap'),
                'class'      => 'input-large'
        ));
        $this->getElement('registrars_id')
                ->setAllowEmpty(true)
                ->setMultiOptions(Registrars::getList());
                  
        $this->addElement('text', 'creation_date', array(
            'filters'    => array('StringTrim'),
            'label'      => $translate->_('Creation date'),
            'decorators' => array('Bootstrap'),
            'class'      => 'input-large date'
        ));
        
        $this->addElement('text', 'expiring_date', array(
            'filters'    => array('StringTrim'),
            'label'      => $translate->_('Expiry Date'),
            'decorators' => array('Bootstrap'),
            'class'      => 'input-large date'
        ));
        
        $this->addElement('text', 'authinfocode', array(
            'filters'    => array('StringTrim'),
            'label'      => $translate->_('AUTHINFO CODE'),
            'description'      => $translate->_('Write down the Authinfo code in order to transfer the domain to this ISP'),
            'decorators' => array('Bootstrap'),
            'class'      => 'input-large'
        ));
      
        $this->addElement('select', 'autorenew', array(
            'filters'     => array('StringTrim'),
            'decorators'  => array('Bootstrap'),
            'label'       => $translate->_('Auto renewal'),
            'class'       => 'input-large'
        ));
        
        $this->getElement('autorenew')
                  ->setAllowEmpty(false)
                  ->setMultiOptions(array('0'=>'no', '1'=>'yes'));        
        
        $this->addElement('select', 'customer_id', array(
                            'label' => $translate->_('Customer'),
                            'decorators' => array('Bootstrap'),
                            'class'      => 'input-large'
        ));
        
        $this->getElement('customer_id')
                  ->setAllowEmpty(false)
                  ->setMultiOptions(Customers::getList())
                  ->setRequired(true); 
//                  
        $note = $this->addElement('textarea', 'note', array(
            'filters'    => array('StringTrim'),
            'decorators' => array('Bootstrap'),
            'label'      => $translate->_('Private Notes'),
            'class'      => 'span12'
        ));
        
        $note = $this->addElement('textarea', 'message', array(
            'filters'    => array('StringTrim'),
            'required'   => false,
            'decorators' => array('Bootstrap'),
            'label'      => $translate->_('Message'),
            'class'      => 'span12 wysiwyg'
        ));        

        $status = $this->addElement('select', 'status_id', array(
        'label' => 'Status',
        'required'    => true,
        'decorators' => array('Bootstrap'),
        'class'      => 'input-large'
        ));
        
        // DNS SECTION
        // ==============
         $this->addElement('text', 'subdomain', array(
            'filters'    => array('StringTrim'),
            'decorators' => array('Bootstrap'),
            'label'      => $translate->_('Subdomain'),
            'class'      => 'input-large'
        ));
        
        $this->addElement('text', 'target', array(
            'filters'    => array('StringTrim'),
            'decorators' => array('Bootstrap'),
            'label'      => $translate->_('Target'),
            'class'      => 'input-large'
        ));
        
        $this->addElement('select', 'zone', array(
                'label' => $translate->_('Zone'),
                'decorators' => array('Bootstrap'),
                'class'      => 'input-large'
        ));
        
        $this->getElement('zone')
                  ->setAllowEmpty(false)
                  ->setMultiOptions(Dns_Zones_Types::getZones());
        
        $status = $this->getElement('status_id')
                  ->setAllowEmpty(false)
                  ->setMultiOptions(Statuses::getList('domains'));
                  
        $save = $this->addElement('submit', 'save', array(
            'required' => false,
            'label'    => $translate->_('Save'),
            'decorators' => array('Bootstrap'),
            'class'    => 'btn'
        ));
        
        $id = $this->addElement('hidden', 'domain_id');

    }
    
}