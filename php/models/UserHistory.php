<?php
/**
 * Created by MPF Framework.
 * Date: 2014-10-31
 * Time: 12:28
 */

namespace app\models;

use mpf\base\App;
use mpf\datasources\sql\DataProvider;
use mpf\datasources\sql\DbModel;
use mpf\datasources\sql\DbRelations;
use mpf\datasources\sql\ModelCondition;
use mpf\WebApp;

/**
 * Class UserHistory
 * @package app\models
 * @property int $id
 * @property int $user_id
 * @property int $action
 * @property int $admin_id
 * @property string $comment
 * @property string $ip
 * @property \app\models\User $user
 */
class UserHistory extends DbModel {

    const ACTION_CREATED = 1;
    const ACTION_DELETED = 2;
    const ACTION_VALIDATED = 3;
    const ACTION_PASSWORDRESETREQUEST = 4;
    const ACTION_PASSWORDRESETCHANGED = 5;
    const ACTION_PASSWORDCHANGED = 6;
    const ACTION_EMAILCHANGED = 7;
    const ACTION_CRONTAB = 8;
    const ACTION_GROUPS = 9;
    const ACTION_MERGED = 10;
    const ACTION_ADMINDELETE = 11;

    /**
     * Get database table name.
     * @return string
     */
    public static function getTableName() {
        return "users_history";
    }

    /**
     * Get list of labels for each column. This are used by widgets like form, or table
     * to better display labels for inputs or table headers for each column.
     * @return array
     */
    public static function getLabels() {
        return array(
            'id' => 'Id',
            'user_id' => 'User',
            'action' => 'Action',
            'admin_id' => 'Admin',
            'comment' => 'Comment',
            'ip' => 'IP'
        );
    }

    /**
     * Return list of relations for current model
     * @return array
     */
    public static function getRelations() {
        return array(
            'user' => array(DbRelations::BELONGS_TO, '\app\models\User', 'user_id')
        );
    }

    /**
     * List of rules for current model
     * @return array
     */
    public static function getRules() {
        return array(
            array("id, user_id, action, admin_id, comment, ip", "safe", "on" => "search")
        );
    }

    public static function addEntry($user, $action, $comment = null) {
        $webApp = is_a(App::get(), '\\mpf\\WebApp');
        return self::insert([
            'user_id' => $user,
            'action' => $action,
            'admin_id' => $webApp ? ((WebApp::get()->user()->id == $user) ? null : WebApp::get()->user()->id) : null,//checks if active user is different than updated user
            'comment' => $comment,
            'ip' => $webApp ? $_SERVER['REMOTE_ADDR'] : null // if is a web app then it will get REMOTE_ADDR
        ]);
    }

    /**
     * Gets DataProvider used later by widgets like \mpf\widgets\datatable\Table to manage models.
     * @return \mpf\datasources\sql\DataProvider
     */
    public function getDataProvider() {
        $condition = new ModelCondition(array('model' => __CLASS__));

        foreach (array("id", "user_id", "action", "admin_id", "comment", "ip") as $column) {
            if ($this->$column) {
                $condition->compareColumn($column, $this->$column, true);
            }
        }
        return new DataProvider(array(
            'modelCondition' => $condition
        ));
    }
}
