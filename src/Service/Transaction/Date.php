<?php

namespace Api\Service\Transaction;

use Api\Entities\User;

/**
 * Class Date
 *
 * @package Api\Service\Transaction
 */
class Date
{
    /**
     * @param User   $user
     * @param string $date
     *
     * @return \DateTime
     */
    public function getDateTime(User $user, $date)
    {
        $time  = strtotime($date);
        if ($time === false) {
            throw new \InvalidArgumentException('Provided date cannot be found');
        }

        $year  = date('Y', $time);
        $month = date('m', $time);
        $day   = date('d', $time);

        $dateTime = new \DateTime();
        $dateTime->setTimezone(new \DateTimeZone($user->getTimezone()));
        $dateTime->setDate($year, $month, $day);

        return $dateTime;
    }

}