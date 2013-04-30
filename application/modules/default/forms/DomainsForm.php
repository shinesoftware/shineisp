<?php
class Default_Form_DomainsForm extends Zend_Form
{
    
    public function init()
    {
    	// Set the custom decorator
        $this->addElementPrefixPath('Shineisp_Decorator', 'Shineisp/Decorator/', 'decorator');
        
        $this->addElement('textarea', 'note', array(
            'filters'     => array('StringTrim'),
            'required'    => false,
            'decorators'  => array('Composite'),
            'label'       => 'Your message',
            'rows'       => 5,
            'description' => 'Write here a message for the administrator about this domain.',
            'class'       => 'textarea'
        ));
        
        $this->addElement('text', 'tags', array(
            'filters'     => array('StringTrim'),
            'required'    => false,
            'decorators'  => array('Composite'),
            'label'       => 'Tags',
            'class'       => 'text-input large-input tags'
        ));
        
        $this->addElement('text', 'authinfocode', array(
            'filters'     => array('StringTrim'),
            'required'    => false,
            'decorators'  => array('Composite'),
            'label'       => 'AuthInfo',
            'class'       => 'text-input medium-input'
        ));
        
        $this->addElement('select', 'autorenew', array(
            'filters'     => array('StringTrim'),
            'decorators'  => array('Composite'),
            'label'       => 'Autorenew',
            'description' => 'By default, all domains are set to auto-renew. Choose if the domain must be auto-renew or not at the expiring date.',
            'class'       => 'text-input large-input'
        ));
        
        $this->getElement('autorenew')
                  ->setAllowEmpty(false)
                  ->setMultiOptions(array('1'=>'Yes, I would like to renew the domain at the expiration date.', '0'=>'No, I am not interested in the renew.'));

        $status = $this->addElement('select', 'status_id', array(
        'label' => 'Status',
        'decorators' => array('Composite'),
        'class'      => 'text-input large-input'
        ));
        
        $status = $this->getElement('status_id')
                  ->setAllowEmpty(false)
                  ->setRegisterInArrayValidator(false)
                  ->setMultiOptions(Statuses::getList('domains'));        
                  
        $this->addElement('submit', 'submit', array(
            'label'    => 'Save',
            'decorators' => array('Composite'),
            'class'    => 'button'
        ));
        
        // Adding a subform
        $dnsform = new Zend_Form_SubForm ();
        
        // Set the decorator
        $dnsform->addElementPrefixPath('Shineisp_Decorator', 'Shineisp/Decorator/', 'decorator');
        
        $dnsform->addElement('text', 'subdomain', array('filters'     => array('StringTrim'),
										        		'decorators'  => array('Composite'),
										        		'label'       => 'Subdomain',
										        		'class'       => 'text-input medium-input'));
										        
        $dnsform->addElement('select', 'zones', array(
								        		'filters'     => array('StringTrim'),
								        		'decorators'  => array('Composite'),
								        		'label'       => 'DNS Type Zone',
								        		'class'       => 'text-input large-input'));
        
        $dnsform->getElement('zones')->setAllowEmpty(false)->setMultiOptions(Dns_Zones_Types::getZones());
                
        $dnsform->addElement('text', 'target', array('filters'     => array('StringTrim'),
									        		'decorators'  => array('Composite'),
									        		'label'       => 'Target Address',
									        		'class'       => 'text-input large-input'));
                
        $this->addSubForm ( $dnsform, 'dnsform' );
        
        
        $id = $this->addElement('hidden', 'domain_id');

    }
    
}