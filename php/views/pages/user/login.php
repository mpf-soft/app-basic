<?php require __DIR__ . '/_header.php'; ?>

<?php

 echo \mWidgets\form\Form::get(array(
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
         'style' => 'width:  460px; margin-left:270px;'
     ),
     'links' => array(
         'Register' => \mpf\WebApp::get()->request()->createURL('users', 'register'),
         'Forgot Password' => \mpf\WebApp::get()->request()->createURL('users', 'forgotpassword')
     )
 ))->display();