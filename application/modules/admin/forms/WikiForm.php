<?php
class Admin_Form_WikiForm extends Zend_Form
{
    
    public function init()
    {
        // Set the custom decorator
        $this->addElementPrefixPath('Shineisp_Decorator', 'Shineisp/Decorator/', 'decorator');
        
        $this->addElement('text', 'subject', array(
            'filters'     => array('StringTrim'),
            'required'    => true,
            'decorators'  => array('Composite'),
            'label'       => 'Subject',
            'class'       => 'text-input large-input'
        ));
        
        $this->addElement('text', 'uri', array(
            'filters'     => array('StringTrim'),
            'decorators'  => array('Composite'),
            'label'       => 'URI',
            'class'       => 'text-input large-input'
        ));
        
        
        $this->addElement('select', 'language_id', array(
            'decorators'  => array('Composite'),
            'label'       => 'Language',
            'class'       => 'text-input large-input'
        ));
                
        $this->getElement('language_id')
                  ->setAllowEmpty(false)
                  ->setRegisterInArrayValidator(false)
                  ->setMultiOptions(Languages::getList());           

        $this->addElement('select', 'active', array(
            'label'      => 'Active',
            'decorators' => array('Composite'),
            'class'      => 'text-input large-input',
            'multioptions' => array('0' => 'No', '1'=>'Yes')
        ));        
        
        $this->addElement('textarea', 'metadescription', array(
            'filters'     => array('StringTrim'),
            'decorators'  => array('Composite'),
            'label'       => 'Meta Description',
            'rows'        => 5,
            'class'       => 'textarea'
        ));
        
        $this->addElement('textarea', 'metakeywords', array(
            'filters'     => array('StringTrim'),
            'decorators'  => array('Composite'),
            'label'       => 'Meta Keywords',
            'rows'        => 5,
            'class'       => 'textarea'
        ));
        
        $this->addElement('textarea', 'content', array(
            'filters'     => array('StringTrim'),
            'decorators'  => array('Composite'),
            'label'       => 'Body',
            'id'          => 'body',
            'class'       => 'wysiwyg'
        ));
        
        $this->addElement('select', 'category_id', array(
            'decorators'  => array('Composite'),
            'label'       => 'Category',
            'class'       => 'text-input large-input'
        ));
        
        $this->getElement('category_id')
                  ->setAllowEmpty(false)
                  ->setRegisterInArrayValidator(false)
                  ->setMultiOptions(WikiCategories::getList());
                  
        $this->addElement('submit', 'save', array(
            'required' => false,
            'label'    => 'Save',
            'decorators' => array('Composite'),
            'class'    => 'button'
        ));
        
        $this->addElement('hidden', 'wiki_id');

    }
    
}