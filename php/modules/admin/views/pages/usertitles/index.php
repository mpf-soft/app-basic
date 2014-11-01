<?php
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


\mWidgets\datatable\Table::get(array(
    'dataProvider' => $model->getDataProvider(),
    'multiSelect' => true,
    'multiSelectActions' => array(
        'delete' => array(
            'label' => 'Delete',
            'icon' => \mpf\web\AssetsPublisher::get()->mpfAssetFile('images/oxygen/16x16/actions/edit-delete.png'),
            'shortcut' => 'Shift+Delete',
            'url' => \mpf\WebApp::get()->request()->createURL("usertitles", "delete")
        )
    ),
    'columns' => array(
        'id' => array(
            'headerHtmlOptions' => array('style' => 'width:60px;'),
            'htmlOptions' => array('style' => 'text-align:center;')
        ),
        'auto' => array(
            'filter' => array('No', 'Yes')
        ),
        'title',
        'description',
        array(
            'class' => 'Actions',
            'headerHtmlOptions' => array('style' => 'width:40px;'),
            'buttons' => array(
                'edit' => array('class' => 'Edit'),
                'delete' => array('class' => 'Delete')
            ),
            'topButtons' => array(
                'add' => array('class' => 'Add')
            )
        )
    )
))->display();