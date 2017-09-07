<?php

namespace Api\Controller\Transactions;

use Api\Entities\User;
use Api\Entities\Transaction;
use Api\Service\Transaction\Money;
use Api\Service\Transaction\Date;
use Api\Service\Transaction\Validate;
use Api\Service\Transaction\Save;
use Api\Service\Transaction\Data as TransactionData;
use Api\Service\AccessorTrait;

/**
 * Class CreateController
 *
 * @package Api\Controller\Update
 *
 * @method UpdateController setValidate(Validate $validate)
 * @method UpdateController setData(TransactionData $data)
 * @method UpdateController setSave(Save $save)
 * @method UpdateController setDate(Date $date)
 * @method UpdateController setMoney(Money $money)
 * @method Validate         getValidate()
 * @method TransactionData  getData()
 * @method Save             getSave()
 * @method Date             getDate()
 * @method Money            getMoney()
 */
class UpdateController
{
    use AccessorTrait;

    /**
     * @param User   $user
     * @param array  $connectedUserIds
     * @param int    $transactionId
     * @param string $itemValue
     * @param string $groupValue
     * @param string $price
     * @param string $date
     *
     * @return array
     */
    public function getResponse(
        User $user,
        array $connectedUserIds,
        $transactionId,
        $itemValue,
        $groupValue,
        $price,
        $currency,
        $date
    ) {
        $response = array('success' => false);

        $entity = $this->getData()->find($transactionId);
        if ($entity && $this->getValidate()->isAllowed($user, $connectedUserIds, $entity)) {
            try {
                $transaction = $this->update(
                    $entity,
                    $user,
                    $itemValue,
                    $groupValue,
                    $price,
                    $currency,
                    $date
                );

                $response['success'] = true;
                $response['data'] = array(
                    'id' => $transaction->getId(),
                );
            } catch (\Exception $exc) {
                $response['success'] = false;
                $response['message'] = $exc->getMessage();
            }
        } else {
            $response['message'] = 'Not allowed';
        }

        return $response;
    }

    /**
     * @param Transaction $entity
     * @param User        $user
     * @param string      $item
     * @param string      $group
     * @param string      $price
     * @param string      $currency
     * @param string      $date
     *
     * @return Transaction
     */
    private function update(
        Transaction $entity,
        User $user,
        $item,
        $group,
        $price,
        $currency,
        $date
    ) {
        $amount        = is_null($price) ? $entity->getPrice() : $this->getMoney()->getAmount($price);
        $item          = is_null($item) ? $entity->getItem()->getName() : $item;
        $group         = is_null($group) ? $entity->getGroup()->getName() : $group;
        $currencyValue = is_null($currency) ? $entity->getCurrency()->getCurrency() : $currency;
        $date          = is_null($date) ? $entity->getDate() : $this->getDate()->getDateTime($user, $date);

        $transaction = $this->getSave()->save(
            $entity,
            $user,
            $item,
            $group,
            $amount,
            $currencyValue,
            $date
        );

        return $transaction;
    }
}
