<?php
// ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start([
    'cookie_lifetime' => 86400,
]);

mb_internal_encoding("UTF-8");

require_once __DIR__ . "/../vendor/autoload.php";

spl_autoload_register(function ($className) {
    include __DIR__ . "/{$className}.class.php";
});

use Tracy\Debugger;
if (LocalConfig::DEBUG)
    Debugger::enable();