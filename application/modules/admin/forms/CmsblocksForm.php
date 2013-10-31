<?php
class Admin_Form_CmsblocksForm extends Zend_Form
{
    
    public function init()
    {
        // Set the custom decorator
        $this->addElementPrefixPath('Shineisp_Decorator', 'Shineisp/Decorator/', 'decorator');
        $translate = Shineisp_Registry::get('Zend_Translate');
        
        $this->addElement('text', 'var', array(
            'filters'     => array('StringTrim'),
            'required'    => false,
            'decorators'  => array('Bootstrap'),
            'label'       => $translate->_('Var'),
            'class'       => 'input-large'
        ));
        
        $this->addElement('text', 'title', array(
            'filters'     => array('StringTrim'),
            'required'    => false,
            'decorators'  => array('Bootstrap'),
            'label'       => $translate->_('Title'),
            'class'       => 'input-large'
        ));
        
        $this->addElement('textarea', 'body', array(
            'filters'     => array('StringTrim'),
            'decorators'  => array('Bootstrap'),
            'label'       => $translate->_('Body'),
            'class'       => 'span12'
        ));

        $this->addElement('multiselect', 'language_id', array(
            'decorators'  => array('Bootstrap'),
            'label'       => $translate->_('Language'),
            'title'	     => $translate->_('Select ...'),
    		'data-container' => 'body',
    		'data-selected-text-format' => 'count > 2',
    		'data-size' => 'auto',
    		'data-live-search' => 'true',
            'class'      => 'multiselect show-tick span4'
        ));
                
        $this->getElement('language_id')
                  ->setAllowEmpty(false)
                  ->setRegisterInArrayValidator(false)
                  ->setMultiOptions(Languages::getList());   
                  
        $this->addElement('submit', 'save', array(
            'required' => false,
            'label'    => $translate->_('Save'),
            'decorators' => array('Bootstrap'),
            'class'    => 'btn'
        ));
        
        $this->addElement('hidden', 'block_id');

    }
    
}