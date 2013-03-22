<?php
class Admin_Form_DomainstldsForm extends Zend_Form
{   
    public function init()
    {
        // Set the custom decorator
    	$this->addElementPrefixPath('Shineisp_Decorator', 'Shineisp/Decorator/', 'decorator');
    	
        $this->addElement('text', 'name', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => 'TLD Name',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input'
        ));
    	
        $this->addElement('textarea', 'description', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => 'Description',
            'decorators' => array('Composite'),
            'class'      => 'textarea large-input wysiwyg',
            'rows'      => '5'
        ));
    	
        $this->addElement('text', 'tags', array(
            'filters'    => array('StringTrim'),
            'label'      => 'Tags/Type',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input'
        ));
        
        $this->addElement('select', 'ishighlighted', array(
            'label'      => 'Is Highlighted',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input',
            'multioptions' => array('0' => 'No', '1'=>'Yes')
        ));
        
        $this->addElement('text', 'resultcontrol', array(
            'filters'    => array('StringTrim'),
            'label'      => 'Result String Control',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input'
        ));

        $this->addElement('text', 'registration_price', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => 'Registration Price',
            'decorators' => array('Composite'),
            'class'      => 'text-input little-input'
        ));        

        $this->addElement('text', 'renewal_price', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => 'Renewal Price',
            'decorators' => array('Composite'),
            'class'      => 'text-input little-input'
        ));        

        $this->addElement('text', 'transfer_price', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => 'Transfer Price',
            'decorators' => array('Composite'),
            'class'      => 'text-input little-input'
        ));        

        $this->addElement('select', 'server_id', array(
            'decorators'  => array('Composite'),
            'label'       => 'TLD Server',
            'class'       => 'text-input large-input'
        ));
                
        $this->getElement('server_id')
                  ->setAllowEmpty(false)
                  ->setRegisterInArrayValidator(false)
                  ->setMultiOptions(WhoisServers::getList()); 
                  
       $this->addElement('select', 'tax_id', array(
        'label' => 'Tax',
        'decorators' => array('Composite'),
        'class'      => 'text-input large-input'
        ));
        
        $this->getElement('tax_id')
                  ->setAllowEmpty(false)
                  ->setMultiOptions(Taxes::getList(true));                        
        
        $this->addElement('text', 'registration_cost', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => 'Registration Cost',
            'decorators' => array('Composite'),
            'class'      => 'text-input little-input'
        ));        

        $this->addElement('text', 'renewal_cost', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => 'Renewal Cost',
            'decorators' => array('Composite'),
            'class'      => 'text-input little-input'
        ));        

        $this->addElement('text', 'transfer_cost', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => 'Transfer Cost',
            'decorators' => array('Composite'),
            'class'      => 'text-input little-input'
        ));        
        
        $this->addElement('select', 'registrars_id', array(
                'label' => 'Registrars',
                'decorators' => array('Composite'),
                'class'      => 'text-input large-input updatechkdomain'
        ));
        
        $this->getElement('registrars_id')
                  ->setAllowEmpty(false)
                  ->setMultiOptions(Registrars::getList())
                  ->setRequired(true);        
        
        $this->addElement('submit', 'save', array(
            'required' => false,
            'label'    => 'Save',
            'decorators' => array('Composite'),
            'class'    => 'button'
        ));
        
        $this->addElement('hidden', 'tld_id');
    }
}