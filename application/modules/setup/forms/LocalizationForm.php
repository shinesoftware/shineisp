<?php
class Setup_Form_LocalizationForm extends Zend_Form
{   
	
    public function init()
    {
        $this->addElementPrefixPath('Shineisp_Decorator', 'Shineisp/Decorator/', 'decorator');
        
        $this->addElement('select', 'locale', array(
        		'decorators'  => array('Bootstrap'),
        		'label'       => 'Language',
        		'class'       => 'form-control',
        		'multioptions' => Languages::getLanguageFiles(PUBLIC_PATH . "/languages")
        ));

        $this->addElement('textarea', 'agreement', array(
        		'filters'    => array('StringTrim'),
        		'decorators' => array('Bootstrap'),
        		'class'      => 'form-control',
        		'label'      => 'Agreements',
        		'rows'      => '10',
        		'value'		 => Shineisp_Commons_Utilities::readfile(PUBLIC_PATH . "/../LICENSE")));

        $this->addElement('select', 'chkagreement', array(
        		'decorators'  => array('Bootstrap'),
        		'label'       => 'I agree with the legal terms',
        		'class'       => 'form-control',
        		'multioptions' => array(  1=> 'YES, I agree with the legal terms', 0=>'NO, I disagree with these legal terms')
        ));

        $this->addElement('submit', 'submit', array(
            'label'    => 'Continue',
            'decorators' => array('Bootstrap'),
            'class'    => 'btn btn-primary btn-lg'
        ));
        
        
    }
}