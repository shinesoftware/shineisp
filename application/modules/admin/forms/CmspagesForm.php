<?php
class Admin_Form_CmspagesForm extends Zend_Form
{
    
    public function init()
    {
        // Set the custom decorator
        $this->addElementPrefixPath('Shineisp_Decorator', 'Shineisp/Decorator/', 'decorator');
        
        $this->addElement('text', 'title', array(
            'filters'     => array('StringTrim'),
            'required'    => false,
            'decorators'  => array('Composite'),
            'label'       => 'Title',
            'class'       => 'text-input large-input'
        ));
        
        $this->addElement('textarea', 'body', array(
            'filters'     => array('StringTrim'),
            'decorators'  => array('Composite'),
            'label'       => 'Body',
            'class'       => 'textarea'
        ));
        
        $this->addElement('text', 'var', array(
            'filters'     => array('StringTrim'),
            'decorators'  => array('Composite'),
            'label'       => 'URL-Key',
        	'description' => 'This is the name of the page. For multilanguages website you can create more page with the same Url-key with different languages.',
            'rows'        => 5,
            'class'       => 'text-input medium-input'
        ));
        
        $this->addElement('textarea', 'keywords', array(
            'filters'     => array('StringTrim'),
            'decorators'  => array('Composite'),
            'label'       => 'Keywords',
            'rows'        => 5,
            'class'       => 'textarea'
        ));
        
        $this->addElement('textarea', 'blocks', array(
            'filters'     => array('StringTrim'),
            'decorators'  => array('Composite'),
            'label'       => 'Blocks',
            'class'       => 'textarea'
        ));
        
        $this->addElement('textarea', 'xmllayout', array(
            'filters'     => array('StringTrim'),
            'decorators'  => array('Composite'),
            'label'       => 'XML Layout',
            'class'       => 'textarea'
        ));
        
        $this->addElement('select', 'parent_id', array(
            'decorators'  => array('Composite'),
            'label'       => 'Parent',
            'class'       => 'text-input large-input'
        ));
        
        $this->getElement('parent_id')
                  ->setAllowEmpty(false)
                  ->setRegisterInArrayValidator(false)
                  ->setMultiOptions(CmsPages::getList(true));
        
        $this->addElement('select', 'layout', array(
            'decorators'  => array('Composite'),
            'label'       => 'Content layouts',
            'class'       => 'text-input large-input'
        ));
        
        $this->getElement('layout')
                  ->setAllowEmpty(false)
                  ->setRegisterInArrayValidator(false)
                  ->setMultiOptions(CmsPages::getLayouts());
        
        $this->addElement('select', 'pagelayout', array(
            'decorators'  => array('Composite'),
            'label'       => 'Page layouts',
            'class'       => 'text-input large-input'
        ));
        
        $this->getElement('pagelayout')
                  ->setAllowEmpty(false)
                  ->setRegisterInArrayValidator(false)
                  ->setMultiOptions(CmsPages::getPageLayouts());
        
        $this->addElement('multiselect', 'language_id', array(
            'decorators'  => array('Composite'),
            'label'       => 'Language',
            'class'       => 'text-input large-input'
        ));
                
        $this->getElement('language_id')
                  ->setAllowEmpty(false)
                  ->setRegisterInArrayValidator(false)
                  ->setMultiOptions(Languages::getList());       
                                    
        $this->addElement('select', 'showinmenu', array(
            'decorators'  => array('Composite'),
            'label'       => 'Show in the navigation menu',
            'class'       => 'text-input large-input',
        	'multioptions' => array( 0=>'Not Visible', 1=> 'Visible')
        ));
        
        $this->addElement('select', 'showonrss', array(
            'decorators'  => array('Composite'),
            'label'       => 'Publish on RSS Feed',
            'class'       => 'text-input large-input',
        	'multioptions' => array( 0=>'Not Published', 1=> 'Published')
        ));
        
        $this->addElement('select', 'active', array(
            'decorators'  => array('Composite'),
            'label'       => 'Active',
            'class'       => 'text-input large-input',
        	'multioptions' => array( 0=>'NO', 1=> 'YES')
        ));
        
        $this->addElement('submit', 'save', array(
            'required' => false,
            'label'    => 'Save',
            'decorators' => array('Composite'),
            'class'    => 'button'
        ));
        
        $this->addElement('hidden', 'page_id');

    }
    
}