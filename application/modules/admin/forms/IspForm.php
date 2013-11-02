<?php
class Admin_Form_IspForm extends Zend_Form
{   
    public function init()
    {
        // Set the custom decorator
    	$this->addElementPrefixPath('Shineisp_Decorator', 'Shineisp/Decorator/', 'decorator');
    	$translate = Shineisp_Registry::get('Zend_Translate');
    	
    	$this->addElement('text', 'company', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => $translate->_('Company'),
            'decorators' => array('Bootstrap'),
            'class'      => 'form-control input-lg'
        ));
        
        $this->addElement('text', 'vatnumber', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => $translate->_('VAT Number'),
            'decorators' => array('Bootstrap'),
            'class'      => 'form-control input-lg'
        ));
        
        $this->addElement('text', 'address', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => $translate->_('Address'),
            'decorators' => array('Bootstrap'),
            'class'      => 'form-control input-lg'
        ));

        $this->addElement('text', 'zip', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => $translate->_('ZIP Code'),
            'decorators' => array('Bootstrap'),
            'class'      => 'form-control input-lg'
        ));

        $this->addElement('text', 'city', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => $translate->_('City'),
            'decorators' => array('Bootstrap'),
            'class'      => 'form-control input-lg'
        ));

        $this->addElement('text', 'country', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => $translate->_('Country'),
            'decorators' => array('Bootstrap'),
            'class'      => 'form-control input-lg'
        ));

        $this->addElement('text', 'telephone', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => $translate->_('Telephone'),
            'decorators' => array('Bootstrap'),
            'class'      => 'form-control input-lg'
        ));

        $this->addElement('text', 'fax', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => $translate->_('Fax'),
            'decorators' => array('Bootstrap'),
            'class'      => 'form-control input-lg'
        ));     

        $this->addElement('text', 'bankname', array(
            'filters'    => array('StringTrim'),
            'label'      => $translate->_('Bank name'),
            'decorators' => array('Bootstrap'),
            'class'      => 'form-control input-lg'
        ));     

        $this->addElement('text', 'iban', array(
            'filters'    => array('StringTrim'),
            'label'      => $translate->_('IBAN'),
            'decorators' => array('Bootstrap'),
            'class'      => 'form-control input-lg'
        ));     

        $this->addElement('text', 'bic', array(
            'filters'    => array('StringTrim'),
            'label'      => $translate->_('BIC'),
            'decorators' => array('Bootstrap'),
            'class'      => 'form-control input-lg'
        ));     

        $this->addElement('text', 'slogan', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => $translate->_('Slogan'),
            'decorators' => array('Bootstrap'),
            'class'      => 'form-control input-lg'
        ));     

        $this->addElement('text', 'manager', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => $translate->_('Manager'),
            'decorators' => array('Bootstrap'),
            'class'      => 'form-control input-lg'
        ));             

        $this->addElement('text', 'custom1', array(
            'filters'    => array('StringTrim'),
            'label'      => $translate->_('Custom 1'),
            'decorators' => array('Bootstrap'),
            'class'      => 'form-control input-lg'
        ));             

        $this->addElement('text', 'custom2', array(
            'filters'    => array('StringTrim'),
            'label'      => $translate->_('Custom 2'),
            'decorators' => array('Bootstrap'),
            'class'      => 'form-control input-lg'
        ));             

        $this->addElement('text', 'custom3', array(
            'filters'    => array('StringTrim'),
            'label'      => $translate->_('Custom 3'),
            'decorators' => array('Bootstrap'),
            'class'      => 'form-control input-lg'
        ));             
        
        $this->addElement('text', 'website', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => $translate->_('Website'),
            'decorators' => array('Bootstrap'),
            'class'      => 'form-control input-lg'
        ));
        
        $this->addElement('text', 'email', array(
            'filters'    => array('StringTrim', 'StringToLower'),
            'decorators' => array('Bootstrap'),
            'validators' => array(
                'EmailAddress',
            ),
            'required'   => true,
            'label'      => $translate->_('Email'),
            'class'      => 'form-control input-lg'
        ));
                
        $this->addElement('file', 'logo', array(
            'label'      => $translate->_('Logo'),
            'decorators' => array('File', array('ViewScript', array('viewScript' => 'partials/file.phtml', 'placement' => false))),
            'class'      => 'form-control input-lg'
        ));
                
        $this->addElement('file', 'logo_email', array(
            'label'      => $translate->_('Logo Email'),
            'decorators' => array('File', array('ViewScript', array('viewScript' => 'partials/file.phtml', 'placement' => false))),
            'class'      => 'form-control input-lg'
        ));
        
        $this->addElement('password', 'password', array(
            'filters'    => array('StringTrim'),
            'decorators' => array('Bootstrap'),
            'validators' => array(
                array('regex', false, '/^[a-zA-Z0-9\-\_\.\%\!\$]{6,20}$/')
            ),
            'label'      => $translate->_('Password'),
            'class'      => 'form-control input-lg'
        ));
        
        $this->addElement('select', 'isppanel', array(
            'filters'    => array('StringTrim'),
            'label'      => $translate->_('ISP Panel'),
            'decorators'  => array('Bootstrap'),
            'class'      => 'form-control input-lg'
        ));
        
        $this->getElement('isppanel')
                  ->setAllowEmpty(false)
                  ->setMultiOptions(Panels::getPanelInstalled());            
        
        $this->addElement('hidden', 'isp_id');
    }
}