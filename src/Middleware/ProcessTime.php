<?php

namespace Api\Middleware;

use Slim\Middleware;
use Api\Service\Time;
use Api\Service\AccessorTrait;

/**
 * Class ProcessTime
 *
 * @package Api\Middleware
 *
 * @method ProcessTime setTime(Time $time)
 * @method Time        getTime()
 */
class ProcessTime extends Middleware
{
    use AccessorTrait;

    /** @var float */
    private $startTime;

    /** @var float */
    private $stopTime;

    /**
     * Set process time
     */
    public function call()
    {
        $this->setStartTime();

        $this->getNextMiddleware()->call();

        $this->setStopTime();

        /** @var \Api\Slim $app */
        $app = $this->getApplication();
        /** @var \Api\Entities\User $user */
        $user = $app->config('user');
        $timezone = $user ? $user->getTimezone() : date_default_timezone_get();

        $time = $this->getTime()->setTimezone(new \DateTimeZone($timezone));

        $timeDifference = array(
            'timestamp' => $time->getDateTime()->getTimestamp(),
            'process'   => $time->getMicroTimeDifference($this->getStartTime(), $this->getStopTime())
        );

        $appData = $app->getData();
        $appData = is_array($appData) ? $appData : array();

        $data = array_merge($appData, $timeDifference);
        $app->setData($data);
    }

    /**
     * @return $this
     */
    public function setStartTime()
    {
        $this->startTime = microtime(true);

        return $this;
    }

    /**
     * @return float
     */
    private function getStartTime()
    {
        return $this->startTime;
    }

    /**
     * @return $this
     */
    private function setStopTime()
    {
        $this->stopTime = microtime(true);

        return $this;
    }

    /**
     * @return float
     */
    private function getStopTime()
    {
        return $this->stopTime;
    }
}
