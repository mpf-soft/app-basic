<?php
$actions = array(
    'index' => 'View All',
    'create' => 'New User'
);
$menu = array();
foreach ($actions as $action => $label) {
    $menu[] = array(
        'url' => array('users', $action),
        'label' => $label,
        'htmlOptions' => ($action == $this->getActiveAction()) ? array('class' => 'selected') : array()
    );
}
/* @var $model \app\models\User */
echo \app\components\htmltools\Page::title('Users - View', $menu);

echo \mpf\widgets\viewtable\Table::get(array(
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
            'value' => $model->getFacebookConnectOrViewURL(true)
        ),
        'google_id' => array(
            'value' => $model->getGoogleConnectOrViewURL(true)
        )
    )
))->display();