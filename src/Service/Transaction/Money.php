<?php

namespace Api\Service\Transaction;

/**
 * Class Price
 *
 * @package Api\Service\Transaction
 */
class Money
{
    /**
     * @param float $price
     *
     * @return int
     */
    public function getAmount($price)
    {
        if (!empty($price) && !is_numeric($price)) {
            throw new \InvalidArgumentException('Provided price is not numeric');
        }

        return (int) ($price * 100);
    }

    /**
     * @param int $amount
     *
     * @return string
     */
    public function getPrice($amount)
    {
        return sprintf('%0.2f', $amount / 100);
    }
}
