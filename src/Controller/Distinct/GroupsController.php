<?php

namespace Api\Controller\Distinct;

use Api\Entities\User;
use Api\Entities\Group;
use Doctrine\ORM\EntityRepository;
use Api\Service\AccessorTrait;

/**
 * Class GroupsController
 *
 * @package Api\Controller\Distinct
 *
 * @method GroupsController setGroupRepository(EntityRepository $group)
 * @method EntityRepository getGroupRepository()
 */
class GroupsController
{
    use AccessorTrait;

    /**
     * @param User  $user
     * @param array $connectedUserIds
     *
     * @return array
     */
    public function getResponse(User $user, array $connectedUserIds)
    {
        $userIds = array_merge(array($user->getId()), $connectedUserIds);
        $groups = $this->getGroups($userIds);

        $response = array(
            'success' => true,
            'count'   => count($groups),
            'data'    => $groups
        );

        return $response;
    }

    /**
     * @param array $userIds
     *
     * @return array
     */
    private function getGroups(array $userIds)
    {
        $criteria = array('user' => $userIds);
        $orderBy = array('name' => 'ASC');

        /** @var Group[] $groups */
        $groups = $this->getGroupRepository()->findBy($criteria, $orderBy);

        $data = array();
        foreach ($groups as $group) {
            $data[$group->getName()] = null;
        }

        return array_keys($data);
    }
}
