<?php
$actions = array(
    'crontab' => 'View All',
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

echo \app\components\htmltools\Page::title('Crontab - Edit Job', $menu);


echo \mWidgets\form\Form::get(array(
    'name' => 'save',
    'model' => $model,
    'theme' => 'default-wide',
    'fields' => array(
        'user',
        'interval',
        'command',
        'log',
        array(
            'name' => 'enabled',
            'type' => 'select',
            'options' => array('No', 'Yes')
        )
    ),
    'formHtmlOptions' => array(
        'autocomplete' => 'off'
    )
))->display();