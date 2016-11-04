<?php
/**
 * Created by MPF Framework.
 * Date: 2014-10-09
 * Time: 17:08
 */

namespace app\models;


use mpf\base\App;
use mpf\datasources\sql\DbModel;
use mpf\datasources\sql\DbRelations;
use mpf\WebApp;

/**
 * Class UserConfig
 * @package app\models
 * @property int $id
 * @property string $name
 * @property string $value
 * @property int $user_id
 * @property \app\models\User $user
 */
class UserConfig extends DbModel
{

    /**
     * Get database table name.
     * @return string
     */
    public static function getTableName()
    {
        return "config_user";
    }

    /**
     * Get list of labels for each column. This are used by widgets like form, or table
     * to better display labels for inputs or table headers for each column.
     * @return array
     */
    public static function getLabels()
    {
        return array(
            'id' => 'Id',
            'name' => 'Name',
            'value' => 'Value',
            'user_id' => 'User'
        );
    }

    /**
     * Return list of relations for current model
     * @return array
     */
    public static function getRelations()
    {
        return array(
            'user' => array(DbRelations::BELONGS_TO, '\app\models\User', 'user_id')
        );
    }

    public static function getRules()
    {
        return array();
    }

    /**
     * Get value for selected key. If it doesn't exists then return null.
     * @param string $key
     * @param string $default
     * @param int|null $userId
     * @return null|string
     */
    public static function value($key, $default = null, $userId = null)
    {
        $userId = ($userId ?: (WebApp::get()->user()->isConnected() ? WebApp::get()->user()->id : null));
        if (!$userId) {
            return $default;
        }
        $cache = App::get()->cacheValue('app:UserConfig:' . $userId);
        if (!$cache) {
            $cache = self::updateCache($userId);
        }
        if (!isset($cache[$key])) {
            self::set($key, (string)$default, $userId);
        }
        return isset($cache[$key]) ? $cache[$key] : $default;
    }

    protected static $configValues;

    /**
     * Update cache with config values
     * @param int|null $userId
     * @param bool $force
     * @return array
     */
    public static function updateCache($userId = null, $force = false)
    {
        if (self::$configValues && !$force) {
            return self::$configValues;
        }
        $userId = $userId ?: WebApp::get()->user()->id;
        $all = self::findAllByAttributes(['user_id' => $userId]);
        $cache = array();
        foreach ($all as $conf) {
            $cache[$conf->name] = $conf->value;
        }
        App::get()->cacheSet('app:UserConfig:' . $userId, $cache);
        if (!App::get()->cacheExists('app:UserConfig:' . $userId)) { // if no cache is used then set it here in a local array.
            self::$configValues = $cache;
        }
        return $cache;
    }

    /**
     * Update(/insert) config value for selected user.
     * @param string $key
     * @param string $value
     * @param null|int $user
     * @return bool|int
     */
    public static function set($key, $value, $user = null)
    {
        $user = $user ?: WebApp::get()->user()->id;
        $s = self::getDb()->table(self::getTableName())->insert(['name' => $key, 'value' => $value, 'user_id' => $user], ['value' => $value]);
        self::updateCache($user, true);
        return $s;
    }
}
