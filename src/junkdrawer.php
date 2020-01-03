<?php
$junkdrawers = array(
    'junkdrawer' => array(
        'path' => 'd:\_junkdrawer',
        'days' => 21
    )
);

$appPath = realpath(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..');
define('LOG', $appPath . DIRECTORY_SEPARATOR . 'log' . DIRECTORY_SEPARATOR . 'error.log');
date_default_timezone_set('Europe/Berlin');

set_exception_handler('exceptionHandler');
set_error_handler('errorHandler');

function logger($msg) {
    error_log(date('Y.m.d - h:i:s - ') . $msg . "\n", 3, LOG); 
    echo $msg . "\n"; 
}

function exceptionHandler($exception) {
    logger("Uncaught exception: " . $exception->getMessage());
}

function errorHandler($errno, $errstr, $errfile, $errline)
{
    switch ($errno) {

    case E_WARNING:
        logger("WARNING: [$errno] $errstr");
        break;

    case E_NOTICE:
        logger("NOTICE: [$errno] $errstr");
        break;

    default:
        logger("Unknown error type: [$errno] $errstr");
        break;
    }

    return true;
}

require_once 'Junkdrawer.class.php';


foreach ($junkdrawers as $name => $options) {
    if (!$options['path']) throw new Exception("path for junkdrawer [$name] not found");
    if (!$options['days']) throw new Exception("days for junkdrawer [$name] not found");
    
    $junkdrawer = new Junkdrawer($name, $options['path'], $options['days']);
    $junkdrawer->clean();
    
}