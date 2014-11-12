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


\mpf\widgets\datatable\Table::get(array(
    'dataProvider' => $model->getDataProvider(),
    'multiSelect' => true,
    'multiSelectActions' => array(
        'delete' => [
            'label' => 'Delete',
            'icon' => \mpf\web\AssetsPublisher::get()->mpfAssetFile('images/oxygen/16x16/actions/edit-delete.png'),
            'shortcut' => 'Shift+Delete',
            'url' => \mpf\WebApp::get()->request()->createURL("users", "delete"),
            'confirmation' => 'Are you sure?'
        ],
        'enable' => [
            'label' => 'Enable',
            'icon' => \mpf\web\AssetsPublisher::get()->mpfAssetFile('images/oxygen/16x16/actions/dialog-ok-apply.png'),
            'url' => \mpf\WebApp::get()->request()->createURL("users", "index")
        ],
        'disable' => [
            'label' => 'Disable',
            'icon' => \mpf\web\AssetsPublisher::get()->mpfAssetFile('images/oxygen/16x16/actions/dialog-cancel.png'),
            'url' => \mpf\WebApp::get()->request()->createURL("users", "index")
        ],
        'join' => [
            'label' => 'Join Accounts',
            'icon'=> \mpf\web\AssetsPublisher::get()->mpfAssetFile('images/oxygen/16x16/actions/im-msn.png'),
            'url' => \mpf\WebApp::get()->request()->createURL("users", "merge"),
            'confirmation' => 'Are you sure? After this user can log in on any of those accounts and see data from all of them.[where this is supported]'
        ]
    ),
    'columns' => array(
        'name',
        'email',
        'register_date' => array('class' => 'Date'),
        'last_login' => array('class' => 'Date'),
        'last_login_source' => array(
            'filter' => array('post' => 'POST', 'cookie' => 'Cookie', 'facebook' => 'Facebook', 'google' => 'Google')
        ),
        'status' => array(
            'class' => 'Select',
            'filter' => \app\models\User::getStatuses()
        ),
        array(
            'class' => 'Actions',
            'buttons' => array(
                'delete' => array('class' => 'Delete'),
                'edit' => array('class' => 'Edit'),
                'view' => array('class' => 'View')
            ),
            'headerHtmlOptions' => array(
                'style' => 'width:60px;'
            ),
            'topButtons' => array(
                'add' => array('class' => 'Add')
            )
        )
    )
))->display();