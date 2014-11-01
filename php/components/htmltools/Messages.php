<?php
/**
 * Created by PhpStorm.
 * User: mirel
 * Date: 9/4/14
 * Time: 3:27 PM
 */

namespace app\components\htmltools;


use mpf\base\TranslatableObject;
use mpf\web\helpers\Html;

class Messages extends TranslatableObject {

    public $htmlClass = 'user-messages user-message-{type}';

    protected $sessionKey = 'Html_Messages';

    protected $messages = array();

    /**
     * @var Messages
     */
    private static $_instance;

    /**
     * @return Messages
     */
    public static function get() {
        if (!static::$_instance) {
            static::$_instance = new static();
        }
        return static::$_instance;
    }

    /**
     * Load messages from session
     * @param array $config
     */
    protected function init($config = array()) {
        $this->messages = isset($_SESSION[$this->sessionKey]) ? $_SESSION[$this->sessionKey] : array();
        return parent::init($config = array());
    }

    /**
     * Get HTML code to display current messages.
     * @return string
     */
    public function display() {
        $htmlMessages = array();
        foreach ($this->messages as $message) {
            $htmlMessages[] = Html::get()->tag('div', Html::get()->tag('span', $this->translate($message['message'])), array(
                'class' => str_replace('{type}', $message['type'], $this->htmlClass)
            ));
        }
        // clear messages
        $this->messages = array();
        $_SESSION[$this->sessionKey] = array();
        return implode("\n", $htmlMessages);
    }

    /**
     * Saves a message to be displayed next time display method is called as long as the session doesn't change.
     * @param string $message
     * @param string $type
     * @return $this
     */
    public function register($message, $type) {
        $this->messages[] = array(
            'message' => $message,
            'type' => $type
        );
        if ($this->sessionKey) {
            $_SESSION[$this->sessionKey] = $this->messages;
        }
        return $this;
    }

    /**
     * Saves a info message
     * @param string $message
     * @return $this
     */
    public function info($message) {
        return $this->register($message, 'info');
    }

    /**
     * Saves a success message
     * @param string $message
     * @return $this
     */
    public function success($message) {
        return $this->register($message, 'success');
    }

    /**
     * Saves an error message
     * @param string $message
     * @return $this
     */
    public function error($message) {
        return $this->register($message, 'error');
    }

    /**
     * Saves a warning message
     * @param string $message
     * @return $this
     */
    public function warning($message) {
        return $this->register($message, 'warning');
    }
} 