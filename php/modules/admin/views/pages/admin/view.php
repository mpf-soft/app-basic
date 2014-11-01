<?php
$actions = array(
    'crontab' => 'View Jobs',
    'addcron' => 'Add Job'
);
$menu = array();
foreach ($actions as $action => $label) {
    $menu[] = array(
        'url' => array('admin', $action),
        'label' => $label,
        'htmlOptions' => ($action == \mpf\WebApp::get()->request()->getAction()) ? array('class' => 'selected') : array()
    );
}

echo \app\components\htmltools\Page::title('Crontab - View Log', $menu);

foreach ($models as $model){
    /* @var $model \app\models\Crontab */
    echo \mpf\web\helpers\Html::get()->tag('div', "<a href='?download={$model->id}'>{$model->log}</a>".
     $model->previewLog(2500), array(
            'class' => 'log-details'
        ));
}