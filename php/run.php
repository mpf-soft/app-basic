#!/usr/bin/env php

<?php
if (!isset($_SERVER['argv'])){
    die("<br /><span style='color:orangered;font-size: 25;font-family: Arial;'>Can run this from browser!</span>");
}

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

define('LIBS_FOLDER', dirname(__DIR__) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR);
define('APP_ROOT', __DIR__ . DIRECTORY_SEPARATOR);

/**
 * Set ErrorException for every error;
 */
set_error_handler(function ($errno, $errstr, $errfile, $errline) {
    $severity = 1 * E_ERROR | // change 0 / 1 value to ignore / handle different errors;
        1 * E_WARNING |
        1 * E_PARSE |
        1 * E_NOTICE |
        0 * E_CORE_ERROR |
        0 * E_CORE_WARNING |
        0 * E_COMPILE_ERROR |
        0 * E_COMPILE_WARNING |
        1 * E_USER_ERROR |
        1 * E_USER_WARNING |
        1 * E_USER_NOTICE |
        0 * E_STRICT |
        0 * E_RECOVERABLE_ERROR |
        0 * E_DEPRECATED |
        0 * E_USER_DEPRECATED;
    $ex = new \ErrorException($errstr, 0, $errno, $errfile, $errline);
    if (($ex->getSeverity() & $severity) != 0) {
        \mpf\ConsoleApp::get()->error($errstr, array(
            'File' => $errfile,
            'Line' => $errline,
            'Number' => $errno,
            'Trace' => $ex->getTraceAsString()
        ));
    }
});

$autoload = require_once LIBS_FOLDER . 'autoload.php';


use mpf\ConsoleApp as App;
use mpf\base\Config as Config;
new Config(APP_ROOT  . 'config' . DIRECTORY_SEPARATOR . 'console.inc.php');

App::run(array(
    'startTime' => microtime(true),
    'autoload' => $autoload
));
