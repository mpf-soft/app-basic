<?php
/**
 * Created by PhpStorm.
 * User: mirel
 * Date: 23.10.2014
 * Time: 14:27
 */

namespace app\modules\admin\controllers;


use app\components\Controller;
use app\components\htmltools\Messages;
use app\models\UserTitle;

class Usertitles extends Controller{

    public function actionIndex() {
        $model = UserTitle::model();
        if (isset($_GET['UserTitle'])){
            $model->setAttributes($_GET['UserTitle']);
        }
        $this->assign('model', $model);
    }

    public function actionCreate() {
        $model = new UserTitle();
        if (isset($_POST['save'])){
            $model->setAttributes($_POST['UserTitle']);
            if ($model->save()){
                Messages::get()->success("Title saved!");
                $this->getRequest()->goToPage('UserTitles');
            }
        }
        $this->assign('model', $model);
    }

    public function actionEdit($id) {
        $model = UserTitle::findByPk($id);
        if (isset($_POST['save'])){
            $model->setAttributes($_POST['UserTitle']);
            if ($model->save()){
                Messages::get()->success("Title saved!");
                $this->getRequest()->goToPage('UserTitles');
            }
        }
        $this->assign('model', $model);
    }

    public function actionDelete() {
        $models = UserTitle::findAllByPk($_POST['UserTitle']);
        $no = 0;
        foreach ($models as $model){
            $no += (int)$model->delete();
        }
        if ($no !== 1){
            Messages::get()->success("$no titles deleted!");
        } else {
            Messages::get()->success("Title deleted!");
        }
        $this->getRequest()->goBack();
    }
} 