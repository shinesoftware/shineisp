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
            'decorators'  => array('Bootstrap'),
            'label'       => $translate->_('Subject'),
            'class'       => 'form-control input-lg'
        ));
        
        $this->addElement('text', 'uri', array(
            'filters'     => array('StringTrim'),
            'decorators'  => array('Bootstrap'),
            'label'       => $translate->_('URI'),
            'class'       => 'form-control input-lg'
        ));
        
        
        $this->addElement('select', 'language_id', array(
            'decorators'  => array('Bootstrap'),
            'label'       => $translate->_('Language'),
            'class'       => 'form-control input-lg'
        ));
                
        $this->getElement('language_id')
                  ->setAllowEmpty(false)
                  ->setRegisterInArrayValidator(false)
                  ->setMultiOptions(Languages::getList());           

        $this->addElement('select', 'active', array(
            'label'      => $translate->_('Active'),
            'decorators' => array('Bootstrap'),
            'class'      => 'form-control input-lg',
            'multioptions' => array('0' => 'No', '1'=>'Yes')
        ));        
        
        $this->addElement('textarea', 'metadescription', array(
            'filters'     => array('StringTrim'),
            'decorators'  => array('Bootstrap'),
            'label'       => $translate->_('Meta Description'),
            'rows'        => 5,
            'class'       => 'col-lg-12'
        ));
        
        $this->addElement('textarea', 'metakeywords', array(
            'filters'     => array('StringTrim'),
            'decorators'  => array('Bootstrap'),
            'label'       => $translate->_('Meta Keywords'),
            'rows'        => 5,
            'class'       => 'col-lg-12'
        ));
        
        $this->addElement('textarea', 'content', array(
            'filters'     => array('StringTrim'),
            'decorators'  => array('Bootstrap'),
            'label'       => $translate->_('Body'),
            'id'          => 'body',
            'class'       => 'col-lg-12 form-control input-lg wysiwyg'
        ));
        
        $this->addElement('select', 'category_id', array(
            'decorators'  => array('Bootstrap'),
            'label'       => $translate->_('Category'),
            'class'       => 'form-control input-lg'
        ));
        
        $this->getElement('category_id')
                  ->setAllowEmpty(false)
                  ->setRegisterInArrayValidator(false)
                  ->setMultiOptions(WikiCategories::getList());
        
        $this->addElement('hidden', 'wiki_id');

    }
    
}