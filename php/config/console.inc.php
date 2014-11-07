<?php

$main = include(__DIR__ . DIRECTORY_SEPARATOR . 'config.inc.php');

$loggers = ['mpf\\loggers\\InlineCliLogger'];

if (isset($main['mpf\\interfaces\\LogAwareObjectInterface'])) {
    $main['mpf\\interfaces\\LogAwareObjectInterface']['loggers'] = $loggers;
} else {
    $main['mpf\\interfaces\\LogAwareObjectInterface'] = ['loggers' => $loggers];
}
return $main;
