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

namespace app\controllers;

use app\components\Controller;
use app\components\htmltools\Messages;
use mpf\datasources\redis\Connection;
use mpf\WebApp;

/**
 * Description of Users
 *
 * @author mirel
 */
class User extends Controller {

    public function actionLogout() {
        WebApp::get()->user()->logout();
        WebApp::get()->request()->goBack();
    }

    /**
     * Page user will see when is searching for it's own profile.
     */
    public function actionProfile($removeFB = null, $removeGoogle = null) {
        if (false === strpos($_SERVER['HTTP_REFERER'], $this->getRequest()->getLinkRoot()) || isset($_GET['code'])) { //if is a request from outsite the website
            if (isset($_GET['code'])) {
                if (WebApp::get()->user()->checkGoogle(true)) {
                    Messages::get()->success("Connected to Google!");
                }
            } else {
                if (WebApp::get()->user()->checkFacebook(true)) {
                    Messages::get()->success("Connected to Facebook!");
                }
            }
        }
        $model = \app\models\User::findByPk(WebApp::get()->user()->id);
        if ($removeFB) {
            $model->fb_id = "";
            if ($model->save(false))
                Messages::get()->success("Disconnected from Facebook!");
        }
        if ($removeGoogle) {
            $model->google_id = "";
            if ($model->save(false))
                Messages::get()->success("Disconnected from Google!");

        }
        $this->assign('model', $model);
    }

    /**
     * This is the page that a user, that is not logged in but tries to access a private section, will see.
     * If the user is already logged in but it still tries to access this page then it will be redirected to home page.
     */
    public function actionLogin() {
        if (WebApp::get()->user()->isConnected()) { // can't login if is already connected
            WebApp::get()->request()->goToPage('home');
        }
        $user = \app\models\User::model('login');
        if (isset($_POST['login']) && $user->setAttributes($_POST['User'])->validate()) {
            if ($user->login()) {
                WebApp::get()->request()->goBack(); // refresh so that it will be redirected back where it has to go.
            }
        }
        $this->assign('model', $user);
    }

    /**
     * Page that is used for new users to register on this website. There is also a similar page on admin module called
     * admin/users/create where admins can create a new user. When an admin create an user then the password is generated
     * automatically and sent to user email address. So not even the admin can see it, just the new user.
     */
    public function actionRegister() {
        if (WebApp::get()->user()->isConnected()) { // can't register if is already connected
            WebApp::get()->request()->goToPage('home');
        }
        $user = new \app\models\User();
        $user->setAction('register');
        if (isset($_POST['register']) && $user->setAttributes($_POST['User'])->validate()) {
            if ($user->register()) {
                Messages::get()->success('Account created!');
                WebApp::get()->request()->goToPage('home');
            }
        }
        $this->assign('user', $user);
    }

    /**
     * The page when a user registered using an external source( Facebook, Steam, Google ) is redirected to fill the
     * extra details that can't be read from that source or that are recommended to be changed ( like maybe real name
     * for forums)
     */
    public function actionRegisterauto() {
        $user = \app\models\User::findByPk(WebApp::get()->user()->id);
        $user->setAction('register-auto');
        if (isset($_POST['save_data_and_login']) && $user->setAttributes($_POST['User'])->validate()) {
            $user->registerAuto();
        }
        $this->assign('model', $user);
    }

    /**
     * Edit general profile options. For more advanced options like email change or password change there are separate
     * actions because those required special confirmations.
     */
    public function actionEdit() {
        $user = \app\models\User::findByPk(WebApp::get()->user()->id)->setAction('user-edit');
        if (isset($_POST['save'])) {
            $user->setAttributes($_POST['User']);
            if ($user->save()) {
                WebApp::get()->user()->name = $user->name;
                Messages::get()->success("Profile saved!");
                $this->getRequest()->goToPage('user', 'profile');
            }
        }
        $this->assign('model', $user);
    }

    /**
     * Change current email address. Email address is not imediately saved, it will first sent a confirmation link to the
     * new address and only after that link has been accessed it will change account email address.
     */
    public function actionEmail() {
        $user = \app\models\User::findByPk(WebApp::get()->user()->id)->setAction('change-email');
        if (isset($_POST['save'])) {
            $user->setAttributes($_POST['User']);
            if ($user->validate() && $user->changeEmail()) {
                Messages::get()->success("Request to change email has been processed! Please click on the confirmation URL from your new email address!");
                $this->getRequest()->goToPage('user', 'profile');
            }
        }
        $this->assign('model', $user);
    }

    /**
     * Change password. In order to change to a new password the current password must be filled and the new password
     * must be typed twice for confirmation.
     */
    public function actionPassword() {
        $user = \app\models\User::findByPk(WebApp::get()->user()->id)->setAction('change-password');
        if (isset($_POST['save'])) {
            $user->setAttributes($_POST['User']);
            if ($user->validate() && $user->changePassword()) {
                Messages::get()->success("Password changed!");
                $this->getRequest()->goToPage('user', 'profile');
            }
        }
        $this->assign('model', $user);
    }

    /**
     * Forgot password. Fill up the email in order to send a pasword reset confirmation to that email address.
     */
    public function actionForgotPassword() {
        if (WebApp::get()->user()->isConnected()) { // can't recover password if is already connected
            WebApp::get()->request()->goToPage('home');
        }
        $user = \app\models\User::model('forgot-password');
        if (isset($_POST['reset_password'])) {
            $user->forgotPassword();
        }
        $this->assign('user', $user);
    }

    /**
     * Reset password for user with the selected code.
     * @param string $code
     */
    public function actionResetPassword($code) {
        $code = explode('_', $code, 2);
        $user = \app\models\User::findByPk($code[0]);
        if (!$user) {
            Messages::get()->error('Invalid code!');
            $this->assign('error', true);
            return;
        }
        $this->assign('error', $user->resetPassword($code));
    }

    /**
     * Sends a request to User to activate account
     * @param $code
     */
    public function actionActivateAccount($code) {
        list($id, $code) = explode(".", $code, 2);
        $user = \app\models\User::findByPk($id);
        if (!$user) {
            $this->assign('error', true);
            return;
        }
        if ($user->validateEmail($code, false)) {
            $this->assign('success', true);
            $this->assign('error', false);
            return;
        }
        $this->assign('error', true);
    }

    /**
     * Sends a request to User to validate new email
     * @param $code
     */
    public function actionValidateEmail($code) {
        list($id, $code) = explode(".", $code, 2);
        $user = \app\models\User::findByPk($id);
        if (!$user) {
            $this->assign('error', true);
            return;
        }
        if ($user->validateEmail($code, true)) {
            $this->assign('success', true);
            $this->assign('error', false);
            return;
        }
        $this->assign('error', true);
    }
}
