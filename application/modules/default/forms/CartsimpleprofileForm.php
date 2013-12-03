<?php
class Default_Form_CartsimpleprofileForm extends Zend_Form
{   
    public function init()
    {
        // Set the custom decorator
    	$this->addElementPrefixPath('Shineisp_Decorator', 'Shineisp/Decorator/', 'decorator');
    	$translate = Shineisp_Registry::get('Zend_Translate');
    	
    	$this->addElement('text', 'firstname', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => $translate->_('Firstname'),
            'description' => $translate->_('Write here your firstname.'),
            'decorators' => array('Bootstrap'),
            'class'      => 'form-control large-input'
        ));
         
        $this->addElement('text', 'lastname', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => $translate->_('Lastname'),
            'description'      => $translate->_('Write here your lastname.'),
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
        'label' => $translate->_('Legal form'),
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
            'description'      => $translate->_('Write the area code')
        ));        
        
        $this->addElement('text', 'taxpayernumber', array(
            'filters'    => array('StringTrim'),
            'label'      => $translate->_('Tax payer number'),
            'decorators' => array('Bootstrap'),
            'class'      => 'form-control large-input',
            'description'      => $translate->_('Write the tax payer number.')
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
            'description'      => $translate->_('Write the zip code'),
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
        		'class'		 => "form-control"
        ));
        $this->getElement('country_id')
                  ->setAllowEmpty(false)
                  ->setMultiOptions(Countries::getList())
                  ->setRequired(true);
             
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
            'required'   => true,
            'decorators' => array('Bootstrap'),
            'validators' => array(
                array('regex', false, '/^[a-zA-Z0-9\-\_\.\%\!\$]{6,20}$/')
            ),
            'description'      => $translate->_('Write here your password. (min.6 chars - max.20 chars)'),
            'label'      => $translate->_('Password'),
            'class'      => 'form-control large-input'
        ));
        
        $this->addElement('submit', 'save', array(
            'required' => false,
            'label'      => $translate->_('Save'),
            'decorators' => array('Bootstrap'),
            'class'    => 'btn btn-primary'
        ));
        
        $this->addElement('hidden', 'customer_id');
    }
}