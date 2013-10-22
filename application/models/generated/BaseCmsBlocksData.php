<?php
// Connection Component Binding
Doctrine_Manager::getInstance()->bindComponent('CmsBlocksData', 'doctrine');

/**
 * BaseCmsBlocksData
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property integer $block_id
 * @property integer $language_id
 * @property Languages $Languages
 * @property CmsBlocks $CmsBlocks
 * 
 * @package    ShineISP
 * 
 * @author     Shine Software <info@shineisp.com>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseCmsBlocksData extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('cms_blocks_data');
        $this->hasColumn('id', 'integer', 8, array(
             'type' => 'integer',
             'autoincrement' => true,
             'primary' => true,
             'length' => '8',
             ));
        $this->hasColumn('block_id', 'integer', 4, array(
             'type' => 'integer',
             'notnull' => false,
             'length' => '4',
             ));
        $this->hasColumn('language_id', 'integer', 4, array(
             'type' => 'integer',
             'notnull' => false,
             'length' => '4',
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('Languages', array(
             'local' => 'language_id',
             'foreign' => 'language_id',
             'onDelete' => 'CASCADE'));

        $this->hasOne('CmsBlocks', array(
             'local' => 'block_id',
             'foreign' => 'block_id',
             'onDelete' => 'CASCADE'));
    }
}