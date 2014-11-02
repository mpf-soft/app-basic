<?php

echo \app\components\htmltools\Page::title('Config');

\mpf\widgets\datatable\Table::get(array(
    'dataProvider' => $model->getDataProvider(),
    'multiSelect' => true,
    'multiSelectActions' => array(
        'editconfig' => array(
            'label' => 'Edit',
            'icon' => \mpf\web\AssetsPublisher::get()->mpfAssetFile('images/oxygen/16x16/actions/document-edit.png'),
            'url' => \mpf\WebApp::get()->request()->createURL("admin", "editconfig")
        )
    ),
    'columns' => array(
        'name',
        'value',
        'lastupdate_date' => array('class' => 'Date'),
        'lastupdate_user' => array(
            'value' => '$row->lastupdate_user?$row->admin->name:"<i>-none-</i>"'
        ),
        array(
            'class' => 'Actions',
            'buttons' => array(
                'edit' => array(
                    'class' => 'Edit',
                    'url' => "\\mpf\\WebApp::get()->request()->createURL(\\mpf\\WebApp::get()->request()->getController(), 'editconfig', array('id' => \$row->id))"
                )
            ),
            'headerHtmlOptions' => array(
                'style' => 'width:20px;'
            )
        )
    )
))->display();