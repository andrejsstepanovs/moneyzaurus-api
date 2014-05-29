<?php

namespace Api\Service\Predict;

use Doctrine\ORM\EntityManager;
use Api\Service\AccessorTrait;

/**
 * Class Price
 *
 * @package Api\Service\Predict
 *
 * @method Price         setEntityManager(EntityManager $entityManager)
 * @method EntityManager getEntityManager()
 */
class Price
{
    use AccessorTrait;

    const LIMIT      = 2;
    const USED_COUNT = 3;
    const DATE_FROM  = '-6 months';

    /**
     * @param array  $userIds
     * @param string $item
     * @param string $group
     *
     * @return array
     */
    public function predict(array $userIds, $item, $group)
    {
        $data = $this->findPrices(
            $userIds,
            $item,
            $group,
            self::USED_COUNT,
            self::LIMIT,
            new \DateTime(self::DATE_FROM)
        );

        return $data;
    }

    /**
     * @param array     $userIds
     * @param string    $item
     * @param string    $group
     * @param int       $usedCount
     * @param int       $limit
     * @param \DateTime $dateFrom
     *
     * @return array
     */
    private function findPrices(
        array $userIds,
        $item,
        $group,
        $usedCount,
        $limit,
        \DateTime $dateFrom = null
    ) {
        $dql = 'SELECT '
               . 't.price AS amount, u.locale, u.timezone, c.currency, c.name as currencyName, '
               . 'c.html as currencySymbol, COUNT(t.id) AS usedCount '
               . 'FROM \Api\Entities\Transaction t '
               . 'JOIN t.item i '
               . 'JOIN t.group g '
               . 'JOIN t.user u '
               . 'JOIN t.currency c '
               . 'WHERE t.user IN (:userIds)';

        $parameters = array('userIds' => $userIds);

        if (!empty($dateFrom)) {
            $dql .= ' AND t.date >= :dateFrom';
            $parameters['dateFrom'] = $dateFrom->format('Y-m-d');
        }

        if (!empty($item)) {
            $dql .= ' AND i.name LIKE :item';
            $parameters['item'] = $item;
        }

        if (!empty($group)) {
            $dql .= ' AND g.name LIKE :group';
            $parameters['group'] = $group;
        }

        $dql .= ' GROUP BY t.price';
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