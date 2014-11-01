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

define('LIBS_FOLDER', dirname(__DIR__) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR);
define('APP_ROOT', dirname(__DIR__) . DIRECTORY_SEPARATOR . 'php' . DIRECTORY_SEPARATOR);
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
    $ex = new ErrorException($errstr, 0, $errno, $errfile, $errline);
    if (($ex->getSeverity() & $severity) != 0) {
        if (!class_exists('\\mpf\\WebApp')){
            echo $errfile . ':' . $errno . ':' . $errstr;
            return;
        }
        if (\mpf\WebApp::get()) {
            \mpf\WebApp::get()->error($errstr, array(
                'File' => $errfile,
                'Line' => $errline,
                'Number' => $errno,
                'Trace' => $ex->getTraceAsString()
            ));
        } else {
            echo $errfile . ':' . $errno . ':' . $errstr;
        }
    }
});

require_once LIBS_FOLDER . 'autoload.php';


use mpf\WebApp as App;
use mpf\base\Config as Config;
use mpf\web\AccessMap as AccessMap;

new Config(APP_ROOT . 'config' . DIRECTORY_SEPARATOR . 'web.inc.php');

//Config::get()->set('\\mpf\\web\\request\\HTML', array(
//    'defaultModule' => 'moduleName'
//));

App::run(array(
    'accessMap' => new AccessMap(array('map' => include(APP_ROOT . 'config' . DIRECTORY_SEPARATOR . 'accessmap.php'))),
    'startTime' => microtime(true)
));
