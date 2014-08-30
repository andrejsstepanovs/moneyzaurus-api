<?php

namespace Api\Controller\Distinct;

use Api\Entities\User;
use Api\Entities\Item;
use Doctrine\ORM\EntityRepository;
use Api\Service\AccessorTrait;

/**
 * Class GroupsController
 *
 * @package Api\Controller\Distinct
 *
 * @method ItemsController  setItemRepository(EntityRepository $item)
 * @method EntityRepository getItemRepository()
 */
class ItemsController
{
    use AccessorTrait;

    /**
     * @param User   $user
     * @param array  $connectedUserIds
     *
     * @return array
     */
    public function getResponse(User $user, array $connectedUserIds)
    {
        $userIds = array_merge(array($user->getId()), $connectedUserIds);
        $items = $this->getItems($userIds);

        $response = array(
            'success' => true,
            'count'   => count($items),
            'data'    => $items
        );

        return $response;
    }

    /**
     * @param array $userIds
     *
     * @return array
     */
    private function getItems(array $userIds)
    {
        $criteria = array('user' => $userIds);
        $orderBy = array('name' => 'ASC');

        /** @var Item[] $items */
        $items = $this->getItemRepository()->findBy($criteria, $orderBy);

        $data = array();
        foreach ($items as $item) {
            $data[$item->getName()] = null;
        }

        return array_keys($data);
    }
}
