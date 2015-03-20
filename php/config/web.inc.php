<?php

$main = include(__DIR__ . DIRECTORY_SEPARATOR . 'config.inc.php');

$main['mpf\\interfaces\\LogAwareObjectInterface'] = isset($main['mpf\\interfaces\\LogAwareObjectInterface']) ? $main['mpf\\interfaces\\LogAwareObjectInterface'] : array();
$main['mpf\\interfaces\\LogAwareObjectInterface']['loggers'] = ['mpf\\loggers\\DevLogger'];

$main['mpf\\web\\Cookie'] = [
    'salt' => '122#!@#3424#@$#25543'
];
$main['mpf\\web\\request\\HTML'] = [
    'SEO' => true,
    'urlRoutes' => [
        // 'guild\/(?<section>[0-9]+)\/forum\/(?<controller>[a-zA-Z0-9]+)\/(?<action>[a-zA-Z0-9_\-]+)' => ['module' => 'forum'],
        // 'guild\/(?<section>[0-9]+)\/forum\/(?<controller>[a-zA-Z0-9]+)' => ['module' => 'forum'],
        '(?<language>[a-z]{2})\/(?<controller>[a-zA-Z0-9]+)\/(?<action>[a-zA-Z0-9_\-]+)\/(?<id>[0-9]+)', // language & controller & action  & id
        '(?<controller>[a-zA-Z0-9]+)\/(?<action>[a-zA-Z0-9_\-]+)\/(?<id>[0-9]+)', // controller & action & id
        '(?<language>[a-z]{2})\/(?<controller>[a-zA-Z0-9]+)\/(?<action>[a-zA-Z0-9_\-]+)', // language & controller & action
        '(?<language>[a-z]{2})\/(?<controller>[a-zA-Z0-9]+)\/(?<id>[0-9]+)' => ['action' => 'view'], // language & controller & view id
        '(?<controller>[a-zA-Z0-9]+)\/(?<id>[0-9]+)' => ['action' => 'view'], // controller & view id
        '(?<language>[a-z]{2})\/(?<controller>[a-zA-Z0-9]+)', // language & controller
        '(?<controller>[a-zA-Z0-9]+)\/(?<action>[a-zA-Z0-9_\-]+)', // controller & action
        '(?<controller>[a-zA-Z0-9]+)' // controller
    ],
    'modules' => [
        'admin' => [
            'mpf\\WebApp' => [
                'title' => $main['mpf\\base\\App']['title'] . ' - Admin'
            ]
        ]
    ],
    'csrfSalt' => 'D$F#$dx32x43'
];

return $main;
