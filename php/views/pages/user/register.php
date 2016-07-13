<?php require __DIR__ . '/_header.php'; ?>

<?php

echo \mpf\widgets\form\Form::get([
    'name' => 'register',
    'model' => $user,
    'fields' => [
        'name',
        'email',
        [
            'name' => 'newPassword',
            'type' => 'password'
        ],
        [
            'name' => 'repeatedPassword',
            'type' => 'password'
        ]
    ],
    'formHtmlOptions' => [
        'autocomplete' => 'off'
    ],
    'links' => [
        'Login' => \mpf\WebApp::get()->request()->createURL('user', 'login')
    ]
])->display();