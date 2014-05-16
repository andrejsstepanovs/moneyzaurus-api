<?php

namespace Api\Service\Transaction;

use Api\Entities\Transaction;
use Api\Entities\User;

/**
 * Class Validate
 *
 * @package Api\Service\Transaction
 */
class Validate
{
    /**
     * @param User        $user
     * @param array       $connectedUserIds
     * @param Transaction $transaction
     *
     * @return bool
     */
    public function isAllowed(User $user, array $connectedUserIds, Transaction $transaction)
    {
        $userIds = array_merge(array($user->getId()), $connectedUserIds);

        $userId = $transaction->getUser()->getId();

        return in_array($userId, $userIds);
    }
}
