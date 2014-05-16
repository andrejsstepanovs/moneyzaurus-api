<?php

namespace Api\Controller\Predict;

use Api\Entities\User;
use Api\Service\Predict\Group as PredictGroup;
use Api\Service\AccessorTrait;

/**
 * Class GroupController
 *
 * @package Api\Controller\Predict
 *
 * @method GroupController setPredictGroup(PredictGroup $predictGroup)
 * @method PredictGroup    getPredictGroup()
 */
class GroupController
{
    use AccessorTrait;

    /**
     * @param User   $user
     * @param array  $connectedUserIds
     * @param string $item
     *
     * @return array
     */
    public function getResponse(User $user, array $connectedUserIds, $item)
    {
        $userIds = array_merge(array($user->getId()), $connectedUserIds);

        $groups = $this->getPredictGroup()->predict($userIds, $item);
        $groups = array_column($groups, 'name');

        $data = array(
            'success' => true,
            'count'   => count($groups),
            'data'    => $groups,
        );

        return $data;
    }
}