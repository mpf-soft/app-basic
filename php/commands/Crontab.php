<?php
/**
 * Created by PhpStorm.
 * User: mirel
 * Date: 31.10.2014
 * Time: 09:43
 */

namespace app\commands;

use app\components\Command;

/**
 * Class Crontab
 * Will execute all cron jobs from web interface.
 * @package app\commands
 */
class Crontab extends Command{

    /**
     * To be executed every minute. It will check jobs from crontab list and will run what it needs to run.
     */
    public function actionIndex(){
        $jobs = \app\models\Crontab::findAllByAttributes(['enabled' => 1, 'user' => array(exec('whoami'), '*')]);
        foreach ($jobs as $job){
            if ($job->toExec()){
                $this->debug("running " . $job->command);
                $job->exec();
            }
        }
    }
} 