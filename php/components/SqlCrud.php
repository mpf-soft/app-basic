<?php

/*
 * @author Mirel Nicu Mitache <mirel.mitache@gmail.com>
 * @package MPF Framework
 * @link    http://www.mpfframework.com
 * @category core package
 * @version 1.0
 * @since MPF Framework Version 1.0
 * @copyright Copyright &copy; 2011 Mirel Mitache 
 * @license  http://www.mpfframework.com/licence
 * 
 * This file is part of MPF Framework.
 *
 * MPF Framework is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * MPF Framework is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with MPF Framework.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace app\components;

/**
 * Description of SqlCrud
 *
 * @author mirel
 */
class SqlCrudController extends Controller {

    /**
     * Name of the model class to be used.
     * To limit actions change the rights for controller and hide actions from datatable
     * 
     * Model must implement public method for each action with Crud prefix.
     * Example
     *  - ->crudAdd()
     *  - ->crudEdit()
     *  - ->crudDelete()
     *  - ->crudDuplicate()
     *  - ->crudHide()
     *  - ->crudShow()
     * 
     * For view it will get info from current model. 
     * 
     * @var string
     */
    public $modelName;

    /**
     * List of models using datatable widget;
     */
    public function actionIndex() {
        
    }

    public function actionAdd() {
        $class = $this->modelName;
        $model = new $class();
        if (isset($_POST['add'])) {
            if ($model->crudAdd()) {
                $this->request->goToPage(null, 'index');
            }
        }
    }

    public function actionEdit($id) {
        $class = $this->modelName;
        $model = $class::findByPk($id);
        if (isset($_POST['edit'])) {
            if ($model->crudEdit()) {
                $this->request->goToPage(null, 'index');
            }
        }
        $this->assign('model', $model);
    }

    public function actionDelete($id) {
        $class = $this->modelName;
        $model = $class::findByPk($id);
        if (isset($_POST['delete'])) {
            if ($model->crudDelete()) {
                $this->request->goToPage(null, 'index');
            }
        }
    }

    public function actionDuplicate($id) {
        $class = $this->modelName;
        $model = $class::findByPk($id);
        if (isset($_POST['duplicate'])) {
            if ($model->crudDuplicate()) {
                $this->request->goToPage(null, 'index');
            }
        }
    }

    public function actionView($id) {
        $class = $this->modelName;
        $model = $class::findByPk($id);
        $this->assign('model', $model);
    }

    public function actionHide($id) {
        
    }

    public function actionShow($id) {
        
    }

}
