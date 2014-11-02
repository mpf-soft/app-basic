<?php
/**
 * Created by MPF Framework.
 * Date: 2014-10-23
 * Time: 14:36
 */

namespace app\models;

use mpf\datasources\sql\DataProvider;
use mpf\datasources\sql\DbModel;
use mpf\datasources\sql\ModelCondition;

/**
 * Class UserTitle
 * @package app\models
 * @property int $id
 * @property int $auto
 * @property string $title
 * @property string $description
 */
class UserTitle extends DbModel {

    /**
     * Get database table name.
     * @return string
     */
    public static function getTableName() {
        return "users_titles";
    }

    /**
     * Get list of labels for each column. This are used by widgets like form, or table
     * to better display labels for inputs or table headers for each column.
     * @return array
     */
    public static function getLabels() {
        return array(
             'id' => 'Id',
             'auto' => 'Auto',
             'title' => 'Title',
             'description' => 'Description'
        );
    }

    /**
     * Return list of relations for current model
     * @return array
     */
    public static function getRelations(){
        return array(
             
        );
    }

    /**
     * List of rules for current model
     * @return array
     */
    public static function getRules(){
        return array(

        );
    }

    /**
     * Gets DataProvider used later by widgets like \mpf\widgets\datatable\Table to manage models.
     * @return \mpf\datasources\sql\DataProvider
     */
    public function getDataProvider() {
        $condition = new ModelCondition(array('model' => __CLASS__));

        foreach (array("id", "auto", "title", "description") as $column) {
            if ($this->$column) {
                $condition->compareColumn($column, $this->$column, true);
            }
        }
        return new DataProvider(array(
            'modelCondition' => $condition
        ));
    }

    public function beforeDelete(){
        User::updateAll(['title_id' => 0], "title_id = :title", array(':title'=>$this->id)); // update user titles to 0 where this id is used.
        return parent::beforeDelete();
    }
}
