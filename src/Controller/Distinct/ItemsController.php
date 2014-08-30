<?php

namespace Api\Controller\Distinct;

use Api\Entities\User;
use Api\Service\AccessorTrait;
use Api\Service\Items\Data as ItemsData;

/**
 * Class GroupsController
 *
 * @package Api\Controller\Distinct
 *
 * @method ItemsController  setItemsData(ItemsData $item)
 * @method ItemsData        getItemsData()
 */
class ItemsController
{
    use AccessorTrait;

    /**
     * @param User   $user
     * @param array  $connectedUserIds
     * @param string $dateFrom
     * @param int    $count
     *
     * @return array
     */
    public function getResponse(User $user, array $connectedUserIds, $dateFrom, $count)
    {
        if ($dateFrom) {
            $timeZone = new \DateTimeZone($user->getTimezone());
            $dateFrom = new \DateTime($dateFrom, $timeZone);
        }

        $userIds = array_merge(array($user->getId()), $connectedUserIds);
        $items   = $this->getItems($userIds, $count, $dateFrom);

        $response = array(
            'success' => true,
            'count'   => count($items),
            'data'    => $items
        );

        return $response;
    }

    /**
     * @param array          $userIds
     * @param int            $count
     * @param \DateTime|null $dateFrom
     *
     * @return array
     */
    private function getItems($userIds, $count, $dateFrom = null)
    {
        $items = $this->getItemsData()->getItems($userIds, $dateFrom);

        if ($count) {
            $items = array_slice($items, 0, $count);
        }

        return $items;
    }
}
