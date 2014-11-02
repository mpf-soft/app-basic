<?php require __DIR__ . '/_header.php'; ?>
<?php

echo \mpf\widgets\form\Form::get(array(
    'name' => 'reset_password',
    'fields' => array(
        'email'
    ),
    'htmlOptions' => array(
        'style' => 'width:  460px; margin-left:270px;'
    )
))->display();