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

namespace app\models;

use app\components\Emails;
use app\components\htmltools\Messages;
use Facebook\FacebookRequest;
use Facebook\FacebookSession;
use Facebook\GraphUser;
use mpf\base\App;
use mpf\datasources\sql\DataProvider;
use mpf\datasources\sql\DbModel;
use mpf\datasources\sql\DbRelations;
use mpf\datasources\sql\ModelCondition;
use mpf\helpers\ArrayHelper;
use mpf\tools\Validator;
use mpf\web\helpers\Html;
use mpf\WebApp;

/**
 * Description of User
 *
 * @author mirel
 * @property \app\models\UserHistory[] $logs User logs
 * @property \app\models\UserGroup[] $groups
 * @property \app\models\UserTitle $title
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property string $register_date
 * @property string $last_login
 * @property string $last_login_source
 * @property string $fb_id
 * @property string $google_id
 * @property int $status
 * @property int $title_id
 * @property string $new_email
 * @property int $createdbyadmin_id
 * @property int $joinuser_id
 * @property string $deleteblock_date
 * @property string $lastconfirmationmail_date
 */
class User extends DbModel {

    const STATUS_NEW = 0; // right after account creation, before activation
    const STATUS_ACTIVE = 1; // when account it's active
    const STATUS_BLOCKED = 2; // account blocked
    const STATUS_DELETED = 3; // account deleted by user (it will be deleted in a few days from DB)
    const TYPE_VISITOR = 0; // normal user
    /**
     * CHANGE THIS VALUE FOR EACH APPLICATION CREATED! KEEP IT FOR INTERNAL APPLICATIONS ONLY IF YOU NEED TO IMPORT USERS
     * FROM ONE APP TO ANOTHER.
     *
     * TIP: When a security problem occurs and passwords must be reseted you can simple change this value. No old password
     * will be matched and all users will have to reset it.
     */
    const PASSWORD_SALT = '342!$!@D#ASDA3d44';

    /**
     * Used by forms to change password and email.
     * @var string
     */
    public $newPassword, $repeatedPassword, $oldPassword, $newEmail;

    public $groupIDs;
    public $comment;
    /**
     * Allow confirmation email re-send in certain cases.
     * @var bool
     */
    public static $allowConfirmationEmailResend = false;

    public static function getStatuses($except = null){
        $list =  [
            self::STATUS_NEW => 'New',
            self::STATUS_ACTIVE => 'Active',
            self::STATUS_BLOCKED => 'Blocked',
            self::STATUS_DELETED => 'Deleted'
        ];
        if (is_null($except)){
            return $list;
        }
        if (is_array($except)){
            foreach ($except as $value){
                unset($list[$value]);
            }
        } else {
            unset($list[$except]);
        }
        return $list;
    }

    public static function getRelations() {
        return array(
            'groups' => array(DbRelations::MANY_TO_MANY, '\app\models\UserGroup', 'users2groups(user_id, group_id)'),
            'logs' => array(DbRelations::HAS_MANY, '\app\models\UserHistory', 'user_id'),
            'title' => array(DbRelations::BELONGS_TO, '\app\models\UserTitle', 'title_id')
        );
    }

    /**
     *
     * @return string;
     */
    public static function getTableName() {
        return 'users';
    }

    /**
     * Get a hashed password from a plain text
     * @param string $password
     * @return string
     */
    public static function hashPassword($password) {
        return md5(self::PASSWORD_SALT . $password);
    }

    public static function getRules() {
        return array(
            array('name, email, comment', 'required, safe', 'on' => 'admin-insert'),
            array('name, email, newPassword, repeatedPassword', 'required, safe', 'on' => 'register'),
            array('email, newEmail', 'unique, email', 'column' => 'email', 'on' => 'register, change-email'), // specified column to be used for newEmail also
            array('name', 'safe', 'on' => 'user-edit'),
            array('name, newPassword, repeatedPassword', 'safe, required', 'on' => 'register-auto'),
            array('name, email, groupIDs, status, title_id', 'safe', 'on' => 'admin-edit'),
            array('name, password', 'required, safe', 'on' => 'login'),
            array('name', 'unique', 'on' => 'register'),
            array('newEmail, oldPassword', 'required', 'on' => 'change-email'),
            array('email', 'required', 'on' => 'forgot-password'),
            array('oldPassword, newPassword, repeatedPassword', 'required', 'on' => 'change-password'),
            array('repeatedPassword', 'compare', 'column' => 'newPassword'),
            array('oldPassword', function (Validator $validator, $field, $options, $label, $message) { // check if old password is correct
                if (User::hashPassword($validator->getValue($field)) == User::findByPk(WebApp::get()->user()->id)->password) {
                    return true;
                }
                throw new \Exception($message ? $message : $validator->translate($label . ' is wrong!'));
            }, 'on' => 'change-password, change-email')
        );
    }

    public static function getLabels() {
        return array(
            'newPassword' => 'New Password',
            'repeatedPassword' => 'Repeat Password',
            'oldPassword' => 'Old Password',
            'newEmail' => 'New Email',
            'title_id' => 'Title',
            'fb_id' => 'Facebook Connection',
            'google_id' => 'Google Connection',
            'groupIDs' => 'Groups'
        );
    }

    /**
     * Logs in current searched user. Can be used only if model action is login.
     */
    public function login() {
        if ($this->getAction() == 'login') { // can only be called if current action is login so that login data can be checked.
            // including possible captcha codes or anything else like that.
            return WebApp::get()->user()->login($this->name, $this->password, 'post', false);
        }
    }

    /**
     * Update this with a better way to generate more complex passwords!
     * @return string
     */
    public static function generateRandomPassword(){
        return uniqid();
    }

    /**
     * @return bool
     */
    public function adminCreate(){
        $this->password = self::hashPassword($password = self::generateRandomPassword());
        $this->register_date = date('Y-m-d H:i:s');
        $this->status = self::STATUS_ACTIVE;
        $this->title_id = GlobalConfig::value('USERS_DEFAULT_TITLE_ID');
        $groups = explode(",", GlobalConfig::value('USERS_DEFAULT_GROUP_IDS'));
        $this->createdbyadmin_id = WebApp::get()->user()->id;
        if ($this->save(false)){
            if (!Emails::get()->sendPasswordToNewAccount($this, $password)){
                Messages::get()->error('There was an error when trying to send confirmation email! Please try again!');
                $this->delete();
                return false;
            }
            $connectionTable =WebApp::get()->sql()->table('users2groups');
            foreach ($groups as $id){
                $connectionTable->insert(array('user_id' => $this->id, 'group_id' => trim($id)));
            }
            $this->logAction(UserHistory::ACTION_CREATED, $this->comment);
            return true;
        }
        return false;
    }

    /**
     * Register current user; to be called after validation
     */
    public function register() {
        $this->password = self::hashPassword($this->newPassword);
        $this->register_date = date('Y-m-d H:i:s');
        $this->status = self::STATUS_NEW;
        $this->title_id = GlobalConfig::value('USERS_DEFAULT_TITLE_ID');
        $this->lastconfirmationmail_date = date('Y-m-d H:i:s');
        $groups = explode(",", GlobalConfig::value('USERS_DEFAULT_GROUP_IDS'));
        if ($this->save(false)) {
            if (!Emails::get()->sendToNewAccount($this)) {
                Messages::get()->error('There was an error when trying to send confirmation email! Please try again!');
                $this->delete(); // delete user from DB so that it can be inserted again with a second try.
                return false;
            }
            $connectionTable =WebApp::get()->sql()->table('users2groups');
            foreach ($groups as $id){
                $connectionTable->insert(array('user_id' => $this->id, 'group_id' => trim($id)));
            }
            $this->logAction(UserHistory::ACTION_CREATED);
            return true;
        }
        return false;
    }

    public function resendConfirmationEmail($action = 'register'){
        $this->setAction($action);
        if('register' == $action) {
            if (!Emails::get()->sendToNewAccount($this)) {
                Messages::get()->error("There was an error when trying to send confirmation email! Please try again later!");
                return false;
            } else {
                Messages::get()->success("A confirmation link was sent to the specified email address!");
                return true;
            }
        } else {
            if (!Emails::get()->sentToEmailChange($this)){
                Messages::get()->error("There was an error when trying to send confirmation email! Please try again later!");
                return false;
            } else {
                Messages::get()->success("A confirmation link was sent to the specified email address!");
                return true;
            }
        }
    }

    /**
     * Save extra details after account was created using an external source like Facebook, Google, Steam
     */
    public function registerAuto() {
        $this->password = self::hashPassword($this->newPassword);
        if ($this->save()){
            WebApp::get()->user()->name = $this->name;
            $this->reload();
        }
    }

    /**
     * Validates new emails or account email for new accounts.
     * @param string $code Code used to check if it should validate or not
     * @param bool $isNew true to change to a new email address or false if current address must be verified
     * @return bool
     */
    public function validateEmail($code, $isNew = false) {
        $this->logAction(UserHistory::ACTION_VALIDATED);
        $correctCode = md5(($isNew ? $this->new_email : $this->email) . $this->register_date . self::PASSWORD_SALT);
        if ($code != $correctCode) {
            Messages::get()->error('Invalid code used!');
            return false;
        }
        if (!$isNew) {
            if ($this->status == self::STATUS_NEW) {
                $this->status = self::STATUS_ACTIVE;
                $this->save();
                return true;
            } else {
                Messages::get()->error('This account is already active!');
                return false;
            }
        }
        $this->email = $this->new_email;
        $this->new_email = null;
        $this->save();
        return true;
    }

    /**
     * Sends an email with a link to reset password
     * @return bool
     */
    public function forgotPassword() {
        $user = self::findByAttributes(['email' => $this->email]);
        if (!$user) {
            Messages::get()->error('User not found!');
            return false;
        }
        $code = $user->id.'_'.md5($user->register_date.$user->email . 'password' . self::PASSWORD_SALT);
        $user->logAction(UserHistory::ACTION_PASSWORDRESETREQUEST);
        if (!Emails::get()->sendPasswordForgot($user, $code)){
            Messages::get()->error("There was a problem while trying to send the email!");
            return false;
        }
        return true;
    }

    /**
     * Send new password to user.
     * @param $code
     * @return bool
     */
    public function resetPassword($code) {
        if ($code != $this->id.'_'.md5($this->register_date.$this->email . 'password' . self::PASSWORD_SALT)){
            Messages::get()->error('Invalid code!');
            return false;
        }
        $newPassword = self::generateRandomPassword();
        $this->password = self::hashPassword($newPassword);
        $this->save();
        $this->logAction(UserHistory::ACTION_PASSWORDRESETCHANGED);
        if (!Emails::get()->sendGeneratedPassword($this, $newPassword)){
            Messages::get()->error('Error while sending email!');
            return false;
        }
        return true;
    }

    /**
     * Send email to change current email with a new address.
     * @return bool
     */
    public function changeEmail() {
        $this->new_email = $this->newEmail;
        $this->lastconfirmationmail_date = date('Y-m-d H:i:s');
        if ($this->save()){
            if (!Emails::get()->sentToEmailChange($this)){
                $this->new_email = null;
                $this->save();
                Messages::get()->error('Error while sending email!');
                return false;
            }
            $this->logAction(UserHistory::ACTION_EMAILCHANGED, "Old email: " . $this->email . "\nNew email: " . $this->new_email);
            return true;
        }
        return false;
    }

    public function changePassword() {
        $this->logAction(UserHistory::ACTION_PASSWORDCHANGED);
        $this->password = self::hashPassword($this->newPassword);
        return $this->save();
    }

    public function getActivationLink() {
        if ($this->getAction() == 'register') { // new account activation link
            $code = $this->id . '.' . md5($this->email . $this->register_date . self::PASSWORD_SALT);
            return WebApp::get()->request()->createURL('user', 'activateAccount', array('code' => $code));
        } elseif ($this->getAction() == 'change-email') { // email update link
            $code = $this->id . '.' . md5($this->new_email . $this->register_date . self::PASSWORD_SALT);
            return WebApp::get()->request()->createURL('user', 'validateEmail', array('code' => $code));
        }
    }

    /**
     * Register or updates an user based on google details
     * @param $details
     * @return User
     */
    public static function googleRegister($details){
        if (!is_null($old = self::findByAttributes(['email' => $details['email']]))){
            $old->google_id = $details['id'];
            if ($old->status == self::STATUS_NEW){
                $old->status = self::STATUS_ACTIVE;
            }
            $old->save(false);
            UserConfig::set('GOOGLE_NAME', $details['name'], $old->id);
            UserConfig::set('GOOGLE_EMAIL', $details['email'], $old->id);
            UserConfig::set('GOOGLE_PROFILE', $details['profile_url'], $old->id);
            UserConfig::set('GOOGLE_IMAGE', $details['image_url'], $old->id);
            return $old;
        }
        $user = new User();
        $user->google_id = $details['id'];
        $user->email = $details['email'];
        $user->register_date = date('Y-m-d H:i:s');
        $user->status = self::STATUS_ACTIVE;
        $user->save(false);
        UserConfig::set('GOOGLE_NAME', $details['name'], $user->id);
        UserConfig::set('GOOGLE_EMAIL', $details['email'], $user->id);
        UserConfig::set('GOOGLE_PROFILE', $details['profile_url'], $user->id);
        UserConfig::set('GOOGLE_IMAGE', $details['image_url'], $user->id);
        return $user;
    }

    /**
     * Register user using facebook data. It will also check if email already exists and it will update that user.
     * @param GraphUser $me
     * @return User|null
     */
    public static function facebookRegister(GraphUser $me) {
        // 1. check if email already exists.
        $old = self::findByAttributes(array('email' => $me->getEmail()));
        if ($old) {
            /* @var $old User */
            $old->fb_id = $me->getId();
            if ($old->status == self::STATUS_NEW) {
                $old->status = self::STATUS_ACTIVE;
            }
            $old->save(false);
            UserConfig::set('FACEBOOK_NAME', $me->getFirstName() . ' ' . $me->getMiddleName() . ' ' . $me->getLastName(), $old->id);
            UserConfig::set('FACEBOOK_EMAIL', $me->getProperty('email'), $old->id);
            UserConfig::set('FACEBOOK_PROFILE', $me->getLink(), $old->id);
            return $old;
        }
        $user = new User();
        $user->fb_id = $me->getId();
        $user->email = $me->getProperty('email');
        $user->register_date = date('Y-m-d H:i:s');
        $user->status = self::STATUS_ACTIVE;
        $user->save(false);
        UserConfig::set('FACEBOOK_NAME', $me->getFirstName() . ' ' . $me->getMiddleName() . ' ' . $me->getLastName(), $user->id);
        UserConfig::set('FACEBOOK_EMAIL', $me->getProperty('email'), $user->id);
        UserConfig::set('FACEBOOK_PROFILE', $me->getLink(), $user->id);
        return $user;
    }

    public static function githubRegister(){
        //@TODO: GitHub Registration
        trigger_error("GitHub Registration not implemented!");
    }

    public static function yahooRegister(){
        //@TODO: Yahoo Registration
        trigger_error("Yahoo Registration not implemented!");
    }

    public static function twitterRegister(){
        //@TODO: Twitter Registration
        trigger_error("Twitter Registration not implemented!");
    }

    public static function windowsRegister(){
        //@TODO: Windows Registration
        trigger_error("Windows Registration not implemented!");
    }

    /**
     * @return DataProvider
     */
    public function getDataProvider(){
        $condition = new ModelCondition(array('model' => __CLASS__));

        foreach (array('id', 'name', 'email', 'status', 'register_date', 'last_login', 'last_login_source') as $column) {
            if ($this->$column) {
                $condition->compareColumn($column, $this->$column, !in_array($column, array('id', 'status', 'last_login_source')));
            }
        }
        return new DataProvider(array(
            'modelCondition' => $condition
        ));
    }

    /**
     * User status as string to display
     * @return mixed
     */
    public function getStringStatus(){
        $statuses = self::getStatuses();
        return $statuses[$this->status];
    }

    /**
     * Update GroupIDs for form or any other use.
     * @return $this
     */
    public function refreshGroupsIDs(){
        if (count($this->groups)){
            $this->groupIDs = ArrayHelper::get()->transform($this->groups, 'id');
        } else {
            $this->groupIDs = array();
        }
        return $this;
    }

    /**
     * Update db connection to selected groups
     * @return $this
     */
    public function saveGroups(){
        $old = ArrayHelper::get()->transform($this->groups, 'name');
        WebApp::get()->sql()->table('users2groups')->where("user_id = :id")->setParam(':id', $this->id)->delete();
        $connectionTable =WebApp::get()->sql()->table('users2groups');
        foreach ($this->groupIDs as $id){
            $connectionTable->insert(array('user_id' => $this->id, 'group_id' => $id));
        }
        $new = ArrayHelper::get()->transform(UserGroup::findAllByPk($this->groupIDs), 'name');
        $this->logAction(UserHistory::ACTION_GROUPS, "Old: " . implode(", ", $old) . "\nNew: " . implode(", ", $new));
        return $this;
    }

    /**
     * Before deleting user delete every other tables connected to it.
     * @return bool|void
     */
    public function beforeDelete(){
        App::get()->sql()->table('users2groups')->where("user_id = :id")->setParam(':id', $this->id)->delete();
        UserHistory::deleteAllByAttributes(['user_id' => $this->id]);
        UserConfig::deleteAllByAttributes(['user_id' => $this->id]);
        return parent::beforeDelete();
    }

    /**
     * Sends a request to UserHistory::addEntry()
     * @param $action
     * @param null $comment
     * @return int
     */
    public function logAction($action, $comment = null){
        return UserHistory::addEntry($this->id, $action, $comment);
    }

    /**
     * Used for profile details page to show if user is connected to Facebook and to add options to Disconnect/Connect
     * @param bool $admin
     * @return string
     */
    public function getFacebookConnectOrViewURL($admin = false){
        if ($this->fb_id){
            return Html::get()->tag('a', "Connected", ['class' => 'ext-login-button facebook-login-button']) .
                Html::get()->link('?removeFB=1', 'Disconnect', ['style' => 'float:right;color:orangered;', 'onclick' => 'return confirm("Are you sure?");']);
        } elseif ($admin) {
            return "Not Connected";
        } else {
            return Html::get()->link(WebApp::get()->user()->getFacebookLoginURL(true), "Link To Facebook", ['class' => 'ext-login-button facebook-login-button']);
        }
    }

    /**
     * Used for profile details page to show if user is connected to Google and to add options to Disconnect/Connect
     * @param bool $admin
     * @return string
     */
    public function getGoogleConnectOrViewURL($admin = false){
        if ($this->google_id){
            return Html::get()->tag('a', "Connected", ['class' => 'ext-login-button google-login-button']) .
            Html::get()->link('?removeGoogle=1', 'Disconnect', ['style' => 'float:right;color:orangered;', 'onclick' => 'return confirm("Are you sure?");']);
        } elseif ($admin) {
            return "Not Connected";
        } else {
            return Html::get()->link(WebApp::get()->user()->getGoogleClient(true)->createAuthUrl(), "Link To Google", ['class' => 'ext-login-button google-login-button']);
        }
    }

    /**
     * Returns list of user_groups.name to be used by ActiveUser (or any other class that needs it.
     * @return array
     */
    public function getGroupsList(){
        return ArrayHelper::get()->transform($this->groups, 'name');
    }
}
