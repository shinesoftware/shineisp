<?php
class Default_Form_SearchForm extends Zend_Form
{
    
    public function init()
    {
        
        $this->addElement('text', 'q', array(
            'filters'     => array('StringTrim'),
            'title'       => 'Type here what you are looking for',
            'id'       	  => 'searchbar',
            'class'       => 'searchbar',
            'size'        => '7'
        ));

    }
    
}
