<?php
class Admin_Form_UsersForm extends Zend_Form
{
    
    public function init()
    {
        
        $company = $this->addElement('text', 'company', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => 'Company:',
        ));
        
        $firstname = $this->addElement('text', 'firstname', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => 'Firstname:',
        ));
        
        $lastname = $this->addElement('text', 'lastname', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => 'Lastname:',
        ));
        
        $contact = $this->addElement('select', 'type_id', array('label' => 'Contact:'));
        $contact = $this->getElement('type_id')
        		  ->setAllowEmpty(false)
                  ->setMultiOptions(UsersTypes::getList())
                  ->setRequired(true);
        
        $email = $this->addElement('text', 'email', array(
            'filters'    => array('StringTrim', 'StringToLower'),
            'validators' => array(
                'EmailAddress',
            ),
            'required'   => true,
            'label'      => 'Your email:',
        ));
        
        $password = $this->addElement('password', 'password', array(
            'filters'    => array('StringTrim'),
            'validators' => array(
                array('regex', false, '/^[a-zA-Z0-9\-\_\.\%\!\$]{6,20}$/')
            ),
            'required'   => true,
            'label'      => 'Password:',
        ));

        $save = $this->addElement('submit', 'save', array(
            'required' => false,
            'ignore'   => true,
            'label'    => 'Save',
        ));
        
        $id = $this->addElement('hidden', 'user_id');

        $this->setDecorators(array(
            'FormElements',
            array('HtmlTag', array('tag' => 'dl', 'class' => 'user_form')),
            array('Description', array('placement' => 'prepend')),
            'Form'
        ));
    }
    
}