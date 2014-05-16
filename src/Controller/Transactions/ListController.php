<?php

namespace Api\Controller\Transactions;

use Api\Entities\User;
use Api\Service\Transaction\Data;
use Api\Service\Transaction\Date;
use Api\Service\AccessorTrait;

/**
 * Class ListController
 *
 * @package Api\Controller\Transactions
 *
 * @method ListController setDate(Date $date)
 * @method ListController setData(Data $data)
 * @method Date           getDate()
 * @method Data           getData()
 */
class ListController
{
    use AccessorTrait;

    /**
     * @param User   $user
     * @param array  $connectedUserIds
     * @param string $from
     * @param string $till
     * @param string $item
     * @param string $group
     * @param string $price
     *
     * @return array
     */
    public function getResponse(
        User $user,
        array $connectedUserIds,
        $offset,
        $limit,
        $from,
        $till,
        $item,
        $group,
        $price
    ) {
        $userIds = array_merge(array($user->getId()), $connectedUserIds);

        $dateFrom = !empty($from) ? $this->getDate()->getDateTime($user, $from) : null;
        $dateTill = !empty($till) ? $this->getDate()->getDateTime($user, $till) : null;

        $transactions = $this->getData()->getTransactionsList(
            $userIds,
            $offset,
            $limit,
            $dateFrom,
            $dateTill,
            $item,
            $group,
            $price
        );

        $response = array(
            'success' => true,
            'count'   => count($transactions),
            'data'    => $this->getData()->normalizeResults($transactions, $user)
        );

        return $response;
    }

}