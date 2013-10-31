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
        
        $this->addElement('text', 'var', array(
            'filters'     => array('StringTrim'),
            'decorators'  => array('Bootstrap'),
            'label'       => $translate->_('URL-Key'),
        	'description' => $translate->_('This is the name of the page. For multilanguages website you can create more page with the same Url-key with different languages.'),
            'rows'        => 5,
            'class'       => 'medium-input'
        ));
        
        $this->addElement('textarea', 'keywords', array(
            'filters'     => array('StringTrim'),
            'decorators'  => array('Bootstrap'),
            'label'       => $translate->_('Keywords'),
            'rows'        => 5,
            'class'       => 'span12'
        ));
        
        $this->addElement('textarea', 'blocks', array(
            'filters'     => array('StringTrim'),
            'decorators'  => array('Bootstrap'),
            'label'       => $translate->_('Blocks'),
            'class'       => 'span12'
        ));
        
        $this->addElement('textarea', 'xmllayout', array(
            'filters'     => array('StringTrim'),
            'decorators'  => array('Bootstrap'),
            'label'       => $translate->_('XML Layout'),
            'class'       => 'span12'
        ));
        
        $this->addElement('select', 'parent_id', array(
            'decorators'  => array('Bootstrap'),
            'label'       => $translate->_('Parent'),
            'class'       => 'input-large'
        ));
        
        $this->getElement('parent_id')
                  ->setAllowEmpty(false)
                  ->setRegisterInArrayValidator(false)
                  ->setMultiOptions(CmsPages::getList(true));
        
        $this->addElement('select', 'layout', array(
            'decorators'  => array('Bootstrap'),
            'label'       => $translate->_('Content layouts'),
            'class'       => 'input-large'
        ));
        
        $this->getElement('layout')
                  ->setAllowEmpty(false)
                  ->setRegisterInArrayValidator(false)
                  ->setMultiOptions(CmsPages::getLayouts());
        
        $this->addElement('select', 'pagelayout', array(
            'decorators'  => array('Bootstrap'),
            'label'       => $translate->_('Page layouts'),
            'class'       => 'input-large'
        ));
        
        $this->getElement('pagelayout')
                  ->setAllowEmpty(false)
                  ->setRegisterInArrayValidator(false)
                  ->setMultiOptions(CmsPages::getPageLayouts());
        
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
                                    
        $this->addElement('select', 'showinmenu', array(
            'decorators'  => array('Bootstrap'),
            'label'       => $translate->_('Show in the navigation menu'),
            'class'       => 'input-large',
        	'multioptions' => array( 0=>'Not Visible', 1=> 'Visible')
        ));
        
        $this->addElement('select', 'showonrss', array(
            'decorators'  => array('Bootstrap'),
            'label'       => $translate->_('Publish on RSS Feed'),
            'class'       => 'input-large',
        	'multioptions' => array( 0=>'Not Published', 1=> 'Published')
        ));
        
        $this->addElement('select', 'active', array(
            'decorators'  => array('Bootstrap'),
            'label'       => $translate->_('Active'),
            'class'       => 'input-large',
        	'multioptions' => array( 0=>'NO', 1=> 'YES')
        ));
        
        $this->addElement('submit', 'save', array(
            'required' => false,
            'label'    => $translate->_('Save'),
            'decorators' => array('Bootstrap'),
            'class'    => 'btn'
        ));
        
        $this->addElement('hidden', 'page_id');

    }
    
}