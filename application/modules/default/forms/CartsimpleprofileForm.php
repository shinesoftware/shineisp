<?php
class Default_Form_CartsimpleprofileForm extends Zend_Form
{   
    public function init()
    {
        // Set the custom decorator
    	$this->addElementPrefixPath('Shineisp_Decorator', 'Shineisp/Decorator/', 'decorator');
    	
    	$this->addElement('text', 'firstname', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => 'Firstname',
            'description' => 'Write here your firstname.',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input'
        ));
        
        $this->addElement('text', 'lastname', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => 'Lastname',
            'description'      => 'Write here your lastname.',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input'
        ));
        
        $this->addElement('select', 'company_type_id', array(
        'label' => 'Company Type',
        'decorators' => array('Composite'),
        'description'      => 'Select the company type',
        'class'      => 'text-input large-input'
        ));
        
        $this->getElement('company_type_id')
                  ->setAllowEmpty(false)
                  ->setMultiOptions(CompanyTypes::getList(true));
        
        $this->addElement('select', 'legalform', array(
        'label' => 'Legalform',
        'required'   => true,
        'decorators' => array('Composite'),
        'description'      => 'Select the type of company.',
        'class'      => 'text-input large-input'
        ));
        
        $this->getElement('legalform')
                  ->setAllowEmpty(false)
                  ->setMultiOptions(Legalforms::getList());
        
        $this->addElement('text', 'company', array(
            'filters'    => array('StringTrim'),
            'label'      => 'Company Name',
            'decorators' => array('Composite'),
            'description'      => 'Write here your company name.',
            'class'      => 'text-input large-input'
        ));
        
        $this->addElement('text', 'vat', array(
            'filters'    => array('StringTrim'),
            'label'      => 'VAT',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input',
            'description'      => 'Write here the VAT number.'
        ));
        
        $this->addElement('text', 'area', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => 'Area',
            'decorators' => array('Composite'),
            'class'      => 'text-input medium-input',
            'description'      => 'Write the area code'
        ));        
        
        $this->addElement('text', 'taxpayernumber', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => 'Tax payer number',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input',
            'description'      => 'Write the tax payer number.'
        ));
        
        $this->addElement('text', 'address', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => 'Address',
            'decorators' => array('Composite'),
            'description'      => 'Write the address',
            'class'      => 'text-input large-input'
        ));
        
        $this->addElement('text', 'code', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => 'Zip',
            'description'      => 'Write the zip code',
            'decorators' => array('Composite'),
            'class'      => 'text-input medium-input'
        ));
        
        $this->addElement('text', 'city', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => 'City',
            'description'      => 'Write here your city name',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input'
        ));
        
        $this->addElement('select', 'country_id', array(
		        'label' => 'Country',
		        'required'   => true,
		        'description'      => 'Select your own country',
		        'decorators' => array('Composite'))
        );
        $this->getElement('country_id')
                  ->setAllowEmpty(false)
                  ->setMultiOptions(Countries::getList())
                  ->setRequired(true);
             
        $this->addElement('text', 'email', array(
            'filters'    => array('StringTrim', 'StringToLower'),
            'required'   => true,
            'decorators' => array('Composite'),
            'validators' => array(
                'EmailAddress',
            ),
            'required'   => true,
            'label'      => 'Email',
            'description'      => 'Write here your email',
            'class'      => 'text-input large-input'
        ));
                
        $this->addElement('password', 'password', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'decorators' => array('Composite'),
            'validators' => array(
                array('regex', false, '/^[a-zA-Z0-9\-\_\.\%\!\$]{6,20}$/')
            ),
            'description'      => 'Write here your password. (min.6 chars - max.20 chars)',
            'label'      => 'Password',
            'class'      => 'text-input large-input'
        ));
        
        
        $this->addElement('submit', 'save', array(
            'required' => false,
            'label'    => 'Save',
            'decorators' => array('Composite'),
            'class'    => 'button'
        ));
        
        $this->addElement('hidden', 'customer_id');
    }
}