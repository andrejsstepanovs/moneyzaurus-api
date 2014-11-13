<?php

namespace Api\Controller\Transactions;

use Api\Entities\User;
use Api\Service\Transaction\Remove;
use Api\Service\Transaction\Validate;
use Api\Service\Transaction\Data as TransactionData;
use Api\Service\AccessorTrait;

/**
 * Class CreateController
 *
 * @package Api\Controller\Transactions
 *
 * @method RemoveController setValidate(Validate $validate)
 * @method RemoveController setData(TransactionData $data)
 * @method RemoveController setRemove(Remove $remove)
 * @method Validate         getValidate()
 * @method TransactionData  getData()
 * @method Remove           getRemove()
 */
class RemoveController
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
        $response = array('success' => false);

        $entity = $this->getData()->find($id);
        if ($entity && $this->getValidate()->isAllowed($user, $connectedUserIds, $entity)) {
            $success = $this->getRemove()->remove($entity);
            if ($success) {
                $response['success'] = true;
            }
        } else {
            $response['message'] = 'Not allowed';
        }

        return $response;
    }
}
