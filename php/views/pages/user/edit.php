<?php require __DIR__ . '/_header.php'; ?>

<?= \mpf\widgets\form\Form::get(array(
    'name' => 'save',
    'model' => $model,
    'theme' => 'default-wide',
    'fields' => array(
        'name'
    ),
    'formHtmlOptions' => array(
        'autocomplete' => 'off'
    )
))->display();?>