<?php

$main = include(__DIR__ . DIRECTORY_SEPARATOR . 'config.inc.php');

$main['mpf\\interfaces\\LogAwareObjectInterface'] = isset($main['mpf\\interfaces\\LogAwareObjectInterface']) ? $main['mpf\\interfaces\\LogAwareObjectInterface'] : array();
$main['mpf\\interfaces\\LogAwareObjectInterface']['loggers'] = array('mpf\\loggers\\DevLogger');

$main['mpf\\WebApp'] = array(
    'title' => 'Tests'
);

$main['mpf\\web\\Cookie'] = array(
    'salt' => '122#!@#3424#@$#25543'
);
$main['mpf\\web\\request\\HTML'] = array(
    'SEO' => true,
    'urlRoutes' => array(
        '(?<language>[a-z]{2})\/(?<controller>[a-zA-Z0-9]+)\/(?<action>[a-zA-Z0-9_\-]+)\/(?<id>[0-9]+)', // language & controller & action  & id
        '(?<controller>[a-zA-Z0-9]+)\/(?<action>[a-zA-Z0-9_\-]+)\/(?<id>[0-9]+)', // controller & action & id
        '(?<language>[a-z]{2})\/(?<controller>[a-zA-Z0-9]+)\/(?<action>[a-zA-Z0-9_\-]+)', // language & controller & action
        '(?<language>[a-z]{2})\/(?<controller>[a-zA-Z0-9]+)\/(?<id>[0-9]+)' => array('action' => 'view'), // language & controller & view id
        '(?<controller>[a-zA-Z0-9]+)\/(?<id>[0-9]+)' => array('action' => 'view'), // controller & view id
        '(?<language>[a-z]{2})\/(?<controller>[a-zA-Z0-9]+)', // language & controller
        '(?<controller>[a-zA-Z0-9]+)\/(?<action>[a-zA-Z0-9_\-]+)', // controller & action
        '(?<controller>[a-zA-Z0-9]+)' // controller
    ),
    'modules' => array(
        'admin' => array(
            'mpf\\WebApp' => array(
                'title' => $main['mpf\\WebApp']['title'] . ' - Admin'
            )
        )
    ),
    'csrfSalt' => 'D$F#$dx32x43'
);

return $main;
