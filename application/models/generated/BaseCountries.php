<?php
// Connection Component Binding
Doctrine_Manager::getInstance()->bindComponent('Countries', 'doctrine');

/**
 * BaseCountries
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $country_id
 * @property string $name
 * @property string $code
 * @property string $tld
 * @property integer $enabled
 * @property Addresses $Addresses
 * @property Doctrine_Collection $Regions
 * @property Doctrine_Collection $Provinces
 * 
 * @package    ShineISP
 * 
 * @author     Shine Software <info@shineisp.com>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseCountries extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('countries');
        $this->hasColumn('country_id', 'integer', 4, array(
             'type' => 'integer',
             'fixed' => 0,
             'unsigned' => false,
             'primary' => true,
             'autoincrement' => true,
             'length' => '4',
             ));
        $this->hasColumn('name', 'string', 200, array(
             'type' => 'string',
             'notnull' => false,
             'length' => '200',
             ));
        $this->hasColumn('code', 'string', 10, array(
             'type' => 'string',
             'notnull' => false,
             'length' => '10',
             ));
        $this->hasColumn('tld', 'string', 10, array(
             'type' => 'string',
             'notnull' => false,
             'length' => '10',
             ));
        $this->hasColumn('enabled', 'integer', 1, array(
             'type' => 'integer',
             'default' => '1',
             'notnull' => true,
             'length' => '1',
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('Addresses', array(
             'local' => 'country_id',
             'foreign' => 'country_id',
             'onDelete' => 'CASCADE'));

        $this->hasMany('Regions', array(
             'local' => 'country_id',
             'foreign' => 'country_id'));

        $this->hasMany('Provinces', array(
             'local' => 'country_id',
             'foreign' => 'country_id'));
    }
}