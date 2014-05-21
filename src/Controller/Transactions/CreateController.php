<?php

namespace Api\Controller\Transactions;

use Api\Entities\Transaction;
use Api\Entities\User;
use Api\Service\Transaction\Save;
use Api\Service\Transaction\Money;
use Api\Service\Transaction\Date;
use Api\Service\AccessorTrait;

/**
 * Class CreateController
 *
 * @package Api\Controller\Transactions
 *
 * @method CreateController setDate(Date $date)
 * @method CreateController setSave(Save $save)
 * @method CreateController setMoney(Money $money)
 * @method Date             getDate()
 * @method Save             getSave()
 * @method Money            getMoney()
 */
class CreateController
{
    use AccessorTrait;

    /**
     * @param User   $user
     * @param string $item
     * @param string $group
     * @param string $price
     * @param string $date
     *
     * @return array
     */
    public function getResponse(
        User $user,
        $item,
        $group,
        $price,
        $currency,
        $date
    ) {
        $response = array('success' => true);

        try {
            $transaction = $this->getSave()->save(
                new Transaction,
                $user,
                $item,
                $group,
                $this->getMoney()->getAmount($price),
                $currency,
                $this->getDate()->getDateTime($user, $date)
            );

            $response['data'] = array(
                'id' => $transaction->getId()
            );

        } catch (\Exception $exc) {
            $response['success'] = false;
            $response['message'] = $exc->getMessage();
        }

        return $response;
    }

}