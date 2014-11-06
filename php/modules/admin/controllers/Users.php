<?php
/**
 * Created by PhpStorm.
 * User: Mirel Mitache
 * Date: 19.10.2014
 * Time: 13:17
 */

namespace app\modules\admin\controllers;


use app\components\Controller;
use app\components\htmltools\Messages;
use app\models\User;
use app\models\UserHistory;
use mpf\datasources\sql\ModelCondition;
use mpf\WebApp;

class Users extends Controller {
    public function actionIndex() {
        $model = User::model();
        $model->setAttributes(isset($_GET['User']) ? $_GET['User'] : array());
        $this->assign('model', $model);
    }

    public function actionCreate() {
        $model = new User();
        $model->setAction('admin-insert');
        if (isset($_POST['save'])) {
            $model->setAttributes($_POST['User']);
            if ($model->validate() && $model->adminCreate()) {
                Messages::get()->success("User created!");
                $this->getRequest()->goToPage('users', 'index');
            }
        }
        $this->assign('model', $model);
    }

    public function actionView($id, $removeFB =null, $removeGoogle =null) {
        $model = User::findByPk($id);
        $this->assign('model', $model);
    }

    public function actionEdit($id) {
        $model = User::findByPk($id);
        $model->setAction('admin-edit');
        $model->refreshGroupsIDs();
        if (isset($_POST['save'])) {
            $model->setAttributes($_POST['User']);
            if ($model->saveGroups()->save()) {
                Messages::get()->success("User saved!");
                $this->getRequest()->goToPage('users', 'index');
            }
        }
        $this->assign('model', $model);
    }

    public function actionDelete() {
        $models = User::findAllByPk($_POST['User']);
        $number = 0;
        $names = [];
        foreach ($models as $model) {
            $names[] = $model->name;
            $number += (int)$model->delete();
        }
        User::findByPk(WebApp::get()->user()->id)->logAction(UserHistory::ACTION_ADMINDELETE, "Users: \n", implode("\n   ", $names));
        if (1 !== $number) {
            Messages::get()->success("User deleted!");
        } else {
            Messages::get()->success("$number users deleted!");
        }
        $this->getRequest()->goBack();
    }

    public function actionMerge() {
        $models = User::findAllByPk($_POST['User']);
        // @TODO: Write merge code!
        Messages::get()->warning("Accounts merges not yet implemented!");
        $this->getRequest()->goBack();
    }
} 