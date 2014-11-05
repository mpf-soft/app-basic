<?php require __DIR__ . '/_header.php'; ?>
<?php /* @var $model app\models\User */ ?>
<?= \mpf\widgets\viewtable\Table::get(array(
    'model' => $model,
    'labels' => $model::getLabels(),
    'columns' => array(
        'name',
        'email',
        'register_date',
        'last_login',
        'status' => array(
            'value' => $model->getStringStatus()
        ),
        'fb_id' => array(
            'value' => $model->getFacebookConnectOrViewURL()
        ),
        'google_id' => array(
            'value' => $model->getGoogleConnectOrViewURL()
        )
    )
))->display(); ?>