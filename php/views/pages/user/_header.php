<?php use app\components\htmltools\Page; ?>
<?php
$actions = array(
    'profile' => 'View Profile',
    'edit' => 'Edit Profile',
    'email' => 'Change Email',
    'password' => 'Change Password',
    'login' => 'Login',
    'register' => 'Create a new account',
    'forgotpassword' => 'Forgot Password',
    'registerauto' => 'Profile details - Last step'
);
$menu = array();
foreach ($actions as $action => $label) {
    $menu[] = array(
        'url' => array('user', $action),
        'label' => $label,
        'visible' => ('registerauto' == $action) ? false : (in_array($action, array('login', 'register', 'forgotpassword')) ? \mpf\WebApp::get()->user()->isGuest() : \mpf\WebApp::get()->user()->isConnected()),
        'htmlOptions' => ($action == \mpf\WebApp::get()->request()->getAction()) ? array('class' => 'selected') : array()
    );
}
?>

<?= Page::title('User - ' . $actions[\mpf\WebApp::get()->request()->getAction()], $menu); ?>