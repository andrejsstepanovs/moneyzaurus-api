<?php

namespace Api\Controller\Transactions;


use Api\Service\Transaction\Money;
use Api\Service\Transaction\Validate;
use Api\Service\Transaction\Data as TransactionData;
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
 * @method Money           getMoney()
 * @method Validate        getValidate()
 * @method TransactionData getData()
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
            'success' => false,
        );

        $entity = $this->getData()->find($id);
        if ($entity && $this->getValidate()->isAllowed($user, $connectedUserIds, $entity)) {
            $dataService     = $this->getData();
            $transactionData = $dataService->toArray($entity);

            $normalizedData = $dataService->normalizeResults(array($transactionData), $user);
            $data['data']    = reset($normalizedData);
            $data['success'] = true;
        }

        return $data;
    }
}
