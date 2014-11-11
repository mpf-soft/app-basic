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
use app\models\GlobalConfig;
use app\models\User;
use app\models\UserConfig;
use Facebook\FacebookRedirectLoginHelper;
use Facebook\FacebookRequest;
use Facebook\FacebookSession;
use Facebook\GraphUser;
use Github\Client;
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
    public $autoLoginSources = ['cookie', 'facebook', 'google', 'gitHub', 'yahoo', 'twitter', 'windows'];

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
            if (!is_null($user = $this->{'check' . ucfirst($source)}())){
                return $this->checkUserLogin($user, $source, true);
            }
        }
    }

    /**
     * @var FacebookRedirectLoginHelper
     */
    protected $facebookLoginHelper;

    /**
     * @return FacebookRedirectLoginHelper|null
     */
    protected function getFacebookRedirectLoginHelper($force = false) {
        if ($this->isConnected() && !$force)
            return null;
        if (!$this->facebookLoginHelper) {
            if (!GlobalConfig::value('FACEBOOK_APPID') || !GlobalConfig::value('FACEBOOK_APPSECRET')) {
                return null;
            }
            FacebookSession::setDefaultApplication(GlobalConfig::value('FACEBOOK_APPID'), GlobalConfig::value('FACEBOOK_APPSECRET'));
            $this->facebookLoginHelper = new FacebookRedirectLoginHelper($force?WebApp::get()->request()->createURL('user', 'profile'):WebApp::get()->request()->getLinkRoot());
        }
        return $this->facebookLoginHelper;
    }

    /**
     * @return string|null
     */
    public function getFacebookLoginURL($force = false) {
        if (!is_null($helper = $this->getFacebookRedirectLoginHelper($force))) {
            return $helper->getLoginUrl(['email']);
        }
    }

    /**
     * @return User|null
     */
    public function checkFacebook($force = false) {
        if (is_null($helper = $this->getFacebookRedirectLoginHelper($force))) {
            return null;
        }
        $this->debug('got helper');
        try {
            if (is_null($session = $helper->getSessionFromRedirect())) {
                $this->debug('can\'t get session');
                return null;
            }
            /* @var $session FacebookSession */
            $session->validate();
            $this->debug('got session');
            $me = (new FacebookRequest($session, 'GET', '/me?fields=id,name,email'))->execute()->getGraphObject(GraphUser::className());
        } catch (\Exception $e) {
            $this->error($e->getMessage());
            return null;
        }
        /* @var $me GraphUser */
        if (!$me || !$me->getId()) {
            return null;
        }

        if ($force){
            $user =User::findByPk($this->id);
            $user->fb_id = $me->getId();
            UserConfig::set('FACEBOOK_NAME', $me->getFirstName() . ' ' . $me->getMiddleName() . ' ' . $me->getLastName());
            UserConfig::set('FACEBOOK_EMAIL', $me->getProperty('email'));
            UserConfig::set('FACEBOOK_PROFILE', $me->getLink());
            return $user->save(false);
        }

        $user = User::findByAttributes(array('fb_id' => $me->getId()));

        if (!$user) {
            $user = User::facebookRegister($me);
        }
        return $user;
    }

    /**
     * @var \Google_Client;
     */
    protected $googleClient;

    /**
     * @var \Google_Service_Oauth2
     */
    protected $googleOauth;

    /**
     * On google developers console you should add the following redirect uris: [replace www.website.com with your own website]
     * http://www.website.com/
     * http://www.website.com/user/profile
     * http://www.website.com/admin/
     * http://www.website.com/admin/user/profile
     * @param bool $force
     * @return null|\Google_Client
     */
    public function getGoogleClient($force = false){
        if ($this->isConnected() && !$force)
            return null;
        if (!$this->googleClient){
            if (!GlobalConfig::value('GOOGLE_CLIENTID') || !GlobalConfig::value('GOOGLE_CLIENTSECRET') || !GlobalConfig::value('GOOGLE_DEVELOPERKEY')){
                return null;
            }
            $this->googleClient = new \Google_Client();
            $this->googleClient->setClientId(GlobalConfig::value('GOOGLE_CLIENTID'));
            $this->googleClient->setClientSecret(GlobalConfig::value('GOOGLE_CLIENTSECRET'));
            $this->googleClient->setRedirectUri($force?WebApp::get()->request()->createURL('user', 'profile'):WebApp::get()->request()->getLinkRoot());
            $this->googleClient->setDeveloperKey(GlobalConfig::value('GOOGLE_DEVELOPERKEY'));
            $this->googleClient->setScopes(array(
                'https://www.googleapis.com/auth/plus.me',
                'https://www.googleapis.com/auth/userinfo.email',
                'https://www.googleapis.com/auth/userinfo.profile',
            ));
            $this->googleOauth = new \Google_Service_Oauth2($this->googleClient);
        }
        return $this->googleClient;
    }

    public function checkGoogle($force = false) {
        if (is_null($client = $this->getGoogleClient($force))){
            return null;
        }
        if (!isset($_GET['code']) || isset($_GET['state'])){
            return null;
        }
        $this->debug($_GET['code']);
        $client->authenticate($_GET['code']);
        $user = $this->googleOauth->userinfo->get();
        $details = [
            'id' => $user['id'],
            'name' => filter_var($user['name'], FILTER_SANITIZE_SPECIAL_CHARS),
            'email' => filter_var($user['email'], FILTER_SANITIZE_EMAIL),
            'profile_url' => filter_var($user['link'], FILTER_VALIDATE_URL),
            'image_url' => filter_var($user['picture'], FILTER_VALIDATE_URL)
        ];
        if ($force){
            $user =User::findByPk($this->id);
            $user->google_id = $details['id'];
            UserConfig::set('GOOGLE_NAME', $details['name']);
            UserConfig::set('GOOGLE_EMAIL', $details['email']);
            UserConfig::set('GOOGLE_PROFILE', $details['profile_url']);
            UserConfig::set('GOOGLE_IMAGE', $details['image_url']);
            return $user->save(false);
        }

        if (!is_null($user = User::findByAttributes(['google_id' => $details['id']]))){
            return $user;
        }

        return User::googleRegister($details);
    }

    protected function checkGitHub(){
        return null;
    }

    protected function checkYahoo(){
        return null;
    }

    protected function checkTwitter(){
        return null;
    }

    protected function checkWindows(){
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
            if (is_null($user->lastconfirmationmail_date) || $user->lastconfirmationmail_date < date('Y-m-d H:i:s', strtotime('-5 minutes'))){
                // if confirmation email was older than 5 minutes then allow it to resend it
                User::$allowConfirmationEmailResend = true;
                if (isset($_POST['resend'])){
                    $user->resendConfirmationEmail();
                }
            }
            if (!isset($_POST['resend'])) {
                Messages::get()->error('Email address was not yet confirmed! Check your emails and access received link to activate the account!');
            }
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
        $this->setRights($groups = $user->getGroupsList());
        $this->debug("Saved groups: " . implode(", " , $groups));
        $user->last_login = date('Y-m-d H:i:s');
        $user->last_login_source = $source;
        $user->save();
        if ($rememberMe) {
            Cookie::get()->set($this->cookieKey, $user->email, $this->cookieTimeout);
        }
        if (!trim($user->name)) { // fill last details if they were not already saved
            $this->debug('need auto register');
            WebApp::get()->request()->setController('user');
            WebApp::get()->request()->setAction('registerauto');
        }
        return true;
    }

    public function init($config = []) {
        parent::init($config);
        if ($this->isConnected()) {
            if (!trim($this->name)) {
                $this->debug('need auto register');
                WebApp::get()->request()->setController('user');
                WebApp::get()->request()->setAction('registerauto');
            }
        }
    }
}
