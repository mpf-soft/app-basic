<?php

$main = include(__DIR__ . DIRECTORY_SEPARATOR . 'config.inc.php');

$loggers = array('mpf\\loggers\\InlineCliLogger');

if (isset($main['mpf\\interfaces\\LogAwareObjectInterface'])) {
    $main['mpf\\interfaces\\LogAwareObjectInterface']['loggers'] = $loggers;
} else {
    $main['mpf\\interfaces\\LogAwareObjectInterface'] = array('loggers' => $loggers);
}
return $main;
