<?php
/**
 * Created by PhpStorm.
 * User: mirel
 * Date: 10.10.2014
 * Time: 13:40
 */

namespace app\modules\admin\controllers;


use app\components\Controller;
use app\components\htmltools\Messages;
use app\components\htmltools\Tools;
use app\models\Crontab;
use app\models\GlobalConfig;
use mpf\datasources\sql\ModelCondition;
use mpf\WebApp;

class Admin extends Controller {
    /**
     * Manages list of cronjobs
     * @param null|int $delete
     */
    public function actionCrontab($delete = null) {
        if (isset($_POST['action'])) {
            $condition = new ModelCondition(array('model' => Crontab::className()));
            $condition->compareColumn('id', $_POST['Crontab']);
            switch ($_POST['action']) {
                case 'enable':
                    $n = Crontab::updateAll(array('enabled' => 1), $condition);
                    Messages::get()->success("$n jobs enabled!");
                    break;
                case 'disable':
                    $n = Crontab::updateAll(array('enabled' => 0), $condition);
                    Messages::get()->success("$n jobs disabled!");
                    break;
            }
            $this->getRequest()->goBack();
        }
        if ($delete && is_int($delete)) {
            $success = Crontab::findByPk($delete)->delete();
            if ($success) {
                Messages::get()->success('Cron job has been removed!');
            } else {
                Messages::get()->warning('There was an error while trying to delete the job!');
            }
            $this->getRequest()->goBack();
        }

        $model = Crontab::model();
        if (isset($_GET['Crontab'])) {
            $model->setAttributes($_GET['Crontab']);
        }
        $this->assign('model', $model);
    }

    public function actionAddCron() {
        $model = new Crontab();
        if (isset($_POST['add'])) {
            $model->setAttributes($_POST['Crontab']);
            if ($model->save()) {
                Messages::get()->success('Cron job was saved!');
                $this->getRequest()->goToPage('admin', 'crontab');
            } else {
                Messages::get()->warning('Can\'t save the cron job! Please check the errors below!');
            }
        }
        $this->assign('model', $model);
    }

    public function actionEditCron($id) {
        $model = Crontab::findByPk($id);
        if (!$model) {
            Messages::get()->error('Job not found!');
            $this->getRequest()->goToPage('admin', 'crontab');
        }
        if (isset($_POST['save'])) {
            $model->setAttributes($_POST['Crontab']);
            if ($model->save()) {
                Messages::get()->success('Cron job was saved!');
                $this->getRequest()->goToPage('admin', 'crontab');
            } else {
                Messages::get()->warning('Can\'t save the cron job! Please check the errors below!');
            }
        }
        $this->assign('model', $model);
    }

    public function actionExecCron($id) {
        $job = Crontab::findByPk($id);
        $job->exec();
        $this->getRequest()->goToPage('admin', 'view', array('id' => $job->id));
    }

    public function actionCronlogpreview($id) {
        $this->assign('model', Crontab::findByPk($id));
    }

    /**
     * @param null|int $id
     * @param bool|int $autoupdate False for no ajax update, true for update each 10 seconds or int for update at the specified number of seconds. Also 0 for real-time update.
     * @param null|int $download
     */
    public function actionView($id = null, $autoupdate = false, $download = null) {
        if ($download) {
            $model = Crontab::findByPk($download);
            Tools::serveFile($model->log, basename($model->log));
        }
        $models = Crontab::findAllByPk($id ? $id : $_POST['Crontab']);
        if (isset($_POST['ajax_update'])) {

            die();
        }
        if (true === $autoupdate) {
            $autoupdate = 10;
        }
        $this->assign('models', $models);
        $this->assign("autoupdate", $autoupdate);
    }

    public function actionDelete() {
        $labels = array('Crontab' => array('cron jobs', 'Cron job'));
        foreach ($_POST as $name => $values) {
            if (in_array($name, array('Crontab'))) {
                $cls = '\\app\models\\' . $name;
                $model = $cls::findAllByPk($values);
                if ($model) {
                    $number = 0;
                    foreach ($model as $m) {
                        $number += (int)$m->delete();
                    }
                    if (1 !== $number) {
                        Messages::get()->success($number . ' ' . $labels[$name][0] . ' deleted!');
                    } else {
                        Messages::get()->success($labels[$name][1] . ' deleted!');
                    }
                }
            }
        }
        $this->getRequest()->goBack();
    }

    public function actionConfig() {
        $model = GlobalConfig::model();
        if (isset($_GET['GlobalConfig'])) {
            $model->setAttributes($_GET['GlobalConfig']);
        }
        $this->assign('model', $model);
    }

    public function actionEditConfig($id = null) {
        if (!$id) {
            $id = isset($_POST['save']) ? array_keys($_POST['GlobalConfig']) : $_POST['GlobalConfig'];
        }
        $ms = GlobalConfig::findAllByPk($id);
        $models = array();
        foreach ($ms as $model) {
            $models[$model->id] = $model;
        }
        if (isset($_POST['save'])) {
            $ok = true;
            foreach ($_POST['GlobalConfig'] as $k => $details) {
                $models[$k]->setAttributes($details);
                $models[$k]->lastupdate_date = date('Y-m-d H:i:s');
                $models[$k]->lastupdate_user = WebApp::get()->user()->id;
                $ok = $ok && $models[$k]->save();
            }
            if ($ok) {
                Messages::get()->info('Changes saved!');
                $this->getRequest()->goToPage('admin', 'config');
            }
        }

        $this->assign('models', $models);
    }
} 