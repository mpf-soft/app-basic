<?php require __DIR__ . '/_header.php'; ?>

<?php

$buttons = [];

if (\app\models\User::$allowConfirmationEmailResend){
    $buttons[] = [
        'name' => 'resend',
        'label' => 'Re-send confirmation email'
    ];
}

 echo \mpf\widgets\form\Form::get([
     'name' => 'login',
     'model' => $model,
     'fields' => [
         'name',
         [
             'name' => 'password',
             'type' => 'password'
         ]
     ],
     'buttons' => $buttons,
     'links' => [
         'Register' => \mpf\WebApp::get()->request()->createURL('user', 'register'),
         'Forgot Password' => \mpf\WebApp::get()->request()->createURL('user', 'forgotpassword')
     ]
 ])->display();