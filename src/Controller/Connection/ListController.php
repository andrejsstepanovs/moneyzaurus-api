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
     *
     * @return array
     */
    public function getResponse(User $user)
    {
        $connections    = $this->getConnectionData()->findByUser($user);
        $connectedUsers = $this->getConnectionData()->normalizeResults($user, $connections);

        $response = array(
            'success' => true,
            'count'   => count($connectedUsers),
            'data'    => $connectedUsers
        );

        return $response;
    }

}