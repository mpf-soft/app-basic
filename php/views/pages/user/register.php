<?php require __DIR__ . '/_header.php'; ?>

<?php

echo \mpf\widgets\form\Form::get(array(
    'name' => 'register',
    'model' => $user,
    'fields' => array(
        'name',
        'email',
        array(
            'name' => 'newPassword',
            'type' => 'password'
        ),
        array(
            'name' => 'repeatedPassword',
            'type' => 'password'
        )
    ),
    'htmlOptions' => array(
        'style' => 'width:  460px; margin-left:270px;'
    ),
    'formHtmlOptions' => array(
        'autocomplete' => 'off'
    ),
    'links' => array(
        'Login' => \mpf\WebApp::get()->request()->createURL('users', 'login')
    )
))->display();