<?php
class Admin_Form_CompanyTypesForm extends Zend_Form
{
    
    public function init()
    {
        // Set the custom decorator
        $this->addElementPrefixPath('Shineisp_Decorator', 'Shineisp/Decorator/', 'decorator');
        
        $this->addElement('text', 'name', array(
            'filters'     => array('StringTrim'),
            'required'    => true,
            'decorators'  => array('Composite'),
            'label'       => 'Name',
            'class'       => 'text-input large-input'
        ));

        $this->addElement('select', 'legalform_id', array(
        		'label'      => 'Legal form',
        		'decorators' => array('Composite'),
        		'class'      => 'text-input large-input'
        ));
        
        $this->getElement('legalform_id')
					        ->setAllowEmpty(false)
					        ->setMultiOptions(Legalforms::getList(true));
					        
        $this->addElement('select', 'active', array(
        		'decorators'  => array('Composite'),
        		'label'       => 'Active',
        		'class'       => 'text-input large-input',
        		'multioptions' => array( 0=>'NO', 1=> 'YES')
        ));
        
        $this->addElement('hidden', 'type_id');

    }
    
}