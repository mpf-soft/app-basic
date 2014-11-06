<?php

if (file_exists($dbConfigFile = dirname(dirname(dirname(__DIR__))) . DIRECTORY_SEPARATOR . 'db.inc.php')) {
    include $dbConfigFile;
}


return array(
    'mpf\\interfaces\\LogAwareObjectInterface' => array(
        'loggers' => array('mpf\\loggers\\InlineWebLogger')
    ),
    'mpf\\datasources\\sql\\PDOConnection' => array(
        'dns' => 'mysql:dbname=demoapp;host=' . (defined('DB_HOST') ? DB_HOST : 'localhost'),
        'username' => defined('DB_USER') ? DB_USER : 'root',
        'password' => defined('DB_PASS') ? DB_PASS : ''
    ),
    'mpf\\interfaces\\TranslatableObjectInterface' => array(
        'translator' => '\\mpf\\translators\\ArrayFile'
    ),
    'mpf\\web\\AssetsPublisher' => array(
        'developmentMode' => true // change it to true when working on widgets or any other classes that publish assets that are changed during development
    ),
    'mpf\\base\\App' => array(
//        'cacheEngineClass' => '\\mpf\\datasources\\redis\\Cache'
    )
);
