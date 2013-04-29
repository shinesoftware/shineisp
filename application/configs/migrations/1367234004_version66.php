<?php
/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class Version66 extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->createForeignKey('products', 'products_welcome_mail_id_emails_templates_template_id', array(
             'name' => 'products_welcome_mail_id_emails_templates_template_id',
             'local' => 'welcome_mail_id',
             'foreign' => 'template_id',
             'foreignTable' => 'emails_templates',
             ));
        $this->addIndex('products', 'products_welcome_mail_id', array(
             'fields' => 
             array(
              0 => 'welcome_mail_id',
             ),
             ));
    }

    public function down()
    {
        $this->dropForeignKey('products', 'products_welcome_mail_id_emails_templates_template_id');
        $this->removeIndex('products', 'products_welcome_mail_id', array(
             'fields' => 
             array(
              0 => 'welcome_mail_id',
             ),
             ));
    }
}