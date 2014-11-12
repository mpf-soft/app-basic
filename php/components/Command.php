<?php
/**
 * Created by PhpStorm.
 * User: mirel
 * Date: 31.10.2014
 * Time: 09:44
 */

namespace app\components;


use mpf\base\App;

class Command extends \mpf\cli\Command {

    const REDIS_LOCKS = ':ConsoleLocks';

    /**
     * Prevents more than one instance for the same action
     * @var bool
     */
    public $lockProcess = true;

    public function beforeAction($actionName) {
        if ($this->debug){
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                echo "\n\n\n\n\n";
            } else {
                passthru('clear');
            }
        }
        return (!$this->lockProcess || $this->checkLock($actionName)) && parent::beforeAction($actionName);
    }

    public function afterAction($actionName, $result) {
        $this->clearLock($actionName);
        return parent::afterAction($actionName, $result);
    }

    /**
     * Checks if there is a lock on selected action, if there isn't then it will create one.
     * @param $actionName
     * @return bool
     */
    protected function checkLock($actionName) {
        $command = str_replace('\\', '_', get_class($this));
        $oldPid = App::get()->redis()->hget(App::get()->shortName.self::REDIS_LOCKS, $command . ':' . $actionName);
        if (!$oldPid) {
            $this->setLock($actionName, $command);
            return true;
        }

        if (!$this->isPIDRunning($oldPid)) {
            $this->setLock($actionName, $command);
            return true;
        }

        return false;
    }

    /**
     * Set a lock for selected action & command
     * @param $actionName
     * @param null $command
     */
    protected function setLock($actionName, $command = null) {
        $command = $command ?: str_replace('\\', '_', get_class($this));
        App::get()->redis()->hset(App::get()->shortName.self::REDIS_LOCKS, $command . ':' . $actionName, getmypid());
    }

    /**
     * Clear lock for selected action and command;
     * @param $actionName
     * @param null $command
     */
    protected function clearLock($actionName, $command = null) {
        $command = $command ?: str_replace('\\', '_', get_class($this));
        App::get()->redis()->hdel(App::get()->shortName.self::REDIS_LOCKS, $command . ':' . $actionName);
    }

    /**
     * Checks if selected process is still active or not
     * @param int $pid
     * @return bool
     */
    protected function isPIDRunning($pid) {
        exec("ps $pid", $processState);
        return (count($processState) >= 2);
    }
} 