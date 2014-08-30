<?php

namespace Api\Service\Predict;

use Doctrine\ORM\EntityManager;
use Api\Service\AccessorTrait;

/**
 * Class Group
 *
 * @package Api\Service\Predict
 *
 * @method Group         setEntityManager(EntityManager $entityManager)
 * @method EntityManager getEntityManager()
 */
class Group
{
    use AccessorTrait;

    const LIMIT      = 5;
    const USED_COUNT = 2;
    const DATE_FROM  = '-1 year';

    /**
     * @param array  $userIds
     * @param string $item
     *
     * @return array
     */
    public function predict(array $userIds, $item)
    {
        $data = $this->findGroups(
            $userIds,
            $item,
            self::USED_COUNT,
            self::LIMIT,
            new \DateTime(self::DATE_FROM)
        );

        return $data;
    }

    /**
     * @param array     $userIds
     * @param string    $item
     * @param int       $usedCount
     * @param int       $limit
     * @param \DateTime $dateFrom
     *
     * @return array
     */
    private function findGroups(
        array $userIds,
        $item,
        $usedCount,
        $limit,
        \DateTime $dateFrom
    ) {
        $dql = 'SELECT '
               . 'g.name, COUNT(t.id) as usedCount '
               . 'FROM \Api\Entities\Transaction t '
               . 'JOIN t.item i '
               . 'JOIN t.group g '
               . 'WHERE t.user IN (:userIds) AND t.date >= :dateFrom';

        $parameters = array(
            'userIds'  => $userIds,
            'dateFrom' => $dateFrom->format('Y-m-d')
        );

        if (!empty($item)) {
            $dql .= ' AND i.name LIKE :item';
            $parameters['item'] = $item;
        }

        $dql .= ' GROUP BY g.name';
        $dql .= ' HAVING usedCount >= ' . (int)$usedCount;
        $dql .= ' ORDER BY usedCount DESC';

        return $this->fetchResults($dql, $parameters, $limit);
    }

    /**
     * @param string $dql
     * @param array  $parameters
     * @param int    $limit
     *
     * @return array
     */
    private function fetchResults($dql, array $parameters, $limit)
    {
        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameters($parameters);
        $query->setMaxResults($limit);

        return $query->getResult();
    }

}
