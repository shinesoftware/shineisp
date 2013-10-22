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
            'decorators' => array('Composite'),
            'class'      => 'input-large'
        ));
        
        $this->addElement('text', 'vatnumber', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => $translate->_('VAT Number'),
            'decorators' => array('Composite'),
            'class'      => 'input-large'
        ));
        
        $this->addElement('text', 'address', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => $translate->_('Address'),
            'decorators' => array('Composite'),
            'class'      => 'input-large'
        ));

        $this->addElement('text', 'zip', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => $translate->_('ZIP Code'),
            'decorators' => array('Composite'),
            'class'      => 'input-large'
        ));

        $this->addElement('text', 'city', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => $translate->_('City'),
            'decorators' => array('Composite'),
            'class'      => 'input-large'
        ));

        $this->addElement('text', 'country', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => $translate->_('Country'),
            'decorators' => array('Composite'),
            'class'      => 'input-large'
        ));

        $this->addElement('text', 'telephone', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => $translate->_('Telephone'),
            'decorators' => array('Composite'),
            'class'      => 'input-large'
        ));

        $this->addElement('text', 'fax', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => $translate->_('Fax'),
            'decorators' => array('Composite'),
            'class'      => 'input-large'
        ));     

        $this->addElement('text', 'bankname', array(
            'filters'    => array('StringTrim'),
            'label'      => $translate->_('Bank name'),
            'decorators' => array('Composite'),
            'class'      => 'input-large'
        ));     

        $this->addElement('text', 'iban', array(
            'filters'    => array('StringTrim'),
            'label'      => $translate->_('IBAN'),
            'decorators' => array('Composite'),
            'class'      => 'input-large'
        ));     

        $this->addElement('text', 'bic', array(
            'filters'    => array('StringTrim'),
            'label'      => $translate->_('BIC'),
            'decorators' => array('Composite'),
            'class'      => 'input-large'
        ));     

        $this->addElement('text', 'slogan', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => $translate->_('Slogan'),
            'decorators' => array('Composite'),
            'class'      => 'input-large'
        ));     

        $this->addElement('text', 'manager', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => $translate->_('Manager'),
            'decorators' => array('Composite'),
            'class'      => 'input-large'
        ));             

        $this->addElement('text', 'custom1', array(
            'filters'    => array('StringTrim'),
            'label'      => $translate->_('Custom 1'),
            'decorators' => array('Composite'),
            'class'      => 'input-large'
        ));             

        $this->addElement('text', 'custom2', array(
            'filters'    => array('StringTrim'),
            'label'      => $translate->_('Custom 2'),
            'decorators' => array('Composite'),
            'class'      => 'input-large'
        ));             

        $this->addElement('text', 'custom3', array(
            'filters'    => array('StringTrim'),
            'label'      => $translate->_('Custom 3'),
            'decorators' => array('Composite'),
            'class'      => 'input-large'
        ));             
        
        $this->addElement('text', 'website', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => $translate->_('Website'),
            'decorators' => array('Composite'),
            'class'      => 'input-large'
        ));
        
        $this->addElement('text', 'email', array(
            'filters'    => array('StringTrim', 'StringToLower'),
            'decorators' => array('Composite'),
            'validators' => array(
                'EmailAddress',
            ),
            'required'   => true,
            'label'      => $translate->_('Email'),
            'class'      => 'input-large'
        ));
                
        $this->addElement('file', 'logo', array(
            'label'      => $translate->_('Logo'),
            'class'      => 'input-large'
        ));
                
        $this->addElement('file', 'logo_email', array(
            'label'      => $translate->_('Logo Email'),
            'class'      => 'input-large'
        ));
        
        $this->addElement('password', 'password', array(
            'filters'    => array('StringTrim'),
            'decorators' => array('Composite'),
            'validators' => array(
                array('regex', false, '/^[a-zA-Z0-9\-\_\.\%\!\$]{6,20}$/')
            ),
            'label'      => $translate->_('Password'),
            'class'      => 'input-large'
        ));
        
        $this->addElement('select', 'isppanel', array(
            'filters'    => array('StringTrim'),
            'label'      => $translate->_('ISP Panel'),
            'class'      => 'input-large'
        ));
        
        $this->getElement('isppanel')
                  ->setAllowEmpty(false)
                  ->setMultiOptions(Panels::getPanelInstalled());            
        
        $this->addElement('hidden', 'isp_id');
    }
}