<?php

namespace AcceptanceTests;

/**
 * Class Bootstrap
 *
 * @package AcceptanceTests
 */
class Bootstrap
{
    /** @var string */
    private $configOriginal;

    /** @var string */
    private $appDb;

    /** @var string */
    private $tmpDb;

    /** @var int[] */
    private $pid = array();

    /** @var int */
    private $port;

    /** @var int */
    private $hhvmPort;

    /** @var string */
    private $host;

    /** @var string */
    private $root;

    /** @var string */
    private $ini;

    /**
     * @param array $params
     */
    public function __construct(array $params)
    {
        $this->configOriginal = $params['config_original'];
        $this->appDb          = $params['app_db'];
        $this->port           = $params['port'];
        $this->host           = $params['host'];
        $this->root           = $params['root'];
        $this->ini            = $params['ini'];
        $this->hhvmPort       = $params['hhvm_port'];
        $this->tmpDb          = $this->getTmpDbFile();
    }

    /**
     * @return bool
     */
    private function isHhVm()
    {
        return defined('HHVM_VERSION');
    }

    /**
     * @return string
     */
    private function getTmpDbFile()
    {
        if ($this->isHhVm()) {
            return __DIR__ . '/../../config/tmp.appdb.sqlite';
        }

        return __DIR__ . '/tmp.appdb.sqlite';
    }

    public function init()
    {
        $this->setUp()->start();

        register_shutdown_function(
            array($this, 'tearDown'),
            array(
                'pids'      => $this->pid,
                'tmpDbFile' => $this->tmpDb,
                'config'    => $this->configOriginal,
                'hhvm'      => $this->isHhVm()
            )
        );
    }

    /**
     * @return $this
     * @throws \RuntimeException
     */
    private function setUp()
    {
        $originalConfig = $this->configOriginal;

        if ($this->isHhVm()) {
            $testConfig = __DIR__ . '/config.php';
            copy($originalConfig, $originalConfig . '.back');
            copy($testConfig, $originalConfig);
        }

        $success = copy($this->appDb, $this->tmpDb);
        if (!$success) {
            throw new \RuntimeException('Was not able to copy db file.');
        }

        return $this;
    }

    public function start()
    {
        $commands = $this->getCommands();
        if (empty($commands)) {
            return;
        }

        foreach ($commands as $command) {
            $output = array();
            exec($command, $output);
            $pid = (int)$output[0];

            $msg = '%s ### PID %d';
            echo sprintf($msg, $command, $pid) . PHP_EOL;

            $this->pid[] = $pid;
        }
    }

    /**
     * @return array
     */
    private function getCommands()
    {
        $commands = array();

        if ($this->isHhVm()) {
            $commands[] = sprintf(
                'hhvm --mode server -vServer.Type=fastcgi -vServer.Port=%s' . ' >/dev/null 2>&1 & echo $!',
                $this->hhvmPort
            );
            $commands[] = 'sudo service nginx start';
        } else {
            $commands[] = sprintf(
                'php -S %s:%d -t %s -c %s' . ' >/dev/null 2>&1 & echo $!',
                $this->host,
                $this->port,
                $this->root,
                $this->ini
            );
        }

        return $commands;
    }

    /**
     * @param array $params
     */
    public function tearDown(array $params)
    {
        $dbFile         = $params['tmpDbFile'];
        $originalConfig = $params['config'];
        $pids           = $params['pids'];
        $hhvm           = $params['hhvm'];

        foreach ($pids as $pid) {
            if ($pid) {
                echo sprintf('Killing PID %d', $pid) . PHP_EOL;
                exec('kill ' . $pid);
            }
        }

        if ($hhvm) {
            exec('sudo service nginx stop');
            unlink('www.pid');
        }

        if (file_exists($dbFile)) {
            unlink($dbFile);
        }

        $backupConfig = $originalConfig . '.back';
        if (file_exists($backupConfig)) {
            copy($backupConfig, $originalConfig);
            unlink($backupConfig);
        }
    }
}