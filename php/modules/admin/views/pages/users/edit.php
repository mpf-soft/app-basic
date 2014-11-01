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

echo \app\components\htmltools\Page::title('Users - Edit', $menu);
echo \mWidgets\form\Form::get(array(
    'name' => 'save',
    'model' => $model,
    'theme' => 'default-wide',
    'fields' => array(
        'name',
        'email',
        array(
            'name' => 'title_id',
            'type' => 'select',
            'options' => \mpf\helpers\ArrayHelper::get()->transform(\app\models\UserTitle::findAll(), array('id' => 'title'))
        ),
        array(
            'name' => 'status',
            'type' => 'radio',
            'options' => array(\app\models\User::STATUS_ACTIVE => 'Active', \app\models\User::STATUS_BLOCKED => 'Blocked')
        ),
        array(
            'name' => 'groupIDs',
            'type' => 'checkbox',
            'options' => \mpf\helpers\ArrayHelper::get()->transform(\app\models\UserGroup::findAll(), array('id' => 'label'))
        )
    )
))->display();