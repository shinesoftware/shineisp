<?php
/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class Version30 extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->addColumn('orders_items', 'parent_detail_id', 'integer', '4', array(
             ));
    }

    public function down()
    {
        $this->removeColumn('orders_items', 'parent_detail_id');
    }
}