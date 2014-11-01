<?php
/**
 * Created by MPF Framework.
 * Date: 2014-10-09
 * Time: 17:08
 */

namespace app\models;


use mpf\datasources\sql\DbModel;
use mpf\datasources\sql\DbRelations;
use mpf\WebApp;

/**
 * Class UserConfig
 * @package app\models
 * @property int $id
 * @property string $name
 * @property string $value
 * @property int $label
 * @property int $user_id
 * @property \app\models\User $user
 */
class UserConfig extends DbModel {

    /**
     * Get database table name.
     * @return string
     */
    public static function getTableName() {
        return "config_user";
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
             'label' => 'Label',
             'user_id' => 'User'
        );
    }

    /**
     * Return list of relations for current model
     * @return array
     */
    public static function getRelations(){
        return array(
             'user' => array(DbRelations::BELONGS_TO, '\app\models\User', 'user_id')
        );
    }

    public static function getRules(){
        return array(

        );
    }

    public static function value($key){
        $cache = App::get()->cacheValue('app:UserConfig:'. WebApp::get()->user()->id);
        if (!$cache){
            $cache = self::updateCache();
        }
        return $cache[$key]?:null;
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
        App::get()->cacheSet('app:UserConfig:'. WebApp::get()->user()->id, $cache);
        if (!App::get()->cacheExists('app:UserConfig:'. WebApp::get()->user()->id)){ // if no cache is used then set it here in a local array.
            self::$configValues = $cache;
        }
        return $cache;
    }
}
