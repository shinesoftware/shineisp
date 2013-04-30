<?php
class Admin_Form_LanguagesForm extends Zend_Form
{   
    public function init()
    {
        // Set the custom decorator
    	$this->addElementPrefixPath('Shineisp_Decorator', 'Shineisp/Decorator/', 'decorator');
    	
        $this->addElement('text', 'language', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => 'Language',
            'description'=> 'Set the name of the language',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input'
        ));
        
        $this->addElement('text', 'locale', array(
            'filters'    => array('StringTrim'),
            'label'      => 'Locale',
        	'description'=> 'Write here the name of the locale (eg. en). Then you have to create the en.mo file in the /application/languages/en.mo',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input'
        ));
        
        $this->addElement('text', 'code', array(
            'filters'    => array('StringTrim'),
            'label'      => 'Code',
        	'description'=> 'Write here the name of the locale (eg. en). ',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input'
        ));

        $this->addElement('select', 'active', array(
        		'decorators'  => array('Composite'),
        		'label'       => 'Active',
        		'class'       => 'text-input large-input',
        		'description' => 'Set the status of the translation language',
        		'multioptions' => array( 0=>'NO', 1=> 'YES')
        ));

        $this->addElement('select', 'base', array(
        		'decorators'  => array('Composite'),
        		'label'       => 'Base',
        		'class'       => 'text-input large-input',
        		'description' => 'Set the main translation language',
        		'multioptions' => array( 0=>'NO', 1=> 'YES, It is the default language')
        ));
        
        $this->addElement('submit', 'save', array(
            'required' => false,
            'label'    => 'Save',
            'decorators' => array('Composite'),
            'class'    => 'button'
        ));
        
        $this->addElement('hidden', 'language_id');
    }
}