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
            \mpf\widgets\menu\Menu::get(array(
                'items' => array(
                    array(
                        'url' => array(),
                        'label' => 'Home'
                    ),
                    array(
                        'url' => array('user', 'login'),
                        'label' => 'Login',
                        'visible' => \mpf\WebApp::get()->user()->isGuest()
                    ),
                    array(
                        'url' => array('user', 'register'),
                        'label' => 'Register',
                        'visible' => \mpf\WebApp::get()->user()->isGuest()
                    ),
                    array(
                        'url' => array('user', 'forgotpassword'),
                        'label' => 'Forgot Password',
                        'visible' => \mpf\WebApp::get()->user()->isGuest()
                    ),
                    array(
                        'class' => 'Label',
                        'label' => \mpf\WebApp::get()->user()->isGuest() ? 'Welcome Guest!' : 'Welcome ' . \mpf\WebApp::get()->user()->name,
                        'htmlOptions' => array('style' => 'float:right;'),
                        'items' => array(
                            array(
                                'url' => array('user', 'profile'),
                                'label' => 'My Profile'
                            ),
                            array(
                                'url' => array('user', 'edit'),
                                'label' => 'Edit My Profile'
                            ),
                            array(
                                'url' => array('user', 'email'),
                                'label' => 'Change Email'
                            ),
                            array(
                                'url' => array('user', 'password'),
                                'label' => 'Change Password'
                            ),
                            array(
                                'url' => array('user', 'logout'),
                                'label' => 'Logout'
                            )
                        )
                    ),
                    array(
                        'label' => 'FacebookLogin',
                        'url' => $url = \mpf\WebApp::get()->user()->getFacebookLoginURL(),
                        'visible' => \mpf\WebApp::get()->user()->isGuest() && trim($url),
                        'htmlOptions' => ['style' => 'float:right;', 'class' => 'ext-login-button facebook-login-button']
                    )
                )
            ))->display();
            ?>
        </div>
        <div id="content">
            <?= \app\components\htmltools\Messages::get()->display(); ?>

