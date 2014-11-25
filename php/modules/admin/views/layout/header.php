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
                            'url' => ['user', 'forgotpassword'],
                            'label' => 'Forgot Password',
                            'visible' => \mpf\WebApp::get()->user()->isGuest()
                        ],
                        [
                            'class' => 'Label',
                            'label' => 'Admin',
                            'visible' => \mpf\WebApp::get()->user()->isConnected(),
                            'items' => [
                                [
                                    'url' => ['users', 'index'],
                                    'label' => 'Users',
                                    'visible' => \mpf\WebApp::get()->user()->isConnected(),
                                    'items' => [
                                        [
                                            'url' => ['users', 'index'],
                                            'label' => 'Manage Users'
                                        ],
                                        [
                                            'url' => ['users', 'create'],
                                            'label' => 'New User'
                                        ],
                                        [
                                            'url' => ['usergroups', 'index'],
                                            'label' => 'Manage Groups'
                                        ],
                                        [
                                            'url' => ['usergroups', 'create'],
                                            'label' => 'New Group'
                                        ],
                                        [
                                            'url' => ['usertitles', 'index'],
                                            'label' => 'Manage Titles'
                                        ],
                                        [
                                            'url' => ['usertitles', 'create'],
                                            'label' => 'New Title'
                                        ]
                                    ],
                                    'icon' => \mpf\web\AssetsPublisher::get()->mpfAssetFile('images/oxygen/22x22/apps/system-users.png')
                                ],
                                [
                                    'url' => ['admin', 'config'],
                                    'label' => 'Config',
                                    'icon' => \mpf\web\AssetsPublisher::get()->mpfAssetFile('images/oxygen/22x22/categories/preferences-other.png')
                                ],
                                [
                                    'url' => ['admin', 'crontab'],
                                    'label' => 'Crontab',
                                    'icon' => \mpf\web\AssetsPublisher::get()->mpfAssetFile('images/oxygen/22x22/categories/applications-system.png')
                                ]
                            ]
                        ],
                        [
                            'class' => 'Label',
                            'label' => \mpf\WebApp::get()->user()->isGuest() ? 'Welcome Guest!' : 'Welcome ' . \mpf\WebApp::get()->user()->name,
                            'htmlOptions' => ['style' => 'float:right;'],
                            'items' => [
                                [
                                    'url' => ['user', 'profile'],
                                    'label' => 'My Profile',
                                    'icon' => \mpf\web\AssetsPublisher::get()->mpfAssetFile('images/oxygen/22x22/apps/preferences-desktop-user.png')
                                ],
                                [
                                    'url' => ['user', 'edit'],
                                    'label' => 'Edit My Profile',
                                    'icon' => \mpf\web\AssetsPublisher::get()->mpfAssetFile('images/oxygen/22x22/apps/accessories-text-editor.png')
                                ],
                                [
                                    'url' => ['user', 'email'],
                                    'label' => 'Change Email',
                                    'icon' => \mpf\web\AssetsPublisher::get()->mpfAssetFile('images/oxygen/22x22/status/mail-unread.png')
                                ],
                                [
                                    'url' => ['user', 'password'],
                                    'label' => 'Change Password',
                                    'icon' => \mpf\web\AssetsPublisher::get()->mpfAssetFile('images/oxygen/22x22/actions/system-lock-screen.png')
                                ],
                                [
                                    'url' => ['user', 'logout'],
                                    'label' => 'Logout',
                                    'icon' => \mpf\web\AssetsPublisher::get()->mpfAssetFile('images/oxygen/22x22/status/task-reject.png')
                                ]
                            ]
                        ],

       /*                 [
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
                        ],
                        [
                            'label' => 'GitHub Login',
                            'url' => 'http://test.test',
                            'htmlOptions' => ['style' => 'float:right;'],
                            'linkHtmlOptions' => ['class' => 'ext-login-button github-login-button']
                        ],*/
                        [
                            'label' => 'Google Login',
                            'url' => ($url = \mpf\WebApp::get()->user()->getGoogleClient()?\mpf\WebApp::get()->user()->getGoogleClient()->createAuthUrl():null),
                            'htmlOptions' => ['style' => 'float:right;'],
                            'linkHtmlOptions' => ['class' => 'ext-login-button google-login-button'],
                            'visible' => \mpf\WebApp::get()->user()->isGuest() && trim($url)
                        ],
                        [
                            'label' => 'Facebook Login',
                            'url' => ($url = \mpf\WebApp::get()->user()->getFacebookLoginURL()),
                            'visible' => \mpf\WebApp::get()->user()->isGuest() && trim($url),
                            'htmlOptions' => ['style' => 'float:right;'],
                            'linkHtmlOptions' => ['class' => 'ext-login-button facebook-login-button']
                        ]
                    ]
                ])->display();
                ?>
            </div>
        </div>
        <div id="content">
            <?= \app\components\htmltools\Messages::get()->display(); ?>

