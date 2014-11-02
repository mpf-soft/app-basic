<?php
$actions = array(
    'index' => 'View All',
    'create' => 'Add Group',
);
$menu = array();
foreach ($actions as $action => $label) {
    $menu[] = array(
        'url' => array('usertitles', $action),
        'label' => $label,
        'htmlOptions' => ($action == $this->getActiveAction()) ? array('class' => 'selected') : array()
    );
}

echo \app\components\htmltools\Page::title('Users Titles - Edit', $menu);

echo \mpf\widgets\form\Form::get(array(
    'name' => 'save',
    'model' => $model,
    'theme' => 'default-wide',
    'fields' => array(
        'title',
        'description',
        'auto'
    ),
    'formHtmlOptions' => array(
        'autocomplete' => 'off'
    )
))->display();