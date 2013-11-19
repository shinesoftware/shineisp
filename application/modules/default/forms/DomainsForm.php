<?php
class Default_Form_DomainsForm extends Zend_Form
{
    
    public function init()
    {
    	// Set the custom decorator
        $this->addElementPrefixPath('Shineisp_Decorator', 'Shineisp/Decorator/', 'decorator');
        $translate = Shineisp_Registry::get('Zend_Translate');
        
        $this->addElement('textarea', 'note', array(
            'filters'     => array('StringTrim'),
            'required'    => false,
            'decorators'  => array('Bootstrap'),
            'rows'       => 5,
            'description' => $translate->_('Write here a message for the administrator about this domain.'),
            'class'       => 'form-control'
        ));
        
        $this->addElement('text', 'tags', array(
            'filters'     => array('StringTrim'),
            'required'    => false,
            'decorators'  => array('Bootstrap'),
            'label'      => $translate->_('Tags'),
            'class'       => 'form-control large-input tags'
        ));
        
        $this->addElement('text', 'authinfocode', array(
            'filters'     => array('StringTrim'),
            'required'    => false,
            'decorators'  => array('Bootstrap'),
            'label'      => $translate->_('Authinfo'),
            'class'       => 'form-control medium-input'
        ));
        
        $this->addElement('select', 'autorenew', array(
            'filters'     => array('StringTrim'),
            'decorators'  => array('Bootstrap'),
            'label'      => $translate->_('Autorenew'),
            'description' => $translate->_('By default, every domain is set with auto-renew option enabled. Choose if the domain must be auto-renew or not at the expiring date.'),
            'class'       => 'form-control large-input'
        ));
        
        $this->getElement('autorenew')
                  ->setAllowEmpty(false)
                  ->setMultiOptions(array('1'=> $translate->_('Yes, I would like to renew the domain at the expiration date.'), '0'=> $translate->_('No, I am not interested in the renew.')));

        $status = $this->addElement('select', 'status_id', array(
                'label' => $translate->_('Status'),
                'decorators' => array('Bootstrap'),
                'class'      => 'form-control large-input'
                ));
        
        $status = $this->getElement('status_id')
                  ->setAllowEmpty(false)
                  ->setRegisterInArrayValidator(false)
                  ->setMultiOptions(Statuses::getList('domains'));        
                  
        $this->addElement('submit', 'submit', array(
            'label'      => $translate->_('Save'),
            'decorators' => array('Bootstrap'),
            'class'    => 'btn btn-primary btn-lg'
        ));
        
        // Adding a subform
        $dnsform = new Zend_Form_SubForm ();
        
        // Set the decorator
        $dnsform->addElementPrefixPath('Shineisp_Decorator', 'Shineisp/Decorator/', 'decorator');
        
        $dnsform->addElement('text', 'subdomain', array('filters'     => array('StringTrim'),
										        		'decorators'  => array('Bootstrap'),
										        		'label'      => $translate->_('Subdomain'),
										        		'class'       => 'form-control medium-input'));
										        
        $dnsform->addElement('select', 'zones', array(
								        		'filters'     => array('StringTrim'),
								        		'decorators'  => array('Bootstrap'),
								        		'label'      => $translate->_('DNS Type Zone'),
								        		'class'       => 'form-control large-input'));
        
        $dnsform->getElement('zones')->setAllowEmpty(false)->setMultiOptions(Dns_Zones_Types::getZones());
                
        $dnsform->addElement('text', 'target', array('filters'     => array('StringTrim'),
									        		'decorators'  => array('Bootstrap'),
									        		'label'      => $translate->_('Target Address'),
									        		'class'       => 'form-control large-input'));
                
        $this->addSubForm ( $dnsform, 'dnsform' );
        
        
        $id = $this->addElement('hidden', 'domain_id');

    }
    
}