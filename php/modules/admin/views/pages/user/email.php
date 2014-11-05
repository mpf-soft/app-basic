<?php require __DIR__ . '/_header.php'; ?>

<?= \mpf\widgets\form\Form::get(array(
    'name' => 'save',
    'model' => $model,
    'theme' => 'default-wide',
    'fields' => array(
        [
            'name' => 'oldPassword',
            'type' => 'password'
        ],
        'newEmail'
    ),
    'formHtmlOptions' => array(
        'autocomplete' => 'off'
    )
))->display();?>