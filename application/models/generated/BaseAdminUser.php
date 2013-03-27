<?php
// Connection Component Binding
Doctrine_Manager::getInstance()->bindComponent('AdminUser', 'doctrine');

/**
 * BaseAdminUser
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $user_id
 * @property string $firstname
 * @property string $lastname
 * @property string $email
 * @property string $password
 * @property timestamp $last_password_change
 * @property tinyint $force_password_change
 * @property timestamp $created
 * @property timestamp $changed
 * @property integer $lognum
 * @property integer $role_id
 * @property string $config
 * @property integer $isp_id
 * @property AdminRoles $AdminRoles
 * @property Isp $Isp
 * @property Doctrine_Collection $Tickets
 * @property Doctrine_Collection $Notes
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseAdminUser extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('admin_user');
        $this->hasColumn('user_id', 'integer', 4, array(
             'type' => 'integer',
             'fixed' => 0,
             'unsigned' => false,
             'primary' => true,
             'autoincrement' => true,
             'length' => '4',
             ));
        $this->hasColumn('firstname', 'string', 250, array(
             'type' => 'string',
             'notnull' => true,
             'length' => '250',
             ));
        $this->hasColumn('lastname', 'string', 250, array(
             'type' => 'string',
             'notnull' => true,
             'length' => '250',
             ));
        $this->hasColumn('email', 'string', 250, array(
             'type' => 'string',
             'notnull' => true,
             'length' => '250',
             ));
        $this->hasColumn('password', 'string', 250, array(
             'type' => 'string',
             'notnull' => true,
             'length' => '250',
             ));
        $this->hasColumn('last_password_change', 'timestamp', 25, array(
             'type' => 'timestamp',
             'notnull' => false,
             'length' => '25',
             ));
        $this->hasColumn('force_password_change', 'tinyint', 1, array(
             'type' => 'tinyint',
             'notnull' => true,
             'fixed' => 0,
             'length' => '1',
             ));
        $this->hasColumn('created', 'timestamp', 25, array(
             'type' => 'timestamp',
             'length' => '25',
             ));
        $this->hasColumn('changed', 'timestamp', 25, array(
             'type' => 'timestamp',
             'length' => '25',
             ));
        $this->hasColumn('lognum', 'integer', 4, array(
             'type' => 'integer',
             'length' => '4',
             ));
        $this->hasColumn('role_id', 'integer', 4, array(
             'type' => 'integer',
             'notnull' => false,
             'length' => '4',
             ));
        $this->hasColumn('config', 'string', null, array(
             'type' => 'string',
             'length' => '',
             ));
        $this->hasColumn('isp_id', 'integer', 4, array(
             'type' => 'integer',
             'length' => '4',
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('AdminRoles', array(
             'local' => 'role_id',
             'foreign' => 'role_id'));

        $this->hasOne('Isp', array(
             'local' => 'isp_id',
             'foreign' => 'isp_id'));

        $this->hasMany('Tickets', array(
             'local' => 'user_id',
             'foreign' => 'user_id'));

        $this->hasMany('Notes', array(
             'local' => 'user_id',
             'foreign' => 'user_id'));
    }
}