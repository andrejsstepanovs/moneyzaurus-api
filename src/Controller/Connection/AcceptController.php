<?php

namespace Api\Controller\Connection;

use Api\Entities\User;
use Api\Entities\Connection;
use Api\Service\Connection\Save as ConnectionSave;
use Api\Service\Connection\Data as ConnectionData;
use Api\Service\AccessorTrait;

/**
 * Class AcceptController
 *
 * @package Api\Controller\Distinct
 *
 * @method AddController  setConnectionSave(ConnectionSave $connectionSave)
 * @method AddController  setConnectionData(ConnectionData $connectionData)
 * @method ConnectionSave getConnectionSave()
 * @method ConnectionData getConnectionData()
 */
class AcceptController
{
    use AccessorTrait;

    /**
     * @param User $user
     * @param int  $connectionId
     *
     * @return array
     */
    public function getResponse(User $user, $connectionId)
    {
        $response = array(
            'success' => false
        );

        try {
            $connection = $this->getConnectionData()->findById($connectionId);
            if (!$connection) {
                throw new \InvalidArgumentException('Connection not found');
            }

            if ($connection->getParent()->getId() != $user->getId()) {
                throw new \InvalidArgumentException('Connection cannot be accepted');
            }

            if ($connection->getState() == Connection::STATE_ACCEPTED) {
                throw new \InvalidArgumentException('Connection already accepted');
            }

            $connection->setState(Connection::STATE_ACCEPTED);
            $this->getConnectionSave()->save($connection);

            $response['success'] = true;

        } catch (\InvalidArgumentException $exc) {
            $response['message'] = $exc->getMessage();
        }

        return $response;
    }

}