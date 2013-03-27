<?php
class Admin_Form_IspForm extends Zend_Form
{   
    public function init()
    {
        // Set the custom decorator
    	$this->addElementPrefixPath('Shineisp_Decorator', 'Shineisp/Decorator/', 'decorator');
    	
    	$this->addElement('text', 'company', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => 'Company',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input'
        ));
        
        $this->addElement('text', 'vatnumber', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => 'VAT Number',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input'
        ));
        
        $this->addElement('text', 'address', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => 'Address',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input'
        ));

        $this->addElement('text', 'zip', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => 'ZIP Code',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input'
        ));

        $this->addElement('text', 'city', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => 'City',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input'
        ));

        $this->addElement('text', 'country', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => 'Country',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input'
        ));

        $this->addElement('text', 'telephone', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => 'Telephone',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input'
        ));

        $this->addElement('text', 'fax', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => 'Fax',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input'
        ));     

        $this->addElement('text', 'bankname', array(
            'filters'    => array('StringTrim'),
            'label'      => 'Bankname',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input'
        ));     

        $this->addElement('text', 'iban', array(
            'filters'    => array('StringTrim'),
            'label'      => 'IBAN',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input'
        ));     

        $this->addElement('text', 'bic', array(
            'filters'    => array('StringTrim'),
            'label'      => 'BIC',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input'
        ));     

        $this->addElement('text', 'slogan', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => 'Slogan',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input'
        ));     

        $this->addElement('text', 'manager', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => 'Manager',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input'
        ));             
        
        $this->addElement('text', 'website', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => 'Website',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input'
        ));
        
        $this->addElement('text', 'email', array(
            'filters'    => array('StringTrim', 'StringToLower'),
            'decorators' => array('Composite'),
            'validators' => array(
                'EmailAddress',
            ),
            'required'   => true,
            'label'      => 'Email',
            'class'      => 'text-input large-input'
        ));
        
                
        $this->addElement('file', 'logo', array(
            'label'      => 'Logo',
            'class'      => 'text-input large-input'
        ));
        
        $this->addElement('password', 'password', array(
            'filters'    => array('StringTrim'),
            'decorators' => array('Composite'),
            'validators' => array(
                array('regex', false, '/^[a-zA-Z0-9\-\_\.\%\!\$]{6,20}$/')
            ),
            'label'      => 'Password',
            'class'      => 'text-input large-input'
        ));
        
        $this->addElement('select', 'isppanel', array(
            'filters'    => array('StringTrim'),
            'label'      => 'ISP Panel',
            'class'      => 'text-input large-input'
        ));
        
        $this->getElement('isppanel')
                  ->setAllowEmpty(false)
                  ->setMultiOptions(Panels::getPanelInstalled());            

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
        
        $this->addElement('hidden', 'isp_id');
    }
}