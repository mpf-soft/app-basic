<?php require __DIR__ . '/_header.php'; ?>
<?php /* @var $model app\models\User */ ?>
<?= \mpf\widgets\viewtable\Table::get([
    'model' => $model,
    'labels' => $model::getLabels(),
    'columns' => [
        'name',
        'email',
        'register_date',
        'last_login',
        'status' => [
            'value' => $model->getStringStatus()
        ],
        'fb_id' => [
            'value' => $model->getFacebookConnectOrViewURL()
        ],
        'google_id' => [
            'value' => $model->getGoogleConnectOrViewURL()
        ]
    ]
])->display(); ?>