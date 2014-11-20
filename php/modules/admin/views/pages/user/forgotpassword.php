<?php require __DIR__ . '/_header.php'; ?>
<?php

echo \mpf\widgets\form\Form::get([
    'model' => $user,
    'name' => 'reset_password',
    'fields' => [
        'email'
    ],
    'htmlOptions' => [
        'style' => 'width:  460px; margin-left:270px;'
    ]
])->display();