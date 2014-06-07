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
    private $configTest;

    /** @var string */
    private $appDb;

    /** @var string */
    private $tmpDb;

    /**
     * @param array $params
     */
    public function __construct(array $params)
    {
        $this->configOriginal = $params['config_original'];
        $this->configTest     = $params['config_test'];
        $this->appDb          = $params['app_db'];
        $this->tmpDb          = realpath(__DIR__ . '/../../config/') . '/tmp.appdb.sqlite';

        define('TEST_CONFIG', serialize($this->getConfigData()));
    }

    /**
     * @return array
     */
    private function getConfigData()
    {
        return include $this->configTest;
    }

    public function init()
    {
        $this->message('<<< BOOTSTRAP >>>');

        $this->setUp();

        register_shutdown_function(
            array($this, 'tearDown'),
            array(
                'configData' => $this->getConfigData(),
                'tmpDbFile'  => $this->tmpDb,
                'config'     => $this->configOriginal
            )
        );

        $this->message('<<< BOOTSTRAP >>>');
    }

    /**
     * @return $this
     * @throws \RuntimeException
     */
    private function setUp()
    {
        $originalConfig = realpath($this->configOriginal);

        $localConfig = substr($originalConfig, 0, -4) . '.local.php';
        $this->moveFile($localConfig, $localConfig . '.back');
        $this->copyFile($this->configTest, $localConfig);

        $success = $this->copyFile($this->appDb, $this->tmpDb);
        if (!$success) {
            throw new \RuntimeException('Was not able to copy db file.');
        }

        $configData = $this->getConfigData();
        $errorLog = $configData['log']['file'];

        $this->deleteFile($errorLog);

        return $this;
    }

    /**
     * @param array $params
     */
    public function tearDown(array $params)
    {
        $this->message('<<< TEAR DOWN >>>');

        $configData     = $params['configData'];
        $dbFile         = $params['tmpDbFile'];
        $originalConfig = $params['config'];

        $this->deleteFile($dbFile);

        $localConfig = substr($originalConfig, 0, -4) . '.local.php';
        $this->moveFile($localConfig . '.back', $localConfig);

        $this->deleteFile($configData['log']['file']);

        $this->message('<<< TEAR DOWN >>>');
    }

    /**
     * @param string $file
     */
    private function deleteFile($file)
    {
        if (file_exists($file)) {
            $file    = realpath($file);
            $success = unlink($file);
            $this->message('rm ' . $file . ' > ' . (int)$success);
        } else {
            $this->message($file . ' missing for rm');
        }
    }

    /**
     * @param string $file
     * @param string $destination
     *
     * @return bool
     */
    private function moveFile($file, $destination)
    {
        if (file_exists($file)) {
            $file    = realpath($file);
            $success = rename($file, $destination);
            $this->message('mv ' . $file . ' ' . $destination . ' > ' . (int)$success);
        } else {
            $this->message($file . ' missing for cp');
            $success = false;
        }

        return $success;
    }

    /**
     * @param string $file
     * @param string $destination
     *
     * @return bool
     */
    private function copyFile($file, $destination)
    {
        if (file_exists($file)) {
            $file    = realpath($file);
            $success = copy($file, $destination);
            $this->message('cp ' . $file . ' ' . $destination . ' > ' . (int)$success);
        } else {
            $this->message($file . ' missing for cp');
            $success = false;
        }

        return $success;
    }

    /**
     * @param string $message
     */
    private function message($message)
    {
        echo $message . PHP_EOL;
    }
}