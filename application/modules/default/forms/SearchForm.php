<?php
class Default_Form_SearchForm extends Zend_Form
{
    
    public function init()
    {
        $translator = Shineisp_Registry::getInstance ()->Zend_Translate;
        
        $this->addElement('text', 'q', array(
            'filters'     => array('StringTrim'),
            'title'       => $translator->translate('Type here what you are looking for'),
            'placeholder'       => $translator->translate('Search'),
            'id'       	  => 'searchbar',
            'class'       => 'searchbar',
            'size'        => '10'
        ));

    }
    
}
