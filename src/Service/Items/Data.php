<?php

namespace Api\Service\Items;

use Doctrine\ORM\EntityManager;
use Api\Service\AccessorTrait;

/**
 * Class Items
 *
 * @package Api\Service\Items
 *
 * @method Data          setEntityManager(EntityManager $entityManager)
 * @method EntityManager getEntityManager()
 */
class Data
{
    use AccessorTrait;

    /**
     * @param array          $userIds
     * @param \DateTime|null $dateFrom
     *
     * @return array
     */
    public function getItems(array $userIds, \DateTime $dateFrom = null)
    {
        $dql = 'SELECT i.name, COUNT(t.id) usedCount ';
        $dql .= 'FROM \Api\Entities\Transaction t JOIN t.item i ';
        $dql .= 'WHERE i.user IN (:userIds)';

        $parameters = array('userIds' => $userIds);
        if (!empty($dateFrom)) {
            $dql .= ' AND t.date >= :dateFrom';
            $parameters['dateFrom'] = $dateFrom->format('Y-m-d');
        }

        $dql .= ' GROUP BY i.name';
        $dql .= ' ORDER BY usedCount DESC';

        $transactions = $this->fetchResults($dql, $parameters);

        $transactions = array_column($transactions, 'name');

        return array_unique($transactions);
    }

    /**
     * @param string $dql
     * @param array  $parameters
     *
     * @return array
     */
    private function fetchResults($dql, array $parameters)
    {
        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameters($parameters);

        return $query->getResult();
    }
}
