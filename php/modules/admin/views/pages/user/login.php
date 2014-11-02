<?php require __DIR__ . '/_header.php'; ?>

<?php

 echo \mpf\widgets\form\Form::get(array(
     'name' => 'login',
     'model' => $model,
     'fields' => array(
         array(
             'name' => 'name'
         ),
         array(
             'name' => 'password',
             'type' => 'password'
         )
     ),
     'htmlOptions' => array(
         'style' => 'width: 460px;float:right; margin-right:20px;'
     ),
     'links' => array(
         'Forgot Password' => \mpf\WebApp::get()->request()->createURL('users', 'forgotpassword')
     )
 ))->display();