<?php
/**
 * Created by MPF Framework.
 * Date: 2014-10-09
 * Time: 17:09
 */

namespace app\models;


use mpf\base\App;
use mpf\datasources\sql\DataProvider;
use mpf\datasources\sql\DbModel;
use mpf\datasources\sql\DbRelations;
use mpf\datasources\sql\ModelCondition;

/**
 * Class UserGroup
 * @package app\models
 * @property int $id
 * @property string $name
 * @property \app\models\User[] $users
 */
class UserGroup extends DbModel {

    /**
     * Get database table name.
     * @return string
     */
    public static function getTableName() {
        return "users_groups";
    }

    /**
     * Get list of labels for each column. This are used by widgets like form, or table
     * to better display labels for inputs or table headers for each column.
     * @return array
     */
    public static function getLabels() {
        return array(
            'id' => 'Id',
            'name' => 'Name',
            'label' => 'Label'
        );
    }

    /**
     * Return list of relations for current model
     * @return array
     */
    public static function getRelations() {
        return array(
            'users' => array(DbRelations::MANY_TO_MANY, '\app\models\User', 'users2groups(group_id,user_id)')
        );
    }

    public static function getRules(){
        return array(
            array('name,label', 'safe,required', 'on'=>'insert,update')
        );
    }

    public function getDataProvider() {
        $condition = new ModelCondition(array('model' => __CLASS__));

        foreach (array('id', 'name', 'label') as $column) {
            if ($this->$column) {
                $condition->compareColumn($column, $this->$column, in_array($column, array('name', 'label')));
            }
        }
        return new DataProvider(array(
            'modelCondition' => $condition
        ));
    }

    public function beforeDelete(){
        App::get()->sql()->table('users2groups')->where('group_id = :group')->setParam(':group', $this->id)->delete(); //delete connections from this group to users
        return parent::beforeDelete();
    }
}
