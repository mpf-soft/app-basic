<?php

if (file_exists($dbConfigFile = dirname(dirname(dirname(__DIR__))) . DIRECTORY_SEPARATOR . 'db.inc.php')) {
    include $dbConfigFile;
}


return [
    'mpf\\interfaces\\LogAwareObjectInterface' => [
        'loggers' => ['mpf\\loggers\\InlineWebLogger']
    ],
    'mpf\\datasources\\sql\\PDOConnection' => [
        'dns' => 'mysql:dbname=demoapp;host=' . (defined('DB_HOST') ? DB_HOST : 'localhost'),
        'username' => defined('DB_USER') ? DB_USER : 'root',
        'password' => defined('DB_PASS') ? DB_PASS : ''
    ],
    'mpf\\interfaces\\TranslatableObjectInterface' => [
        'translator' => '\\mpf\\translators\\ArrayFile'
    ],
    'mpf\\web\\AssetsPublisher' => [
        'developmentMode' => true // change it to true when working on widgets or any other classes that publish assets that are changed during development
    ],
    'mpf\\base\\App' => [
        'shortName' => 'app',
        'title' => 'BASIC - App Template'
//        'cacheEngineClass' => '\\mpf\\datasources\\redis\\Cache'
    ]
];
