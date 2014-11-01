<?php require __DIR__ . '/_header.php'; ?>
<?= \mWidgets\viewtable\Table::get(array(
    'model' => $model,
    'labels' => $model::getLabels(),
    'columns' => array(
        'name',
        'email',
        'register_date',
        'last_login',
        'status' => array(
            'value' => $model->getStringStatus()
        )
    )
))->display(); ?>