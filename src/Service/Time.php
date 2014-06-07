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

    /**
     * @param \DateTime $dateTimeLess
     * @param \DateTime $dateTimeMore
     *
     * @return bool
     */
    public function compareDateTime(\DateTime $dateTimeLess, \DateTime $dateTimeMore)
    {
        $timezone = $this->getTimezone();

        $dateTimeLess->setTimezone($timezone);
        $dateTimeMore->setTimezone($timezone);

        return $dateTimeMore >= $dateTimeLess;
    }
}