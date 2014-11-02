<?php require __DIR__ . '/_header.php'; ?>

<?= \mpf\widgets\form\Form::get(array(
    'name' => 'save',
    'model' => $model,
    'theme' => 'default-wide',
    'fields' => [
        [
            'name' => 'oldPassword',
            'type' => 'password'
        ],
        [
            'name' => 'newPassword',
            'type' => 'password'
        ],
        [
            'name' => 'repeatedPassword',
            'type' => 'password'
        ]
    ],
    'formHtmlOptions' => array(
        'autocomplete' => 'off'
    )
))->display(); ?>