<?php
// Connection Component Binding
Doctrine_Manager::getInstance()->bindComponent('OauthRefreshTokens', 'doctrine');

/**
 * BaseOauthRefreshTokens
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property string $refresh_token
 * @property string $client_id
 * @property integer $user_id
 * @property timestamp $expires
 * @property string $scope
 * @property Doctrine_Collection $AdminUser
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseOauthRefreshTokens extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('oauth_refresh_tokens');
        $this->hasColumn('id', 'integer', 4, array(
             'type' => 'integer',
             'fixed' => 0,
             'unsigned' => true,
             'primary' => true,
             'autoincrement' => true,
             'length' => '4',
             ));
        $this->hasColumn('refresh_token', 'string', 250, array(
             'type' => 'string',
             'notnull' => true,
             'length' => '250',
             ));
        $this->hasColumn('client_id', 'string', 250, array(
             'type' => 'string',
             'notnull' => true,
             'length' => '250',
             ));
        $this->hasColumn('user_id', 'integer', 4, array(
             'type' => 'integer',
             'notnull' => true,
             'length' => '4',
             ));
        $this->hasColumn('expires', 'timestamp', 25, array(
             'type' => 'timestamp',
             'notnull' => true,
             'length' => '25',
             ));
        $this->hasColumn('scope', 'string', 250, array(
             'type' => 'string',
             'notnull' => true,
             'length' => '250',
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasMany('AdminUser', array(
             'local' => 'user_id',
             'foreign' => 'user_id'));
    }
}