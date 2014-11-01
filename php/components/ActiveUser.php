<?php

/**
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

use app\components\htmltools\Messages;
use app\models\User;
use app\models\UserConfig;
use Facebook\FacebookRequest;
use Facebook\FacebookSession;
use Facebook\GraphUser;
use mpf\web\Cookie;
use mpf\WebApp;

/**
 * Class ActiveUser
 * @package app\components
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $status
 */
class ActiveUser extends \mpf\web\ActiveUser {

    public $facebook = array(
        'app-id' => 'id',
        'app-secret' => 'secret'
    );


    /**
     * Cookie timeout in days
     * @var int
     */
    protected $cookieTimeout = 30;

    /**
     * List of sources used for auto login(not post). A method called check{Source} must be implemented for each
     * source that will return null or User object.
     * @var array
     */
    public $autoLoginSources = array('cookie', 'facebook', 'google', 'steam');

    public function login($user, $password, $source = 'post', $rememberMe = true) {
        $searchBy = (false !== strpos($user, '@')) ? 'email' : 'name';
        $user = User::findByAttributes(array(
            $searchBy => $user,
            'password' => User::hashPassword($password)
        ));
        if (!$user) {
            Messages::get()->error('Invalid username or password!');
            return false;
        }
        return $this->checkUserLogin($user, $source, $rememberMe);
    }

    /**
     * Checks login from cookie or any other  used by user;
     */
    protected function checkAutoLogin() {
        foreach ($this->autoLoginSources as $source) {
            $user = $this->{'check' . ucfirst($source)}();
            if (null !== $user) {
                return $this->checkUserLogin($user, $source, true);
            }
        }
    }

    protected function checkGoogle() {
        return null;
    }

    protected function checkFacebook() {
        return null;
        $session = FacebookSession::newAppSession($this->facebook['app-id'], $this->facebook['app-secret']);
        $session->validate();
        $me = (new FacebookRequest($session, 'GET', '/me'))->execute()->getGraphObject(GraphUser::className());
        /* @var $me GraphUser */
        if (!$me || !$me->getId()) {
            return false;
        }
        $user = User::findByAttributes(array('fb_id' => $me->getId()));

        if (!$user) {
            $user = new User();
            if ($old = $user->facebookRegister($session, $me)) {
                $user = $old;
            }
        }

        return $user;
    }

    protected function checkSteam() {
        return null;
    }

    /**
     * @return null|User
     */
    protected function checkCookie() {
        if (null === ($email = Cookie::get()->value($this->cookieKey))) {
            return null;
        }
        return User::findByAttributes(array(
            'email' => $email
        ));
    }

    /**
     * @param User $user
     * @param string $source
     * @param boolean $rememberMe
     * @return boolean
     */
    protected function checkUserLogin(User $user, $source, $rememberMe) {
        if ($user->status == User::STATUS_NEW) {
            Messages::get()->error('Email address was not yet confirmed! Check your emails and access received link to activate the account!');
            return false;
        }
        if ($user->status == User::STATUS_BLOCKED) {
            Messages::get()->error('This account has been banned! Please contact an admin if you think this is a mistake!');
            return false;
        }
        if ($user->status == User::STATUS_DELETED) {
            Messages::get()->error('This account has been recently deleted! If you want to recover it please contact an admin. An account is permanently removed ' . User::DELETE_ACCOUNT_AFTER_X_DAYS . ' days after it was deleted!');
            return false;
        }
        $this->connected = true;
        $this->setState('id', $user->id);
        $this->setState('name', $user->name);
        $this->setState('email', $user->email);
        $this->setState('status', $user->status);
        $user->last_login = date('Y-m-d H:i:s');
        $user->last_login_source = $source;
        $user->save();
        if ($rememberMe) {
            Cookie::get()->set($this->cookieKey, $user->email, $this->cookieTimeout);
        }
        if (!trim($user->name)) { // fill last details if they were not already saved
            WebApp::get()->request()->setController('user');
            WebApp::get()->request()->setAction('registerauto');
        }
        return true;
    }
}
