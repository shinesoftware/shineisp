<?php
class Admin_Form_LanguagesForm extends Zend_Form
{   
    public function init()
    {
        // Set the custom decorator
    	$this->addElementPrefixPath('Shineisp_Decorator', 'Shineisp/Decorator/', 'decorator');
    	$translate = Shineisp_Registry::get('Zend_Translate');
    	
        $this->addElement('text', 'language', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => $translate->_('Language'),
            'description'=> $translate->_('Set the name of the language'),
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input'
        ));
        
        $this->addElement('text', 'locale', array(
            'filters'    => array('StringTrim'),
            'label'      => $translate->_('Locale'),
        	'description'=> $translate->_('Write here the name of the locale (eg. en). Then you have to create the en.mo file in the /application/languages/en.mo'),
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input'
        ));
        
        $this->addElement('text', 'code', array(
            'filters'    => array('StringTrim'),
            'label'      => $translate->_('Code'),
        	'description'=> $translate->_('Write here the name of the locale (eg. en). '),
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input'
        ));

        $this->addElement('select', 'active', array(
        		'decorators'  => array('Composite'),
        		'label'       => $translate->_('Active'),
        		'class'       => 'text-input large-input',
        		'description' => $translate->_('Set the status of the translation language'),
        		'multioptions' => array( 0=>'NO', 1=> 'YES')
        ));

        $this->addElement('select', 'base', array(
        		'decorators'  => array('Composite'),
        		'label'       => $translate->_('Base'),
        		'class'       => 'text-input large-input',
        		'description' => $translate->_('Set the main translation language'),
        		'multioptions' => array( 0=>'NO', 1=> 'YES, It is the default language')
        ));
        
        $this->addElement('hidden', 'language_id');
    }
}