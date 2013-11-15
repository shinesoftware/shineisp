<?php
class Default_Form_ProfileForm extends Zend_Form
{   
    public function init()
    {
        // Set the custom decorator
    	$this->addElementPrefixPath('Shineisp_Decorator', 'Shineisp/Decorator/', 'decorator');
    	$translate = Shineisp_Registry::get('Zend_Translate');
    	
    	$this->addElement('text', 'firstname', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => $translate->_('First name'),
            'description' => $translate->_('Write here your first name.'),
            'decorators' => array('Bootstrap'),
            'class'      => 'form-control large-input'
        ));
        
        $this->addElement('text', 'lastname', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => $translate->_('Last name'),
            'description'      => $translate->_('Write here your last name.'),
            'decorators' => array('Bootstrap'),
            'class'      => 'form-control large-input'
        ));
        
        $this->addElement('text', 'birthdate', array(
            'filters'    => array('StringTrim'),
            'validators' => array(new Zend_Validate_Date('dd/mm/yyyy','it')),
            'label'      => $translate->_('Birthdate'),
            'description'  => $translate->_('Write here your birthday (eg. dd/mm/yyyy)'),
            'decorators' => array('Bootstrap'),
            'class'        => 'form-control medium-input'
        ));
        
        $this->addElement('text', 'birthplace', array(
            'filters'    => array('StringTrim'),
            'label'      => $translate->_('Birthplace'),
            'description'      => $translate->_('Write here the birthplace.'),
            'decorators' => array('Bootstrap'),
            'class'      => 'form-control large-input'
        ));
        
        $this->addElement('select', 'company_type_id', array(
        'label' => $translate->_('Company Type'),
        'decorators' => array('Bootstrap'),
        'description'      => $translate->_('Select the company type'),
        'class'      => 'form-control large-input'
        ));
        
        $this->getElement('company_type_id')
                  ->setAllowEmpty(false)
                  ->setMultiOptions(CompanyTypes::getList(true));
        
        $this->addElement('select', 'legalform', array(
        'label' => $translate->_('Legalform'),
        'required'   => true,
        'decorators' => array('Bootstrap'),
        'description'      => $translate->_('Select the type of company.'),
        'class'      => 'form-control large-input'
        ));
        
        $this->getElement('legalform')
                  ->setAllowEmpty(false)
                  ->setMultiOptions(Legalforms::getList());
        
        $this->addElement('text', 'company', array(
            'filters'    => array('StringTrim'),
            'label'      => $translate->_('Company Name'),
            'decorators' => array('Bootstrap'),
            'description'      => $translate->_('Write here your company name.'),
            'class'      => 'form-control large-input'
        ));
        
        $this->addElement('text', 'birthplace', array(
            'filters'    => array('StringTrim'),
            'label'      => $translate->_('Birth place'),
            'decorators' => array('Bootstrap'),
            'class'      => 'form-control large-input'
        ));
        
        $this->addElement('text', 'birthdistrict', array(
            'filters'    => array('StringTrim'),
            'label'      => $translate->_('Birth District'),
            'decorators' => array('Bootstrap'),
            'class'      => 'form-control medium-input',
            'maxlength' => 2
        ));
        
        $this->addElement('text', 'birthcountry', array(
            'filters'    => array('StringTrim'),
            'label'      => $translate->_('Country of Birth'),
            'decorators' => array('Bootstrap'),
            'class'      => 'form-control large-input'
        ));
        
        $this->addElement('text', 'birthnationality', array(
            'filters'    => array('StringTrim'),
            'label'      => $translate->_('Birth Nationality'),
            'decorators' => array('Bootstrap'),
            'class'      => 'form-control large-input'
        ));
        
        $this->addElement('text', 'vat', array(
            'filters'    => array('StringTrim'),
            'label'      => $translate->_('VAT'),
            'decorators' => array('Bootstrap'),
            'class'      => 'form-control large-input',
            'description'      => $translate->_('Write here the VAT number.')
        ));
        
        $this->addElement('text', 'area', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => $translate->_('Area'),
            'decorators' => array('Bootstrap'),
            'class'      => 'form-control medium-input',
            'description'      => 'Write the area code'
        ));        
        
        $this->addElement('text', 'taxpayernumber', array(
            'filters'    => array('StringTrim'),
            'label'      => $translate->_('Tax payer number'),
            'decorators' => array('Bootstrap'),
            'class'      => 'form-control large-input',
            'description'      => 'Write the tax payer number.'
        ));
        
        $this->addElement('text', 'address', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => $translate->_('Address'),
            'decorators' => array('Bootstrap'),
            'description'      => $translate->_('Write the address'),
            'class'      => 'form-control large-input'
        ));
        
        $this->addElement('text', 'code', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => $translate->_('Zip'),
            'description'      => 'Write the zip code',
            'decorators' => array('Bootstrap'),
            'class'      => 'form-control medium-input'
        ));
        
        $this->addElement('text', 'city', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => $translate->_('City'),
            'description'      => $translate->_('Write here your city name'),
            'decorators' => array('Bootstrap'),
            'class'      => 'form-control large-input'
        ));
        
        $this->addElement('select', 'country_id', array(
		        'label' => $translate->_('Country'),
		        'required'   => true,
		        'description'      => $translate->_('Select your own country'),
		        'decorators' => array('Bootstrap'),
                'class'      => 'form-control large-input')
        );
        $this->getElement('country_id')
                  ->setAllowEmpty(false)
                  ->setMultiOptions(Countries::getList())
                  ->setRequired(true);
                  
        $this->addElement('select', 'gender', array(
        'label' => $translate->_('Gender'),
        'required'   => true,
        'decorators' => array('Bootstrap'),
        'class'      => 'form-control medium-input'));
        
        $this->getElement('gender')
                  ->setAllowEmpty(false)
                  ->setMultiOptions(array('M'=>'M', 'F'=>'F'))
                  ->setRequired(true);                  
                  
        $this->addElement('select', 'newsletter', array(
					        'label' => $translate->_('Newsletter'),
					        'description'   => $translate->_('Subscribe to our free content feeds and get all the news for your bought services and products.'),
					        'decorators' => array('Bootstrap'),
					        'class'      => 'form-control medium-input'));
					        
        $this->getElement('newsletter')
                  ->setAllowEmpty(false)
                  ->setMultiOptions(array('0' => $translate->_('No, I am not interested'), '1' => $translate->_('Yes, please send me your updates')))
                  ->setRequired(true);                  
                  
        $this->addElement('select', 'contacttypes', array(
        'label' => $translate->_('Contact Types'),
        'required'   => true,
        'description'      => $translate->_('Select the contact type'),
        'decorators' => array('Bootstrap'),
        'class'      => 'form-control large-input'));
        
        $this->getElement('contacttypes')
                  ->setAllowEmpty(false)
                  ->setMultiOptions(ContactsTypes::getList());
        
        $this->addElement('text', 'contact', array(
            'filters'    => array('StringTrim'),
            'label'      => $translate->_('Contact'),
            'decorators' => array('Bootstrap'),
            'description'      => $translate->_('Write here the contact (eg. +39.98368276)'),
            'class'      => 'form-control medium-input'
        ));
        
        $this->addElement('text', 'email', array(
            'filters'    => array('StringTrim', 'StringToLower'),
            'required'   => true,
            'decorators' => array('Bootstrap'),
            'validators' => array(
                'EmailAddress',
            ),
            'required'   => true,
            'label'      => $translate->_('Email'),
            'description'      => $translate->_('Write here your email'),
            'class'      => 'form-control large-input'
        ));
                
        $this->addElement('password', 'password', array(
            'filters'    => array('StringTrim'),
            'decorators' => array('Bootstrap'),
            'validators' => array(
                array('regex', false, '/^[a-zA-Z0-9\-\_\.\%\!\$]{6,20}$/')
            ),
            'description'      => $translate->_('Write here your password. (min.6 chars - max.20 chars)'),
            'label'      => $translate->_('Password'),
            'class'      => 'form-control large-input'
        ));
        
        
        $this->addElement('submit', 'submit', array(
            'label'      => $translate->_('Save'),
            'decorators' => array('Bootstrap'),
            'class'    => 'btn btn-primary'
        ));
        
        $this->addElement('hidden', 'customer_id');
    }
}