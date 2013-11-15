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
            'class'       => 'form-control'
        ));
        
        $this->addElement('text', 'title', array(
            'filters'     => array('StringTrim'),
            'required'    => false,
            'decorators'  => array('Bootstrap'),
            'label'       => $translate->_('Title'),
            'class'       => 'form-control'
        ));
        
        $this->addElement('textarea', 'body', array(
            'filters'     => array('StringTrim'),
            'decorators'  => array('Bootstrap'),
            'label'       => $translate->_('Body'),
            'class'       => 'col-lg-12 form-control'
        ));

        $this->addElement('multiselect', 'language_id', array(
            'decorators'  => array('Bootstrap'),
            'label'       => $translate->_('Language'),
            'title'	     => $translate->_('Select ...'),
    		'data-container' => 'body',
    		'data-selected-text-format' => 'count > 2',
    		'data-size' => 'auto',
    		'data-live-search' => 'true',
            'class'      => 'multiselect show-tick col-md-4'
        ));
                
        $this->getElement('language_id')
                  ->setAllowEmpty(false)
                  ->setRegisterInArrayValidator(false)
                  ->setMultiOptions(Languages::getList());   
        
        $this->addElement('hidden', 'block_id');

    }
    
}