<?php
/* @var $this \app\controllers\Usergroups */
$actions = array(
    'index' => 'View All',
    'create' => 'Add Group'
);
$menu = array();
foreach ($actions as $action => $label) {
    $menu[] = array(
        'url' => array('usergroups', $action),
        'label' => $label,
        'htmlOptions' => ($action == $this->getActiveAction()) ? array('class' => 'selected') : array()
    );
}

echo \app\components\htmltools\Page::title('Users Groups - ' . $actions[$this->getActiveAction()], $menu);


echo \mpf\widgets\form\Form::get(array(
    'name' => 'save',
    'model' => $model,
    'theme' => 'default-wide',
    'fields' => array(
        'name',
        'label'
    ),
    'formHtmlOptions' => array(
        'autocomplete' => 'off'
    )
))->display();