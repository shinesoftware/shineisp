<?php

class Shineisp_Validate_NoRecordExists extends Zend_Validate_Abstract
{
    private $_table;
    private $_field;

    const OK = '';

    protected $_messageTemplates = array(
        self::OK => "'%value%' is already in our database."
    );

    public function __construct($table, $field) {
        if(is_null(Doctrine::getTable($table)))
            return null;

        if(!Doctrine::getTable($table)->hasColumn($field))
            return null;

        $this->_table = Doctrine::getTable($table);
        $this->_field = $field;
    }

    public function isValid($value)
    {
        $this->_setValue($value);

        $funcName = 'findBy' . $this->_field;

        if(count($this->_table->$funcName($value))>0) {
            $this->_error(self::OK);
            return false;
        }

        return true;
    }
}
