<?php
class Admin_Form_CmspagesForm extends Zend_Form
{
    
    public function init()
    {
        // Set the custom decorator
        $this->addElementPrefixPath('Shineisp_Decorator', 'Shineisp/Decorator/', 'decorator');
        $translate = Shineisp_Registry::get('Zend_Translate');
        
        $this->addElement('text', 'title', array(
            'filters'     => array('StringTrim'),
            'required'    => false,
            'decorators'  => array('Composite'),
            'label'       => $translate->_('Title'),
            'class'       => 'text-input large-input'
        ));
        
        $this->addElement('textarea', 'body', array(
            'filters'     => array('StringTrim'),
            'decorators'  => array('Composite'),
            'label'       => $translate->_('Body'),
            'class'       => 'textarea'
        ));
        
        $this->addElement('text', 'var', array(
            'filters'     => array('StringTrim'),
            'decorators'  => array('Composite'),
            'label'       => $translate->_('URL-Key'),
        	'description' => $translate->_('This is the name of the page. For multilanguages website you can create more page with the same Url-key with different languages.'),
            'rows'        => 5,
            'class'       => 'text-input medium-input'
        ));
        
        $this->addElement('textarea', 'keywords', array(
            'filters'     => array('StringTrim'),
            'decorators'  => array('Composite'),
            'label'       => $translate->_('Keywords'),
            'rows'        => 5,
            'class'       => 'textarea'
        ));
        
        $this->addElement('textarea', 'blocks', array(
            'filters'     => array('StringTrim'),
            'decorators'  => array('Composite'),
            'label'       => $translate->_('Blocks'),
            'class'       => 'textarea'
        ));
        
        $this->addElement('textarea', 'xmllayout', array(
            'filters'     => array('StringTrim'),
            'decorators'  => array('Composite'),
            'label'       => $translate->_('XML Layout'),
            'class'       => 'textarea'
        ));
        
        $this->addElement('select', 'parent_id', array(
            'decorators'  => array('Composite'),
            'label'       => $translate->_('Parent'),
            'class'       => 'text-input large-input'
        ));
        
        $this->getElement('parent_id')
                  ->setAllowEmpty(false)
                  ->setRegisterInArrayValidator(false)
                  ->setMultiOptions(CmsPages::getList(true));
        
        $this->addElement('select', 'layout', array(
            'decorators'  => array('Composite'),
            'label'       => $translate->_('Content layouts'),
            'class'       => 'text-input large-input'
        ));
        
        $this->getElement('layout')
                  ->setAllowEmpty(false)
                  ->setRegisterInArrayValidator(false)
                  ->setMultiOptions(CmsPages::getLayouts());
        
        $this->addElement('select', 'pagelayout', array(
            'decorators'  => array('Composite'),
            'label'       => $translate->_('Page layouts'),
            'class'       => 'text-input large-input'
        ));
        
        $this->getElement('pagelayout')
                  ->setAllowEmpty(false)
                  ->setRegisterInArrayValidator(false)
                  ->setMultiOptions(CmsPages::getPageLayouts());
        
        $this->addElement('multiselect', 'language_id', array(
            'decorators'  => array('Composite'),
            'label'       => $translate->_('Language'),
            'class'       => 'text-input large-input'
        ));
                
        $this->getElement('language_id')
                  ->setAllowEmpty(false)
                  ->setRegisterInArrayValidator(false)
                  ->setMultiOptions(Languages::getList());       
                                    
        $this->addElement('select', 'showinmenu', array(
            'decorators'  => array('Composite'),
            'label'       => $translate->_('Show in the navigation menu'),
            'class'       => 'text-input large-input',
        	'multioptions' => array( 0=>'Not Visible', 1=> 'Visible')
        ));
        
        $this->addElement('select', 'showonrss', array(
            'decorators'  => array('Composite'),
            'label'       => $translate->_('Publish on RSS Feed'),
            'class'       => 'text-input large-input',
        	'multioptions' => array( 0=>'Not Published', 1=> 'Published')
        ));
        
        $this->addElement('select', 'active', array(
            'decorators'  => array('Composite'),
            'label'       => $translate->_('Active'),
            'class'       => 'text-input large-input',
        	'multioptions' => array( 0=>'NO', 1=> 'YES')
        ));
        
        $this->addElement('submit', 'save', array(
            'required' => false,
            'label'    => $translate->_('Save'),
            'decorators' => array('Composite'),
            'class'    => 'button'
        ));
        
        $this->addElement('hidden', 'page_id');

    }
    
}