<?php use app\components\htmltools\Page; ?>
<?php
$actions = [
    'profile' => 'View Profile',
    'edit' => 'Edit Profile',
    'email' => 'Change Email',
    'password' => 'Change Password',
    'login' => 'Login',
    'register' => 'Create a new account',
    'forgotpassword' => 'Forgot Password',
    'registerauto' => 'Profile details - Last step'
];
$menu = [];
foreach ($actions as $action => $label) {
    $menu[] = [
        'url' => ['user', $action],
        'label' => $label,
        'visible' => ('registerauto' == $action) ? false : (in_array($action, ['login', 'register', 'forgotpassword']) ? \mpf\WebApp::get()->user()->isGuest() : \mpf\WebApp::get()->user()->isConnected()),
        'htmlOptions' => ($action == \mpf\WebApp::get()->request()->getAction()) ? ['class' => 'selected'] : []
    ];
}
?>

<?= Page::title('User', $menu); ?>