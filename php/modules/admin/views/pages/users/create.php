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

echo \app\components\htmltools\Page::title('Users - ' . $actions[$this->getActiveAction()], $menu);


echo \mpf\widgets\form\Form::get(array(
    'name' => 'save',
    'model' => $model,
    'theme' => 'default-wide',
    'fields' => array(
        'name',
        'email',
        array(
            'name' => 'comment',
            'type' => 'textarea'
        )
    )
))->display();