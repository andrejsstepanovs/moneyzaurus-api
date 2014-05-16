<?php

namespace Api\Controller\Predict;

use Api\Entities\User;
use Api\Service\Predict\Price as PredictPrice;
use Api\Service\Transaction\Data as TransactionData;
use Api\Service\AccessorTrait;

/**
 * Class PriceController
 *
 * @package Api\Controller\Predict
 *
 * @method PriceController setData(TransactionData $data)
 * @method PriceController setPredictPrice(PredictPrice $predictPrice)
 * @method TransactionData getData()
 * @method PredictPrice    getPredictPrice()
 */
class PriceController
{
    use AccessorTrait;

    /**
     * @param User   $user
     * @param array  $connectedUserIds
     * @param string $item
     * @param string $group
     *
     * @return array
     */
    public function getResponse(User $user, array $connectedUserIds, $item, $group)
    {
        $userIds = array_merge(array($user->getId()), $connectedUserIds);

        $prices = $this->getPredictPrice()->predict($userIds, $item, $group);

        $data = array(
            'success' => true,
            'count'   => count($prices),
            'data'    => $this->getData()->normalizeResults($prices, $user),
        );

        return $data;
    }
}