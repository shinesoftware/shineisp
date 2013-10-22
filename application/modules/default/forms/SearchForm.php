<?php
class Default_Form_SearchForm extends Zend_Form
{
    
    public function init()
    {
        $translate = Shineisp_Registry::get('Zend_Translate');
        
        $this->addElement('text', 'q', array(
            'filters'     => array('StringTrim'),
            'title'       => $translate->_('Type here what you are looking for'),
            'placeholder'       => $translate->_('Search'),
            'id'       	  => 'searchbar',
            'class'       => 'searchbar',
            'size'        => '10'
        ));

    }
    
}
