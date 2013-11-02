<?php
class Admin_Form_CompanyTypesForm extends Zend_Form
{
    
    public function init()
    {
        // Set the custom decorator
        $this->addElementPrefixPath('Shineisp_Decorator', 'Shineisp/Decorator/', 'decorator');
        $translate = Shineisp_Registry::get('Zend_Translate');
        
        $this->addElement('text', 'name', array(
            'filters'     => array('StringTrim'),
            'required'    => true,
            'decorators'  => array('Bootstrap'),
            'label'       => $translate->_('Name'),
            'class'       => 'form-control input-lg'
        ));

        $this->addElement('select', 'legalform_id', array(
        		'label'      => $translate->_('Legal form'),
        		'decorators' => array('Bootstrap'),
        		'class'      => 'form-control input-lg'
        ));
        
        $this->getElement('legalform_id')
					        ->setAllowEmpty(false)
					        ->setMultiOptions(Legalforms::getList(true));
					        
        $this->addElement('select', 'active', array(
        		'decorators'  => array('Bootstrap'),
        		'label'       => $translate->_('Active'),
        		'class'       => 'form-control input-lg',
        		'multioptions' => array( 0=>'NO', 1=> 'YES')
        ));
        
        $this->addElement('hidden', 'type_id');

    }
    
}