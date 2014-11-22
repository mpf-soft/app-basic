<!DOCTYPE html>
<html>
<head>
    <title><?= \mpf\WebApp::get()->title ?></title>
    <?= \mpf\web\helpers\Html::get()->cssFile(\mpf\WebApp::get()->request()->getWebRoot() . 'main/style.css'); ?>
    <?= \mpf\web\helpers\Html::get()->mpfScriptFile('jquery.js'); ?>
    <?= \mpf\web\helpers\Html::get()->scriptFile(\mpf\WebApp::get()->request()->getWebRoot() . 'main/main.js'); ?>
</head>
<body>
<div id="wrapper">
    <div id="site">
        <div id="header">
            <h1><?= \mpf\web\helpers\Html::get()->link(\mpf\WebApp::get()->request()->getLinkRoot(), \mpf\WebApp::get()->title); ?></h1>
            <?php
            \mpf\widgets\menu\Menu::get([
                'items' => [
                    [
                        'url' => [],
                        'label' => 'Home'
                    ],
                    [
                        'url' => ['user', 'login'],
                        'label' => 'Login',
                        'visible' => \mpf\WebApp::get()->user()->isGuest()
                    ],
                    [
                        'url' => ['user', 'register'],
                        'label' => 'Register',
                        'visible' => \mpf\WebApp::get()->user()->isGuest()
                    ],
                    [
                        'url' => ['user', 'forgotpassword'],
                        'label' => 'Forgot Password',
                        'visible' => \mpf\WebApp::get()->user()->isGuest()
                    ],
                    [
                        'class' => 'Label',
                        'label' => \mpf\WebApp::get()->user()->isGuest() ? 'Welcome Guest!' : 'Welcome ' . \mpf\WebApp::get()->user()->name,
                        'htmlOptions' => ['style' => 'float:right;'],
                        'items' => [
                            [
                                'url' => ['user', 'profile'],
                                'label' => 'My Profile'
                            ],
                            [
                                'url' => ['user', 'edit'],
                                'label' => 'Edit My Profile'
                            ],
                            [
                                'url' => ['user', 'email'],
                                'label' => 'Change Email'
                            ],
                            [
                                'url' => ['user', 'password'],
                                'label' => 'Change Password'
                            ],
                            [
                                'url' => ['home', 'index', 'admin'],
                                'label' => 'Administration'
                            ],
                            [
                                'url' => ['user', 'logout'],
                                'label' => 'Logout'
                            ]
                        ]
                    ],
                    /*                    [
                                            'label' => 'Windows Login',
                                            'url' => 'http://test.test',
                                            'htmlOptions' => ['style' => 'float:right;'],
                                            'linkHtmlOptions' => ['class' => 'ext-login-button windows-login-button']
                                        ],
                                        [
                                            'label' => 'Twitter Login',
                                            'url' => 'http://test.test',
                                            'htmlOptions' => ['style' => 'float:right;'],
                                            'linkHtmlOptions' => ['class' => 'ext-login-button twitter-login-button']
                                        ],
                                        [
                                            'label' => 'Yahoo Login',
                                            'url' => 'http://test.test',
                                            'htmlOptions' => ['style' => 'float:right;'],
                                            'linkHtmlOptions' => ['class' => 'ext-login-button yahoo-login-button']
                                        ),
                                        [
                                            'label' => 'GitHub Login',
                                            'url' => 'http://test.test',
                                            'htmlOptions' => ['style' => 'float:right;'],
                                            'linkHtmlOptions' => ['class' => 'ext-login-button github-login-button']
                                        ],*/
                    [
                        'label' => 'Google Login',
                        'url' => ($url = \mpf\WebApp::get()->user()->getGoogleClient() ? \mpf\WebApp::get()->user()->getGoogleClient()->createAuthUrl() : null),
                        'htmlOptions' => ['style' => 'float:right;'],
                        'linkHtmlOptions' => ['class' => 'ext-login-button google-login-button'],
                        'visible' => \mpf\WebApp::get()->user()->isGuest() && trim($url)
                    ],
                    [
                        'label' => 'Facebook Login',
                        'url' => $url = \mpf\WebApp::get()->user()->getFacebookLoginURL(),
                        'visible' => \mpf\WebApp::get()->user()->isGuest() && trim($url),
                        'htmlOptions' => ['style' => 'float:right;'],
                        'linkHtmlOptions' => ['class' => 'ext-login-button facebook-login-button']
                    ]
                ]
            ])->display();
            ?>
        </div>
        <div id="content">
            <?= \app\components\htmltools\Messages::get()->display(); ?>

