<?php
require_once dirname(__FILE__) . '/../../videos/configuration.php';
if (!isset($global['systemRootPath'])) {
    $configFile = '../../videos/configuration.php';
    if (!file_exists($configFile)) {
        $configFile = '../videos/configuration.php';
    }
    require_once $configFile;
}
if (!isCommandLineInterface()) {
    die('Command Line only');
}
$url = $argv[1];

$name = "get_data_" . md5($url);
$lockFile = getTmpDir() . $name;

_error_log("Live:asyncGetStats: {$url} Lockfile={$lockFile}");
file_put_contents($lockFile, time());

if (!file_exists($lockFile)) {
    _error_log("Live:asyncGetStats: {$url} start");
    try {
        $result = url_get_contents($url);
        ObjectYPT::setCache($name, $result);
    } catch (Exception $exc) {
        _error_log($exc->getTraceAsString());
    }
} else {
    _error_log("Live:asyncGetStats: {$url} is already processing");
}
unlink($lockFile);