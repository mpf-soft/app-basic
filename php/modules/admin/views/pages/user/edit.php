<?php require __DIR__ . '/_header.php'; ?>

<?= \mpf\widgets\form\Form::get([
    'name' => 'save',
    'model' => $model,
    'theme' => 'default-wide',
    'fields' => [
        'name',
        [
            'name' => 'icon',
            'type' => 'image',
            'urlPrefix' => \app\models\User::AVATAR_LOCATION_URL
        ]
    ],
    'formHtmlOptions' => [
        'autocomplete' => 'off',
        'enctype' => 'multipart/form-data'
    ]
])->display(); ?>