<?php require __DIR__ . '/_header.php'; ?>

<?php

echo \mpf\widgets\form\Form::get([
    'name' => 'save_data_and_login',
    'model' => $model,
    'fields' => [
        'name',
        [
            'name' => 'newPassword',
            'type' => 'password'
        ],
        [
            'name' => 'repeatedPassword',
            'type' => 'password'
        ]
    ]
])->display();