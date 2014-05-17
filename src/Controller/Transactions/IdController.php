<?php

namespace Api\Controller\Transactions;

use Api\Service\Locale;
use Api\Service\Transaction\Money;
use Api\Service\Transaction\Validate;
use Api\Service\Transaction\Data as TransactionData;
use Api\Entities\Transaction;
use Api\Entities\User;
use Api\Service\AccessorTrait;

/**
 * Class IdController
 *
 * @package Api\Controller\Transactions
 *
 * @method IdController    setMoney(Money $money)
 * @method IdController    setValidate(Validate $validate)
 * @method IdController    setData(TransactionData $data)
 * @method IdController    setLocale(Locale $locale)
 * @method Money           getMoney()
 * @method Validate        getValidate()
 * @method TransactionData getData()
 * @method Locale          getLocale()
 */
class IdController
{
    use AccessorTrait;

    /**
     * @param User  $user
     * @param array $connectedUserIds
     * @param int   $id
     *
     * @return array
     */
    public function getResponse(User $user, array $connectedUserIds, $id)
    {
        $data = array(
            'success' => false
        );

        $entity = $this->getData()->find($id);
        if ($entity && $this->getValidate()->isAllowed($user, $connectedUserIds, $entity)) {
            $locale = $this->getLocale()
                ->setLocale($entity->getUser()->getLocale())
                ->setTimezone($entity->getUser()->getTimezone());

            $transactionData = $this->getTransactionData($entity, $locale);

            $data['success'] = true;
            $data['data']    = $transactionData;
        }

        return $data;
    }

    /**
     * @param Transaction $entity
     * @param Locale      $locale
     *
     * @return array
     */
    private function getTransactionData(Transaction $entity, Locale $locale)
    {
        $user        = $entity->getUser();
        $date        = $entity->getDate();
        $currency    = $entity->getCurrency()->getCurrency();
        $dateCreated = $entity->getDateCreated();
        $price       = $entity->getPrice();

        return array(
            'id'                => $entity->getId(),
            'item'              => $entity->getItem()->getName(),
            'group'             => $entity->getGroup()->getName(),
            'amount'            => $price,
            'price'             => $this->getMoney()->getPrice($price),
            'money'             => $locale->getFormattedMoney($currency, $price),
            'currency'          => $currency,
            'date'              => $locale->getDateFormatter()->format($date->getTimestamp()),
            'date_full'         => $locale->getDateFormatter(\IntlDateFormatter::FULL)->format($date->getTimestamp()),
            'date_timestamp'    => $date->getTimestamp(),
            'created'           => $locale->getDateTimeFormatter()->format($dateCreated->getTimestamp()),
            'created_full'      => $locale->getDateTimeFormatter(\IntlDateFormatter::FULL)->format($dateCreated->getTimestamp()),
            'created_timestamp' => $dateCreated->getTimestamp(),
            'user'              => $user->getId(),
            'email'             => $user->getEmail()
        );
    }

}