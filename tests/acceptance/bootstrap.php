<?php

if (defined('HHVM_VERSION')) {
    exit(0);
}

error_reporting(E_ALL);
ini_set('display_errors', 1);


$sourceDb = __DIR__ . '/../../data/appdb.sqlite';
$dbFile   = __DIR__ . '/tmp.appdb.sqlite';

$success = copy($sourceDb, $dbFile);
if (!$success) {
    throw new \RuntimeException('Was not able to copy db file.');
}

$command = sprintf(
    'php -S %s:%d -t %s -c %s >/dev/null 2>&1 & echo $!',
    WEB_SERVER_HOST,
    WEB_SERVER_PORT,
    WEB_SERVER_DOCROOT,
    PHP_INI_FILE
);

// Execute the command and store the process ID
$output = array();
exec($command, $output);
$pid = (int) $output[0];


echo sprintf(
    '%s - Web server started on %s:%d with PID %d',
    date('r'),
    WEB_SERVER_HOST,
    WEB_SERVER_PORT,
    $pid
) . PHP_EOL;

// Kill the web server when the process ends
register_shutdown_function(
    function() use ($pid, $dbFile) {
        echo sprintf('%s - Killing process with ID %d', date('r'), $pid) . PHP_EOL;
        exec('kill ' . $pid);
        unlink($dbFile);
    }
);

// More bootstrap code
require_once 'TestCase.php';
