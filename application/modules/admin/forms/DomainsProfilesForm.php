<?php
class Admin_Form_DomainsProfilesForm extends Zend_Form
{   
    public function init()
    {
        // Set the custom decorator
    	$this->addElementPrefixPath('Shineisp_Decorator', 'Shineisp/Decorator/', 'decorator');
    	$translate = Shineisp_Registry::get('Zend_Translate');
    	

    	$this->addElement('select', 'customer_id', array(
    	        'label' => $translate->_('Owner of the domain profile'),
    	        'description' => $translate->_('This is the customer who has created the domain profiles.'),
    	        'decorators' => array('Bootstrap'),
    	        'class'      => 'form-control'
    	));
    	
    	$this->getElement('customer_id')
                        	->setAllowEmpty(false)
                        	->setMultiOptions(Customers::getList())
                        	->setRequired(true);
    	
    	$this->addElement('text', 'firstname', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => $translate->_('Firstname'),
            'decorators' => array('Bootstrap'),
            'class'      => 'form-control'
        ));
        
        $this->addElement('text', 'lastname', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => $translate->_('Lastname'),
            'decorators' => array('Bootstrap'),
            'class'      => 'form-control'
        ));
        
        $this->addElement('select', 'gender', array(
        'label' => $translate->_('Gender'),
        'decorators' => array('Bootstrap'),
        'class'      => 'form-control'
        ));
        
        $this->getElement('gender')
                  ->setAllowEmpty(true)
                  ->setMultiOptions(array('M'=>'Man', 'F'=>'Female'));
        
        $this->addElement('text', 'birthdate', array(
            'filters'    => array('StringTrim'),
            'label'      => $translate->_('Birth date'),
            'decorators' => array('Bootstrap'),
            'class'        => 'form-control date'
        ));
        
        $this->addElement('text', 'birthplace', array(
            'filters'    => array('StringTrim'),
            'label'      => $translate->_('Birth place'),
            'decorators' => array('Bootstrap'),
            'class'      => 'form-control'
        ));
        
        $this->addElement('text', 'birthdistrict', array(
            'filters'    => array('StringTrim'),
            'label'      => $translate->_('Birth district'),
            'decorators' => array('Bootstrap'),
            'class'      => 'form-control'
        ));
        
        $this->addElement('text', 'birthcountry', array(
            'filters'    => array('StringTrim'),
            'label'      => $translate->_('Birth country'),
            'decorators' => array('Bootstrap'),
            'class'      => 'form-control'
        ));
        
        $this->addElement('text', 'birthnationality', array(
            'filters'    => array('StringTrim'),
            'label'      => $translate->_('Birth nationality'),
            'decorators' => array('Bootstrap'),
            'class'      => 'form-control'
        ));
        
        $this->addElement('select', 'type_id', array(
        'label' => $translate->_('Company Type'),
        'decorators' => array('Bootstrap'),
        'class'      => 'form-control'
        ));
        
        $this->getElement('type_id')
                  ->setAllowEmpty(true)
                  ->setMultiOptions(CompanyTypes::getList(true));
        
        $this->addElement('select', 'legalform_id', array(
        'label' => 'Legal form',
        'decorators' => array('Bootstrap'),
        'class'      => 'form-control'
        ));
        
        $this->getElement('legalform_id')
                  ->setAllowEmpty(true)
                  ->setMultiOptions(Legalforms::getList());
        
        $this->addElement('text', 'company', array(
            'filters'    => array('StringTrim'),
            'label'      => $translate->_('Company Name'),
            'decorators' => array('Bootstrap'),
            'class'      => 'form-control'
        ));

        $this->addElement('text', 'vat', array(
            'filters'    => array('StringTrim'),
            'label'      => $translate->_('VAT Number'),
            'decorators' => array('Bootstrap'),
            'class'      => 'form-control',
        ));
        
        $this->addElement('text', 'taxpayernumber', array(
            'filters'    => array('StringTrim'),
            'label'      => $translate->_('Taxpayer Number'),
            'decorators' => array('Bootstrap'),
            'class'      => 'form-control',
        ));
        
        $this->addElement('text', 'address', array(
            'filters'    => array('StringTrim'),
            'label'      => $translate->_('Address'),
            'decorators' => array('Bootstrap'),
            'class'      => 'form-control'
        ));
        
        $this->addElement('text', 'zip', array(
            'filters'    => array('StringTrim'),
            'label'      => $translate->_('Zip code'),
            'decorators' => array('Bootstrap'),
            'class'      => 'form-control'
        ));
        
        $this->addElement('text', 'area', array(
            'filters'    => array('StringTrim'),
            'label'      => $translate->_('Area'),
            'decorators' => array('Bootstrap'),
            'class'      => 'form-control'
        ));        
        
        $this->addElement('text', 'city', array(
            'filters'    => array('StringTrim'),
            'label'      => $translate->_('City'),
            'decorators' => array('Bootstrap'),
            'class'      => 'form-control'
        ));
        
        
        $this->addElement('select', 'country_id', array(
        		'label' => $translate->_('Country'),
        		'decorators' => array('Bootstrap'),
        		'class'      => 'form-control',
        		'onchange'   => 'onChangeCountry( this );')
        );
        
        
        $this->getElement('country_id')
				        ->setAllowEmpty(false)
				        ->setMultiOptions(Countries::getList( true ))
				        ->setRequired(true);
        
        $this->addElement('text', 'phone', array(
            'filters'    => array('StringTrim'),
            'label'      => $translate->_('Phone'),
            'decorators' => array('Bootstrap'),
            'class'      => 'form-control'
        ));
        
        $this->addElement('text', 'fax', array(
            'filters'    => array('StringTrim'),
            'label'      => $translate->_('Fax'),
            'decorators' => array('Bootstrap'),
            'class'      => 'form-control'
        ));
        
        $this->addElement('text', 'email', array(
            'filters'    => array('StringTrim', 'StringToLower'),
            'decorators' => array('Bootstrap'),
            'validators' => array(
                'EmailAddress'
            ),
            'required'   => true,
            'label'      => $translate->_('Email'),
            'class'      => 'form-control'
        ));
        
        $this->addElement('select', 'status_id', array(
        'label' => $translate->_('Status'),
        'decorators' => array('Bootstrap'),
        'class'      => 'form-control'
        ));
        
        $this->getElement('status_id')
                  ->setAllowEmpty(false)
                  ->setMultiOptions(Statuses::getList('customers'));
        
                        
        $this->addElement('hidden', 'profile_id');
    }
}