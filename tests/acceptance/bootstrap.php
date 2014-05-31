<?php
require_once '/var/www/lib/auto_prepend_file.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

$originalConfig = __DIR__ . '/../../config/config.php';
$sourceDb       = __DIR__ . '/../../data/appdb.sqlite';

$isHhvm = defined('HHVM_VERSION');

if ($isHhvm) {
    $testConfig = __DIR__ . '/config.php';
    copy($originalConfig, $originalConfig . '.back');
    copy($testConfig, $originalConfig);

    $dbFile = __DIR__ . '/../../config/tmp.appdb.sqlite';

    $command = null;
} else {
    $command = sprintf(
        'php -S %s:%d -t %s -c %s >/dev/null 2>&1 & echo $!',
        WEB_SERVER_HOST,
        WEB_SERVER_PORT,
        WEB_SERVER_DOCROOT,
        PHP_INI_FILE
    );

    $dbFile = __DIR__ . '/tmp.appdb.sqlite';
}

$success = copy($sourceDb, $dbFile);
if (!$success) {
    throw new \RuntimeException('Was not able to copy db file.');
}

$pid = null;
if ($command) {
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
}

// Kill the web server when the process ends
register_shutdown_function(
    function() use ($pid, $dbFile, $originalConfig) {
        if ($pid) {
            echo sprintf('%s - Killing process with ID %d', date('r'), $pid) . PHP_EOL;
            exec('kill ' . $pid);
        }

        unlink($dbFile);

        $backupConfig = $originalConfig . '.back';
        if (file_exists($backupConfig)) {
            copy($backupConfig, $originalConfig);
            unlink($backupConfig);
        }
    }
);
