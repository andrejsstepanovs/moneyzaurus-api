<?php

namespace Api\Controller\Connection;

use Api\Entities\User;
use Api\Service\AccessorTrait;
use Api\Service\Connection\Data as ConnectionData;

/**
 * Class ListController
 *
 * @package Api\Controller\Distinct
 *
 * @method ListController setConnectionData(ConnectionData $connectionData)
 * @method ConnectionData getConnectionData()
 */
class ListController
{
    use AccessorTrait;

    /**
     * @param User $user
     * @param bool $parent
     *
     * @return array
     */
    public function getResponse(User $user, $parent)
    {
        $connectionData = $this->getConnectionData();
        if ($parent) {
            $connections = $connectionData->findByParent($user);
        } else {
            $connections = $connectionData->findByUser($user);
        }

        $connectedUsers = $connectionData->normalizeResults($user, $connections);

        $response = array(
            'success' => true,
            'count'   => count($connectedUsers),
            'data'    => $connectedUsers
        );

        return $response;
    }

}