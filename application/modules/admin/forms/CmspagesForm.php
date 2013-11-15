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
            'class'       => 'form-control'
        ));
        
        $this->addElement('textarea', 'body', array(
            'filters'     => array('StringTrim'),
            'decorators'  => array('Bootstrap'),
            'label'       => $translate->_('Body'),
            'class'       => 'form-control col-lg-12'
        ));
        
        $this->addElement('text', 'var', array(
            'filters'     => array('StringTrim'),
            'decorators'  => array('Bootstrap'),
            'label'       => $translate->_('URL-Key'),
        	'description' => $translate->_('This is the name of the page. For multilanguages website you can create more page with the same Url-key with different languages.'),
            'rows'        => 5,
            'class'       => 'form-control'
        ));
        
        $this->addElement('textarea', 'keywords', array(
            'filters'     => array('StringTrim'),
            'decorators'  => array('Bootstrap'),
            'label'       => $translate->_('Keywords'),
            'rows'        => 5,
            'description' => $translate->_('separate each keyword by a comma'),
            'class'       => 'col-lg-12 form-control'
        ));
        
        $this->addElement('textarea', 'blocks', array(
            'filters'     => array('StringTrim'),
            'decorators'  => array('Bootstrap'),
            'label'       => $translate->_('Blocks'),
            'rows'        => 5,
            'class'       => 'col-lg-12 form-control'
        ));
        
        $this->addElement('textarea', 'xmllayout', array(
            'filters'     => array('StringTrim'),
            'decorators'  => array('Bootstrap'),
            'label'       => $translate->_('XML Layout'),
            'rows'        => 5,
            'class'       => 'col-lg-12 form-control'
        ));
        
        $this->addElement('checkbox', 'blog', array(
            'filters'     => array('StringTrim'),
            'decorators'  => array('Bootstrap'),
            'label'       => $translate->_('is Blog post'),
            'class'       => 'form-control'
        ));
        
        $this->addElement('select', 'parent_id', array(
            'decorators'  => array('Bootstrap'),
            'label'       => $translate->_('Parent'),
            'class'       => 'form-control'
        ));
        
        $this->getElement('parent_id')
                  ->setAllowEmpty(false)
                  ->setRegisterInArrayValidator(false)
                  ->setMultiOptions(CmsPages::getList(true));
        
        $this->addElement('select', 'layout', array(
            'decorators'  => array('Bootstrap'),
            'label'       => $translate->_('Content layouts'),
            'class'       => 'form-control'
        ));
        
        $this->getElement('layout')
                  ->setAllowEmpty(false)
                  ->setRegisterInArrayValidator(false)
                  ->setMultiOptions(CmsPages::getLayouts());
        
        $this->addElement('select', 'pagelayout', array(
            'decorators'  => array('Bootstrap'),
            'label'       => $translate->_('Page layouts'),
            'class'       => 'form-control'
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
            'class'      => 'multiselect show-tick col-md-4'
        ));
                
        $this->getElement('language_id')
                  ->setAllowEmpty(false)
                  ->setRegisterInArrayValidator(false)
                  ->setMultiOptions(Languages::getList());       
                                    
        $this->addElement('select', 'showinmenu', array(
            'decorators'  => array('Bootstrap'),
            'label'       => $translate->_('Show in the navigation menu'),
            'class'       => 'form-control',
        	'multioptions' => array( 0=>'Not Visible', 1=> 'Visible')
        ));
        
        $this->addElement('select', 'showonrss', array(
            'decorators'  => array('Bootstrap'),
            'label'       => $translate->_('Publish on RSS Feed'),
            'class'       => 'form-control',
        	'multioptions' => array( 0=>'Not Published', 1=> 'Published')
        ));
        
        $this->addElement('select', 'active', array(
            'decorators'  => array('Bootstrap'),
            'label'       => $translate->_('Active'),
            'class'       => 'form-control',
        	'multioptions' => array( 0=>'NO', 1=> 'YES')
        ));
        
        $this->addElement('hidden', 'page_id');

    }
    
}