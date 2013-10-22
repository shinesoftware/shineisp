<?php
class Admin_Form_WikiForm extends Zend_Form
{
    
    public function init()
    {
        // Set the custom decorator
        $this->addElementPrefixPath('Shineisp_Decorator', 'Shineisp/Decorator/', 'decorator');
        $translate = Shineisp_Registry::get('Zend_Translate');
        
        $this->addElement('text', 'subject', array(
            'filters'     => array('StringTrim'),
            'required'    => true,
            'decorators'  => array('Composite'),
            'label'       => $translate->_('Subject'),
            'class'       => 'input-large'
        ));
        
        $this->addElement('text', 'uri', array(
            'filters'     => array('StringTrim'),
            'decorators'  => array('Composite'),
            'label'       => $translate->_('URI'),
            'class'       => 'input-large'
        ));
        
        
        $this->addElement('select', 'language_id', array(
            'decorators'  => array('Composite'),
            'label'       => $translate->_('Language'),
            'class'       => 'input-large'
        ));
                
        $this->getElement('language_id')
                  ->setAllowEmpty(false)
                  ->setRegisterInArrayValidator(false)
                  ->setMultiOptions(Languages::getList());           

        $this->addElement('select', 'active', array(
            'label'      => $translate->_('Active'),
            'decorators' => array('Composite'),
            'class'      => 'input-large',
            'multioptions' => array('0' => 'No', '1'=>'Yes')
        ));        
        
        $this->addElement('textarea', 'metadescription', array(
            'filters'     => array('StringTrim'),
            'decorators'  => array('Composite'),
            'label'       => $translate->_('Meta Description'),
            'rows'        => 5,
            'class'       => 'textarea'
        ));
        
        $this->addElement('textarea', 'metakeywords', array(
            'filters'     => array('StringTrim'),
            'decorators'  => array('Composite'),
            'label'       => $translate->_('Meta Keywords'),
            'rows'        => 5,
            'class'       => 'textarea'
        ));
        
        $this->addElement('textarea', 'content', array(
            'filters'     => array('StringTrim'),
            'decorators'  => array('Composite'),
            'label'       => $translate->_('Body'),
            'id'          => 'body',
            'class'       => 'wysiwyg'
        ));
        
        $this->addElement('select', 'category_id', array(
            'decorators'  => array('Composite'),
            'label'       => $translate->_('Category'),
            'class'       => 'input-large'
        ));
        
        $this->getElement('category_id')
                  ->setAllowEmpty(false)
                  ->setRegisterInArrayValidator(false)
                  ->setMultiOptions(WikiCategories::getList());
        
        $this->addElement('hidden', 'wiki_id');

    }
    
}