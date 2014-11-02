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

echo \app\components\htmltools\Page::title('Crontab - ' . $actions[\mpf\WebApp::get()->request()->getAction()], $menu);

\mpf\widgets\datatable\Table::get(array(
    'dataProvider' => $model->getDataProvider(),
    'multiSelect' => true,
    'multiSelectActions' => array(
        'delete' => array(
            'label' => 'Delete',
            'icon' => \mpf\web\AssetsPublisher::get()->mpfAssetFile('images/oxygen/16x16/actions/edit-delete.png'),
            'shortcut' => 'Shift+Delete',
            'url' => \mpf\WebApp::get()->request()->createURL("admin", "delete")
        ),
        'view' => array(
            'label' => 'View',
            'icon' => \mpf\web\AssetsPublisher::get()->mpfAssetFile('images/oxygen/16x16/actions/document-preview.png'),
            'shortcut' => 'Ctrl+V',
            'url' => \mpf\WebApp::get()->request()->createURL("admin", "view")
        ),
        'enable' => array(
            'label' => 'Enable',
            'icon' => \mpf\web\AssetsPublisher::get()->mpfAssetFile('images/oxygen/16x16/actions/dialog-ok-apply.png'),
            'url' => \mpf\WebApp::get()->request()->createURL("admin", "crontab")
        ),
        'disable' => array(
            'label' => 'Disable',
            'icon' => \mpf\web\AssetsPublisher::get()->mpfAssetFile('images/oxygen/16x16/actions/dialog-cancel.png'),
            'url' => \mpf\WebApp::get()->request()->createURL("admin", "crontab")
        )
    ),
    'columns' => array(
        'user' => array('headerHtmlOptions' => array('style' => 'width:75px;')),
        'interval',
        'command',
        'log',
        array(
            'class' => 'Actions',
            'buttons' => array(
                'preview' => array(
                    'icon' => \mpf\web\AssetsPublisher::get()->mpfAssetFile('images/oxygen/16x16/places/document-multiple.png'),
                    'title' => '"Last logs preview"',
                    'jsAction' => 'crontabLogPreview',
                    'url' => "\\mpf\\WebApp::get()->request()->createURL(\\mpf\\WebApp::get()->request()->getController(), 'cronlogpreview', array('id' => \$row->id))"
                ),
            ),
            'headerHtmlOptions' => array(
                'style' => 'width:20px;'
            )
        ),
        'enabled' => array(
            'filter' => array('No', 'Yes'),
            'value' => '$row->enabled?"<span style=\'color:limegreen;\'>Yes</span>":"<span style=\'color:orangered;\'>No</span>";'
        ),
        'laststart' => array('class' => 'Date'),
        array(
            'class' => 'Actions',
            'buttons' => array(
                'delete' => array('class' => 'Delete'),
                'edit' => array(
                    'class' => 'Edit',
                    'url' => "\\mpf\\WebApp::get()->request()->createURL(\\mpf\\WebApp::get()->request()->getController(), 'editcron', array('id' => \$row->id))"
                ),
                'view' => array('class' => 'View'),
                'run' => array(
                    'title' => '"Run job"',
                    'url' => "\\mpf\\WebApp::get()->request()->createURL(\\mpf\\WebApp::get()->request()->getController(), 'execcron', array('id' => \$row->id))",
                    'icon' => \mpf\web\AssetsPublisher::get()->mpfAssetFile('images/oxygen/16x16/actions/system-run.png')
                )
            ),
            'headerHtmlOptions' => array(
                'style' => 'width:80px;'
            ),
            'topButtons' => array(
                'add' => array(
                    'class' => 'Add',
                    'url' => \mpf\WebApp::get()->request()->createURL('admin', 'addcron')
                )
            )
        )
    )
))->display();
?>
<div style="display: none;" id="log-dialog" title="Log Preview"><i>loading</i></div>