<?php

namespace Api\Service\Chart;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Api\Service\Locale;
use Api\Entities\Transaction;
use Api\Entities\User;
use Api\Service\AccessorTrait;

/**
 * Class Transactions
 *
 * @package Api\Service\Transaction
 *
 * @method Pie              setTransactionEntity(EntityRepository $transactionEntity)
 * @method Pie              setLocale(Locale $locale)
 * @method Pie              setEntityManager(EntityManager $entityManager)
 * @method EntityRepository getTransactionEntity()
 * @method Locale           getLocale()
 * @method EntityManager    getEntityManager()
 */
class Pie
{
    use AccessorTrait;

    /**
     * @return string
     */
    private function getBaseDql()
    {
        $dql = 'SELECT '
               . 'SUM(t.price) as amount, g.id as groupId, g.name as groupName '
               . 'FROM \Api\Entities\Transaction t '
               . 'JOIN t.group g '
               . '{{ WHERE }} '
               . 'GROUP BY g.id';

        return $dql;
    }

    /**
     * @param array          $userIds
     * @param string         $currency
     * @param \DateTime|null $dateFrom
     * @param \DateTime|null $dateTill
     *
     * @return array
     */
    public function getData(
        array $userIds,
        $currency,
        \DateTime $dateFrom = null,
        \DateTime $dateTill = null
    ) {
        $dql = $this->getBaseDql();
        $where = ' WHERE t.user IN (:userIds)';

        $parameters = array('userIds' => $userIds);

        if (!empty($dateFrom)) {
            $where .= ' AND t.date >= :dateFrom';
            $parameters['dateFrom'] = $dateFrom->format('Y-m-d');
        }

        if (!empty($dateTill)) {
            $where .= ' AND t.date <= :dateTill';
            $parameters['dateTill'] = $dateTill->format('Y-m-d');
        }

        if (!empty($currency)) {
            $where .= ' AND t.currency = :currency';
            $parameters['currency'] = $currency;
        }

        $dql = str_replace('{{ WHERE }}', $where, $dql);

        $offset = null;
        $limit  = null;
        $data = $this->fetchResults($dql, $parameters, $limit, $offset);

        return $data;
    }

    /**
     * @param string $dql
     * @param array  $parameters
     * @param int    $limit
     * @param int    $offset
     *
     * @return array
     */
    private function fetchResults($dql, array $parameters, $limit, $offset)
    {
        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameters($parameters);
        $query->setMaxResults($limit);
        $query->setFirstResult($offset);

        return $query->getResult();
    }

    /**
     * @param array $data
     *
     * @return array
     */
    public function addPercent(array $data)
    {
        $column = array_column($data, 'amount');
        $total = array_sum($column);

        foreach ($data as &$row) {
            $row['percent'] = $row['amount'] / $total * 100;
        }

        return $data;
    }

    /**
     * @param array $data
     *
     * @return array
     */
    public function sortByPercent(array $data)
    {
        usort($data, function ($rowA, $rowB) {
            $valueA = $rowA['percent'];
            $valueB = $rowB['percent'];

            if ($valueA == $valueB) {
                return strcasecmp($rowA['groupName'], $rowB['groupName']);
            }

            return $valueA < $valueB ? 1 : -1;
        });

        return $data;
    }

    /**
     * @param array $groups
     *
     * @return array
     */
    public function normalizeResults(array $groups, User $user, $currency)
    {
        $userLocale   = $user->getLocale();
        $userTimezone = $user->getTimezone();

        foreach ($groups as &$data) {
            foreach ($data as $key => &$value) {
                if ($key == 'amount') {
                    /** @var int $value */
                    $data['price'] = sprintf('%0.2f', $value / 100);
                    $data['money'] = $this
                        ->getLocale()
                        ->setLocale($userLocale)
                        ->setTimezone($userTimezone)
                        ->getFormattedMoney($currency, (int) $value);
                }
            }
        }

        return $groups;
    }
}
