<?php

namespace Api\Controller\Distinct;

use Api\Entities\User;
use Api\Service\AccessorTrait;
use Api\Entities\Group;
use Api\Service\Groups\Data as GroupsData;

/**
 * Class GroupsController
 *
 * @package Api\Controller\Distinct
 *
 * @method GroupsController setGroupsData(GroupsData $group)
 * @method GroupsData       getGroupsData()
 */
class GroupsController
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
        $userIds = array_merge(array($user->getId()), $connectedUserIds);

        if ($dateFrom) {
            $timeZone = new \DateTimeZone($user->getTimezone());
            $dateFrom = new \DateTime($dateFrom, $timeZone);
        }

        $groups = $this->getGroups($userIds, $count, $dateFrom);

        $response = array(
            'success' => true,
            'count'   => count($groups),
            'data'    => $groups
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
    private function getGroups(array $userIds, $count, \DateTime $dateFrom = null)
    {
        /** @var Group[] $groups */
        $groups = $this->getGroupsData()->getGroups($userIds, $dateFrom);

        if ($count) {
            $groups = array_slice($groups, 0, $count);
        }

        return $groups;
    }
}
