<?php
/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class Version47 extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->createTable('servers_groups', array(
             'group_id' => 
             array(
              'type' => 'integer',
              'fixed' => '0',
              'unsigned' => '1',
              'primary' => '1',
              'autoincrement' => '1',
              'length' => '4',
             ),
             'name' => 
             array(
              'type' => 'string',
              'length' => '100',
             ),
             'fill_type' => 
             array(
              'type' => 'integer',
              'unsigned' => '1',
              'notnull' => '1',
              'length' => '4',
             ),
             'active' => 
             array(
              'type' => 'boolean',
              'default' => '1',
              'length' => '25',
             ),
             ), array(
             'primary' => 
             array(
              0 => 'group_id',
             ),
             'charset' => 'UTF8',
             ));
        $this->createTable('servers_groups_indexes', array(
             'relationship_id' => 
             array(
              'type' => 'integer',
              'fixed' => '0',
              'unsigned' => '',
              'primary' => '1',
              'autoincrement' => '1',
              'length' => '4',
             ),
             'server_id' => 
             array(
              'type' => 'int',
              'notnull' => '1',
              'length' => '4',
             ),
             'group_id' => 
             array(
              'type' => 'integer',
              'notnull' => '1',
              'length' => '4',
             ),
             ), array(
             'primary' => 
             array(
              0 => 'relationship_id',
             ),
             'charset' => 'UTF8',
             ));
    }

    public function down()
    {
        $this->dropTable('servers_groups');
        $this->dropTable('servers_groups_indexes');
    }
}