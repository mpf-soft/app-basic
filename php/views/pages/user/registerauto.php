<?php require __DIR__ . '/_header.php'; ?>

<?php

echo \mpf\widgets\form\Form::get(array(
    'name' => 'save_data_and_login',
    'model' => $user,
    'fields' => array(
        'name',
        array(
            'name' => 'password',
            'type' => 'password'
        ),
        array(
            'name' => 'repeatedPassword',
            'type' => 'password'
        )
    ),
    'htmlOptions' => array(
        'style' => 'width:  460px; margin-left:270px;'
    )
))->display();