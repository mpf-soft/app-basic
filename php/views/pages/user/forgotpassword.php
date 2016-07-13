<?php require __DIR__ . '/_header.php'; ?>
<?php

echo \mpf\widgets\form\Form::get([
    'model' => $user,
    'name' => 'reset_password',
    'fields' => [
        'email'
    ]
])->display();