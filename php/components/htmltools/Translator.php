<?php
/**
 * Created by PhpStorm.
 * User: Mirel Mitache
 * Date: 16.09.2014
 * Time: 22:37
 */

namespace app\components\htmltools;

use mpf\base\TranslatableObject;

class Translator extends TranslatableObject {

    /**
     * @var static
     */
    private static $instance;

    /**
     * @param array $config
     * @return static
     */
    public static function get($config = array()) {
        if (!static::$instance) {
            static::$instance = new static($config);
        }
        return static::$instance;
    }

    /**
     * Advanced translation with variables.
     * @param string $text
     * @param array $variables
     * @return string
     */
    public function t($text, $variables = array()) {
        $text = $this->translate($text);
        foreach ($variables as $k => $v) {
            $text = str_replace("{$k}", $v, $text);
        }
        return $text;
    }
} 