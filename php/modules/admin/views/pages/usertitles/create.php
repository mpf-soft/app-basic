<?php
/* @var $this \app\controllers\Usertitles */
$actions = array(
    'index' => 'View All',
    'create' => 'Add Title'
);
$menu = array();
foreach ($actions as $action => $label) {
    $menu[] = array(
        'url' => array('usertitles', $action),
        'label' => $label,
        'htmlOptions' => ($action == $this->getActiveAction()) ? array('class' => 'selected') : array()
    );
}

echo \app\components\htmltools\Page::title('Users Titles - ' . $actions[$this->getActiveAction()], $menu);


echo \mWidgets\form\Form::get(array(
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