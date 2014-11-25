<?php
/**
 * Created by PhpStorm.
 * User: mirel
 * Date: 31.10.2014
 * Time: 12:02
 */

namespace app\commands;


use app\components\Command;
use app\models\GlobalConfig;
use app\models\User;

/**
 * Class Clean
 * Contains methods to clean db from Users, to any other table that needs cleaning.
 * @package app\commands
 */
class Clean extends Command{

    /**
     * Remove new users that didn't validate the email for more than x days(read from config)
     * Removed deleted accounts if x days passed (read from config)
     */
    public function actionUsers(){
        $newUsers = User::findAll("status = :new AND register_date < DATE_SUB(NOW(), INTERVAL :days DAY)", array(':new' => User::STATUS_NEW, ':days' => GlobalConfig::value('USERS_REMOVE_NEW_AFTER_DAYS')));
        foreach ($newUsers as $user){
            $this->debug($user->name . '<'.$user->email.'> deleted! [new]');
            $user->delete();
        }
        $deletedUsers = User::findAll("status = :deleted AND deleteblock_date < DATE_SUB(NOW(), INTERVAL :days DAY)", array(':deleted' => User::STATUS_DELETED, ':days' => GlobalConfig::value('USERS_REMOVE_DELETED_AFTER_X_DAYS')));
        foreach ($deletedUsers as $user){
            $this->debug($user->name . '<'.$user->email.'> deleted! [delete request on '  . $user->deleteblock_date.']');
            $user->delete();
        }
    }

} 