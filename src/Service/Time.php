<?php

namespace Api\Service;

/**
 * Class Time
 *
 * @package Api\Service
 */
class Time
{
    /** @var \DateTimeZone */
    private $timezone;

    /**
     * @param \DateTimeZone $timezone
     *
     * @return $this
     */
    public function setTimezone(\DateTimeZone $timezone)
    {
        $this->timezone = $timezone;

        return $this;
    }

    /**
     * @return \DateTimeZone
     */
    public function getTimezone()
    {
        return $this->timezone;
    }

    /**
     * @param string             $time
     * @param \DateTimeZone|null $timezone
     *
     * @return \DateTime
     */
    public function getDateTime($time = 'now', \DateTimeZone $timezone = null)
    {
        $timezone = $timezone ? $timezone : $this->getTimezone();

        $dateTime = new \DateTime($time);
        $dateTime->setTimezone($timezone);

        return $dateTime;
    }

    /**
     * @param float $startMicroTime
     * @param float $stopMicroTime
     *
     * @throw \InvalidArgumentException
     * @return float
     */
    public function getMicroTimeDifference($startMicroTime, $stopMicroTime)
    {
        if ($stopMicroTime < $startMicroTime) {
            throw new \InvalidArgumentException('Stop micro time is less than start micro time.');
        }

        return $stopMicroTime - $startMicroTime;
    }

}