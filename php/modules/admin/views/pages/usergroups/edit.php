<?php
$actions = array(
    'index' => 'View All',
    'create' => 'Add Group',
);
$menu = array();
foreach ($actions as $action => $label) {
    $menu[] = array(
        'url' => array('usergroups', $action),
        'label' => $label,
        'htmlOptions' => ($action == $this->getActiveAction()) ? array('class' => 'selected') : array()
    );
}

echo \app\components\htmltools\Page::title('Users Groups - Edit', $menu);

echo \mWidgets\form\Form::get(array(
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