<?php
/**
 * Created by MPF Framework.
 * Date: 2014-10-09
 * Time: 17:07
 */

namespace app\models;


use mpf\base\App;
use mpf\datasources\sql\DataProvider;
use mpf\datasources\sql\DbModel;
use mpf\datasources\sql\DbRelations;
use mpf\datasources\sql\ModelCondition;

/**
 * Class Config
 * @package app\models
 * @property int $id
 * @property string $name
 * @property string $value
 * @property string $description
 * @property string $lastupdate_date
 * @property int $lastupdate_user
 * @property \app\models\User $admin
 */
class GlobalConfig extends DbModel {

    /**
     * Get database table name.
     * @return string
     */
    public static function getTableName() {
        return "config_global";
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
            'value' => 'Value',
            'description' => 'Description',
            'lastupdate_date' => 'Last Updated',
            'lastupdate_user' => 'Updated By'
        );
    }

    public static function getRules() {
        return array(
            array('value', 'safe')
        );
    }

    /**
     * Return list of relations for current model
     * @return array
     */
    public static function getRelations() {
        return array(
            'admin' => array(DbRelations::BELONGS_TO, '\\app\\models\\User', 'lastupdate_user')
        );
    }

    /**
     * @return DataProvider
     */
    public function getDataProvider() {
        $condition = new ModelCondition(array('model' => __CLASS__));
        $condition->with = array('admin');
        foreach (array('id', 'name', 'value') as $column) {
            if ($this->$column) {
                $condition->compareColumn($column, $this->$column, !in_array($column, array('id')));
            }
        }
        return new DataProvider(array(
            'modelCondition' => $condition
        ));
    }

    public static function value($key){
        $cache = App::get()->cacheValue('app:GlobalConfig');
        if (!$cache){
            $cache = self::updateCache();
        }
        return isset($cache[$key])?$cache[$key]:null;
    }

    protected static $configValues;

    public static function updateCache(){
        if (self::$configValues){
            return self::$configValues;
        }
        $all = self::findAll();
        $cache = array();
        foreach ($all as $conf){
            $cache[$conf->name] = $conf->value;
        }
        App::get()->cacheSet('app:GlobalConfig', $cache);
        if (!App::get()->cacheExists('app:GlobalConfig')){ // if no cache is used then set it here in a local array.
            self::$configValues = $cache;
        }
        return $cache;
    }
}
