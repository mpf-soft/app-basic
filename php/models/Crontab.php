<?php
/**
 * Created by MPF Framework.
 * Date: 2014-10-10
 * Time: 12:42
 */

namespace app\models;


use app\components\htmltools\Messages;
use mpf\cli\Helper;
use mpf\datasources\sql\DataProvider;
use mpf\datasources\sql\DbModel;
use mpf\datasources\sql\ModelCondition;

/**
 * Class Crontab
 * @package app\models
 * @property int $id
 * @property string $user
 * @property string $interval
 * @property string $command
 * @property string $log
 * @property int $enabled
 * @property string $laststart
 * @property int $pid
 */
class Crontab extends DbModel {

    /**
     * Get database table name.
     * @return string
     */
    public static function getTableName() {
        return "crontab";
    }

    /**
     * Get list of labels for each column. This are used by widgets like form, or table
     * to better display labels for inputs or table headers for each column.
     * @return array
     */
    public static function getLabels() {
        return array(
            'id' => 'Id',
            'user' => 'User',
            'interval' => 'Interval',
            'command' => 'Command',
            'log' => 'Log',
            'enabled' => 'Enabled',
            'laststart' => 'Last Started',
            'pid' => 'Last process id'
        );
    }

    /**
     * Return list of relations for current model
     * @return array
     */
    public static function getRelations() {
        return array();
    }

    public static function getRules() {
        return array();
    }

    public function getDataProvider() {
        $condition = new ModelCondition(array('model' => __CLASS__));

        foreach (array('id', 'user', 'interval', 'command', 'log', 'enabled', 'laststart') as $column) {
            if ($this->$column) {
                $condition->compareColumn($column, $this->$column, !in_array($column, array('id', 'enabled')));
            }
        }
        return new DataProvider(array(
            'modelCondition' => $condition
        ));
    }

    /**
     * Checks if it should execute this cronjob
     * @return bool
     */
    public function toExec(){
        if (!$this->enabled){
            return false;
        }
        return $this->laststart < self::cronLastRun($this->interval);//check if last start is different than last programmed start
    }

    /**
     * Executes current job.
     * @return $this
     */
    public function exec() {
        if ('search' != $this->getAction()) {
            $this->pid = exec($this->command . ' >> ' . $this->log  . ' 2>>' . $this->log . ' & echo $!', $output);
            $this->laststart = date('Y-m-d H:i:s');
        }
        $this->save();
        return $this;
    }


    public function previewLog($lines = 40){
        exec("tail -n $lines {$this->log}", $output);
        return Helper::get()->logToHtml(implode("\n", $output));
    }

    public static function cronParseStr($str) {
        $ivs = array(array(0, 59), array(0, 23), array(1, 31), array(1, 12), array(0, 6));
        $fin = array();
        $tks = explode(' ', $str);
        foreach ($tks as $nr => $val) {
            if (($nr == 5) || ($val == ''))
                return false;
            $pos = explode(',', $val);
            foreach ($pos as $p => $pval) {
                if (is_numeric($pval)) {
                    $fin[$nr][(int) $pval] = 1;
                } else if (preg_match('/^(\*|\d+(-\d+)?)(\/\d+)?/', $pval, $ms)) {
                    $st = (isset($ms[3]) && $ms[3] ? substr($ms[3], 1) : 1);
                    $en = (isset($ms[2]) && $ms[2] ? substr($ms[2], 1) : $ivs[$nr][1]);
                    $be = ($ms[1] != '*' ? (int) $ms[1] : $ivs[$nr][0]);
                    for ($j = $be; $j <= $en; $j += $st)
                        $fin[$nr][$j] = 1;
                } else
                    return false;
            }
        }
        for ($nr = 0; $nr <= 4; $nr++) {
            if (!isset($fin[$nr])) {
                for ($j = $ivs[$nr][0]; $j <= $ivs[$nr][1]; $j += 1)
                    $fin[$nr][$j] = 1;
            }
        }
        return $fin;
    }

    public static function cronLastRun($str) {
        $fin = Crontab::cronParseStr($str);
        $now = time();
        krsort($fin[0]);
        krsort($fin[1]);
        krsort($fin[2]);
        krsort($fin[3]);
        krsort($fin[4]);
        $year = date('Y');
        foreach (array($year, $year - 1) as $Y) {
            foreach ($fin[3] as $m => $j3) {
                if (mktime(0, 0, 0, $m, 1, $Y) > $now)
                    continue;
                foreach ($fin[2] as $d => $j2) {
                    $dti = mktime(0, 0, 0, $m, $d, $Y);
                    if ($dti > $now)
                        continue;
                    $dow = date('w', $dti);
                    if (!isset($fin[4][$dow]))
                        continue;
                    foreach ($fin[1] as $H => $j1) {
                        if (mktime($H, 0, 0, $m, $d, $Y) > $now)
                            continue;
                        foreach ($fin[0] as $i => $j0) {
                            $time = mktime($H, $i, 0, $m, $d, $Y);
                            if ($time <= $now) {
                                return date('Y-m-d H:i:s', $time);
                            }
                        }
                    }
                }
            }
        }
        return false;
    }
}
