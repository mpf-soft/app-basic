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

use app\models\User;
use mpf\base\Mailer;
use \mpf\base\TranslatableObject;
use mpf\WebApp;

/**
 * Contains a list of templates for emails sent for different actions. New methods can be added at any time for more
 * templates or the entire class can be extended.
 * All templates can be translated.
 *
 * @author Mirel Mitache
 */
class Emails extends TranslatableObject {

    /**
     * Website title to be displayed in emails.
     * @var string
     */
    public $website = 'My First App';

    /**
     * A link root can be specified in case that this method is used from console app or the active link root should not
     * be used.
     * @var string
     */
    public $linkRoot;

    /**
     *
     * @var static
     */
    private static $_instance;

    public static function get() {
        if (!static::$_instance) {
            static::$_instance = new static();
        }
        return static::$_instance;
    }

    protected function init($config = array()) {
        return parent::init($config = array());
    }

    protected function getLinkRoot() {
        return $this->linkRoot ? $this->linkRoot : WebApp::get()->request()->getLinkRoot();
    }

    /**
     * Send an email to selected address and name.
     * @param string $subject
     * @param string $message
     * @param string $email
     * @param string $name
     * @return boolean
     */
    protected function sendEmail($subject, $message, $email, $name) {
        return Mailer::get()->send(array('email' => $email, 'name' => $name), $subject, $message);
    }

    public function sendPasswordForgot(User $user, $code){
        $url = WebApp::get()->request()->createURL("user", "resetpassword", array('code' => $code));
        $message = <<<MESSAGE
Hello {user->name},

You requested a password reset for your account on <a href="{this->getLinkRoot()}">{this->website}</a>.
In order to fulfil your request please confirm your actions by accessing the following URL:
<a href='{url}'>{url}</a>

If you didn't requested this action then please ignore this message.

Regards,
{this->website}
MESSAGE;
        $message = nl2br(eval("return <<<MESSAGE\n" . str_replace(array('{user', '{this', '{password', '{url'), array('{$user', '{$this', '{$password', '{$url'), $this->translate($message)) . "\nMESSAGE;\n"));
        return $this->sendEmail('Reset Password For ' . $this->website, $message, $user->email, $user->name);
    }

    public function sendGeneratedPassword(User $user, $password){
        $message = <<<MESSAGE
Hello {user->name},

As you requested here is the new password and active email for your account:
Email: {user->email}
Password: {password}

Regards,
{this->website}
MESSAGE;
        $message = nl2br(eval("return <<<MESSAGE\n" . str_replace(array('{user', '{this', '{password'), array('{$user', '{$this', '{$password'), $this->translate($message)) . "\nMESSAGE;\n"));
        return $this->sendEmail('New Password For ' . $this->website, $message, $user->email, $user->name);
    }

    public function sendPasswordToNewAccount(User $user, $password){
        $adminName= WebApp::get()->user()->name;
        $message = <<<MESSAGE
Hello {user->name},

An account has been created for you by {adminName} on <a href="{this->getLinkRoot()}">{this->website}</a>.

To login use the following data:
Email: {user->email}
Password: {password}

Regards,
{this->website}
MESSAGE;
        $message = nl2br(eval("return <<<MESSAGE\n" . str_replace(array('{user', '{this', '{adminName', '{password'), array('{$user', '{$this', '{$adminName', '{$password'), $this->translate($message)) . "\nMESSAGE;\n"));
        return $this->sendEmail('Password for new account on ' . $this->website, $message, $user->email, $user->name);
    }

    /**
     * Send an email for when a new account was created. Return true on success.
     * @param \app\models\User $user
     * @return boolean
     */
    public function sendToNewAccount(User $user) {
        $message = <<<MESSAGE
Hello {user->name},

Welcome to <a href="{this->getLinkRoot()}">{this->website}</a>! In order to activate your new account please click on the link below:
<a href="{user->getActivationLink()}">{user->getActivationLink()}</a>


In case you didn't create an account then please ignore this message and the account will be automatically deleted in a few days.

Regards,
{this->website}
MESSAGE;
        $message = nl2br(eval("return <<<MESSAGE\n" . str_replace(array('{user', '{this'), array('{$user', '{$this'), $this->translate($message)) . "\nMESSAGE;\n"));
        return $this->sendEmail('Confirm account for ' . $this->website, $message, $user->email, $user->name);
    }

    /**
     * Send an email for when email address was changed.
     * @param \app\models\User $user
     * @return boolean
     */
    public function sentToEmailChange(User $user) {
        $message = <<<MESSAGE
Hello {user->name},

We sent this message to you because you tried to change current email from <a href="{this->getLinkRoot()}">{this->website}</a> to this address. In order to complete the email change process please click on the link below:
<a href="{user->getActivationLink()}">{user->getActivationLink()}</a>

                
In case you didn't create this request then you should login on the website and change your password as it is possible that someone else got it and logged in to your account.

Regards,
{this->website}
MESSAGE;
        $message = nl2br(eval("return <<<MESS\n" . str_replace(array('{user', '{this'), array('{$user', '{$this'), $this->translate($message)) . "\nMESS;\n"));
        return $this->sendEmail('Confirm new email for ' . $this->website, $message, $user->new_email, $user->name);
    }

}
