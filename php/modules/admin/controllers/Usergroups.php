<?php
/**
 * Created by PhpStorm.
 * User: Mirel Mitache
 * Date: 19.10.2014
 * Time: 13:23
 */

namespace app\modules\admin\controllers;


use app\components\Controller;
use app\components\htmltools\Messages;
use app\models\UserGroup;

class Usergroups extends Controller {

    public function actionIndex() {
        $model = UserGroup::model();
        if (isset($_GET['UserGroup'])){
            $model->setAttributes($_GET['UserGroup']);
        }
        $this->assign('model', $model);
    }

    public function actionCreate() {
        $model = new UserGroup();
        if (isset($_POST['save'])){
            $model->setAttributes($_POST['UserGroup']);
            if ($model->save()){
                Messages::get()->success("Group saved!");
                $this->getRequest()->goToPage('usergroups');
            }
        }
        $this->assign('model', $model);
    }

    public function actionEdit($id) {
        $model = UserGroup::findByPk($id);
        if (isset($_POST['save'])){
            $model->setAttributes($_POST['UserGroup']);
            if ($model->save()){
                Messages::get()->success("Group saved!");
                $this->getRequest()->goToPage('usergroups');
            }
        }
        $this->assign('model', $model);
    }

    public function actionDelete() {
        $models = UserGroup::findAllByPk($_POST['UserGroup']);
        $no = 0;
        foreach ($models as $model){
            $no += (int)$model->delete();
        }
        if ($no !== 1){
            Messages::get()->success("$no groups deleted!");
        } else {
            Messages::get()->success("Group deleted!");
        }
        $this->getRequest()->goBack();
    }
} 