<?php
class Api_Form_CustomerForm extends Zend_Form
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
        
        $this->addElement('text', 'birthdate', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'validators' => array(new Zend_Validate_Date('dd/mm/yyyy','it')),
            'label'      => 'Birthdate',
            'description'  => 'Write here your birthday (eg. dd/mm/yyyy)',
            'decorators' => array('Composite'),
            'class'        => 'text-input medium-input'
        ));
        
        $this->addElement('text', 'birthplace', array(
            'filters'    => array('StringTrim'),
            'label'      => 'Birthplace',
            'required'   => true,
            'description'      => 'Write here the birthplace.',
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
                  ->setMultiOptions(Legalforms::getList(true))
                  ->addValidator( new Shineisp_Validate_Customer( ) );
        
        $this->addElement('text', 'company', array(
            'filters'    => array('StringTrim'),
            'label'      => 'Company Name',
            'decorators' => array('Composite'),
            'description'=> 'Write here your company name.',
            'class'      => 'text-input large-input'
        ));
        
        $this->addElement('text', 'birthplace', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => 'Birthplace',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input'
        ));
        
        $this->addElement('text', 'birthdistrict', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => 'Birth District',
            'decorators' => array('Composite'),
            'class'      => 'text-input medium-input',
            'maxlength' => 2
        ));
        
        $this->addElement('text', 'birthcountry', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => 'Country of Birth',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input'
        ));
        
        $this->addElement('text', 'birthnationality', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => 'Birth Nationality',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input'
        ));
        
        #$vatValidator = new Shineisp_Validate_Vat();
        $this->addElement('text', 'vat', array(
            'filters'    => array('StringTrim'),
            'label'      => 'VAT Number',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input',
            'description'      => 'Write here the VAT code. Eg: IT123456789'
        ));
        #$this->getElement('vat')->addValidator($vatValidator);
        
        $this->addElement('text', 'area', array(
            'filters'    => array('StringTrim'),
            'label'      => 'Area',
            'decorators' => array('Composite'),
            'class'      => 'text-input medium-input',
            'description'      => 'Write the area code'
        ));        
        
        $fiscalcodeValidator = new Shineisp_Validate_Fiscalcode();
        $this->addElement('text', 'taxpayernumber', array(
            'filters'    => array('StringTrim'),
            'label'      => 'Tax payer number',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input',
            'description'      => 'Write the tax payer number.'
        ));
        $this->getElement('taxpayernumber')->addValidator($fiscalcodeValidator);
        
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
                  
        $this->addElement('select', 'sex', array(
        'label' => 'Sex',
        'required'   => true,
        'decorators' => array('Composite'),
        'class'      => 'text-input medium-input'));
        
        $this->getElement('sex')
                  ->setAllowEmpty(false)
                  ->setMultiOptions(array('M'=>'M', 'F'=>'F'))
                  ->setRequired(true);                  
                  
        $this->addElement('text', 'telephone', array(
            'filters'    => array('StringTrim'),
            'label'      => 'Telephone',
            'required'   => true,
            'decorators' => array('Composite'),
            'description'      => 'Write here the contact (eg. +39.98368276)',
            'class'      => 'text-input medium-input'
        ));
        
        
        $this->addElement('text', 'email', array(
            'filters'    => array('StringTrim', 'StringToLower'),
            'required'   => true,
            'decorators' => array('Composite'),
            'validators' => array(
                'EmailAddress'
            ),
            'required'   => true,
            'label'      => 'Email',
            'description'      => 'Write here your email',
            'class'      => 'text-input large-input'
        ));
        $mailValidator = new Shineisp_Validate_Email();
        $this->getElement('email')->addValidator($mailValidator);
                
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
        
        
        $this->addElement('submit', 'submit', array(
            'required' => false,
            'label'    => 'Save',
            'decorators' => array('Composite'),
            'class'    => 'button'
        ));
        
        $this->addElement('hidden', 'customer_id');
    }
}