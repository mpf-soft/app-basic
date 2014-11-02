<!DOCTYPE html>
<html>
<head>
    <title><?= \mpf\WebApp::get()->title ?></title>
    <?= \mpf\web\helpers\Html::get()->cssFile(\mpf\WebApp::get()->request()->getWebRoot() . 'admin/style.css'); ?>
    <?= \mpf\web\helpers\Html::get()->mpfScriptFile('jquery.js'); ?>
    <?= \mpf\web\helpers\Html::get()->mpfCssFile('../scripts/jquery-ui/themes/' . \mpf\web\helpers\Html::get()->jqueryUITheme . '/jquery-ui.css'); ?>
    <?= \mpf\web\helpers\Html::get()->mpfScriptFile('jquery-ui/jquery-ui.js'); ?>
    <?= \mpf\web\helpers\Html::get()->scriptFile(\mpf\WebApp::get()->request()->getWebRoot() . 'admin/main.js'); ?>
</head>
<body>
<div id="wrapper">
    <div id="site">
        <div id="header">
            <div id="header-bar">
                <h1><?= \mpf\web\helpers\Html::get()->link(\mpf\WebApp::get()->request()->getLinkRoot(), \mpf\WebApp::get()->title); ?></h1>
            </div>
            <div id="menu-bar">
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
                            'url' => array('user', 'forgotpassword'),
                            'label' => 'Forgot Password',
                            'visible' => \mpf\WebApp::get()->user()->isGuest()
                        ),
                        array(
                            'class' => 'Label',
                            'label' => 'Admin',
                            'visible' => \mpf\WebApp::get()->user()->isConnected(),
                            'items' => array(
                                array(
                                    'url' => array('users', 'index'),
                                    'label' => 'Users',
                                    'visible' => \mpf\WebApp::get()->user()->isConnected(),
                                    'items' => array(
                                        array(
                                            'url' => array('users', 'index'),
                                            'label' => 'Manage Users'
                                        ),
                                        array(
                                            'url' => array('users', 'create'),
                                            'label' => 'New User'
                                        ),
                                        array(
                                            'url' => array('usergroups', 'index'),
                                            'label' => 'Manage Groups'
                                        ),
                                        array(
                                            'url' => array('usergroups', 'create'),
                                            'label' => 'New Group'
                                        ),
                                        array(
                                            'url' => array('usertitles', 'index'),
                                            'label' => 'Manage Titles'
                                        ),
                                        array(
                                            'url' => array('usertitles', 'create'),
                                            'label' => 'New Title'
                                        )
                                    ),
                                    'icon' => \mpf\web\AssetsPublisher::get()->mpfAssetFile('images/oxygen/22x22/apps/system-users.png')
                                ),
                                array(
                                    'url' => array('admin', 'config'),
                                    'label' => 'Config',
                                    'icon' => \mpf\web\AssetsPublisher::get()->mpfAssetFile('images/oxygen/22x22/categories/preferences-other.png')
                                ),
                                array(
                                    'url' => array('admin', 'crontab'),
                                    'label' => 'Crontab',
                                    'icon' => \mpf\web\AssetsPublisher::get()->mpfAssetFile('images/oxygen/22x22/categories/applications-system.png')
                                )
                            )
                        ),
                        array(
                            'class' => 'Label',
                            'label' => \mpf\WebApp::get()->user()->isGuest() ? 'Welcome Guest!' : 'Welcome ' . \mpf\WebApp::get()->user()->name,
                            'htmlOptions' => array('style' => 'float:right;'),
                            'items' => array(
                                array(
                                    'url' => array('user', 'profile'),
                                    'label' => 'My Profile',
                                    'icon' => \mpf\web\AssetsPublisher::get()->mpfAssetFile('images/oxygen/22x22/apps/preferences-desktop-user.png')
                                ),
                                array(
                                    'url' => array('user', 'edit'),
                                    'label' => 'Edit My Profile',
                                    'icon' => \mpf\web\AssetsPublisher::get()->mpfAssetFile('images/oxygen/22x22/apps/accessories-text-editor.png')
                                ),
                                array(
                                    'url' => array('user', 'email'),
                                    'label' => 'Change Email',
                                    'icon' => \mpf\web\AssetsPublisher::get()->mpfAssetFile('images/oxygen/22x22/status/mail-unread.png')
                                ),
                                array(
                                    'url' => array('user', 'password'),
                                    'label' => 'Change Password',
                                    'icon' => \mpf\web\AssetsPublisher::get()->mpfAssetFile('images/oxygen/22x22/actions/system-lock-screen.png')
                                ),
                                array(
                                    'url' => array('user', 'logout'),
                                    'label' => 'Logout',
                                    'icon' => \mpf\web\AssetsPublisher::get()->mpfAssetFile('images/oxygen/22x22/status/task-reject.png')
                                )
                            )
                        )
                    )
                ))->display();
                ?>
            </div>
        </div>
        <div id="content">
            <?= \app\components\htmltools\Messages::get()->display(); ?>

