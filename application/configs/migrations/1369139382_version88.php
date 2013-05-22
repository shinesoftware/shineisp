<?php
/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class Version88 extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->addIndex('orders', 'uuid', array(
             'fields' => 
             array(
              0 => 'uuid',
             ),
             'type' => 'unique',
             ));
        $this->addIndex('orders_items', 'uuid', array(
             'fields' => 
             array(
              0 => 'uuid',
             ),
             'type' => 'unique',
             ));
    }

    public function down()
    {
        $this->removeIndex('orders', 'uuid', array(
             'fields' => 
             array(
              0 => 'uuid',
             ),
             'type' => 'unique',
             ));
        $this->removeIndex('orders_items', 'uuid', array(
             'fields' => 
             array(
              0 => 'uuid',
             ),
             'type' => 'unique',
             ));
    }
}