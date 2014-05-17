<?php

namespace Api\Service\Transaction;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Api\Entities\Item;
use Api\Entities\Group;
use Api\Entities\Currency;
use Api\Entities\Transaction;
use Api\Entities\User;
use SebastianBergmann\Money\Money as MoneyClass;
use SebastianBergmann\Money\Currency as MoneyCurrency;
use Api\Service\AccessorTrait;

/**
 * Class Save
 *
 * @package Api\Service\Transaction
 *
 * @method Save             setEntityManager(EntityManager $entityManager)
 * @method Save             setCurrencyEntity(EntityRepository $currencyEntity)
 * @method Save             setGroupEntity(EntityRepository $groupEntity)
 * @method Save             setItemEntity(EntityRepository $itemEntity)
 * @method EntityManager    getEntityManager()
 * @method EntityRepository getCurrencyEntity()
 * @method EntityRepository getGroupEntity()
 * @method EntityRepository getItemEntity()
 */
class Save
{
    use AccessorTrait;

    /**
     * @param Currency $currency
     * @param int      $amount
     *
     * @return MoneyClass
     */
    private function getMoney(Currency $currency, $amount)
    {
        $currency = new MoneyCurrency($currency->getCurrency());
        $money = new MoneyClass($amount, $currency);

        return $money;
    }

    /**
     * @param string $currency
     *
     * @return Currency
     * @throws \Exception
     */
    private function findCurrency($currency)
    {
        $criteria = array('currency' => $currency);
        $entity = $this->getCurrencyEntity()->findOneBy($criteria);

        if ($entity === null) {
            throw new \RuntimeException('Currency ' . $currency . ' not found');
        }

        return $entity;
    }

    /**
     * @param User   $user
     * @param string $item
     *
     * @return Item
     */
    private function findItem(User $user, $item)
    {
        $criteria = array(
            'name' => $item,
            'user' => $user
        );

        $entity = $this->getItemEntity()->findOneBy($criteria);
        if ($entity === null) {
            $entity = $this
                ->createItem($item, $user, new \DateTime())
                ->findItem($user, $item);
        }

        return $entity;
    }

    /**
     * @param string    $name
     * @param User      $user
     * @param \DateTime $dateTime
     *
     * @return $this
     */
    private function createItem($name, User $user, \DateTime $dateTime)
    {
        $entity = new Item;
        $entity
            ->setName($name)
            ->setUser($user)
            ->setDateCreated($dateTime);

        $entityManager = $this->getEntityManager();
        $entityManager->persist($entity);
        $entityManager->flush($entity);

        return $this;
    }

    /**
     * @param User   $user
     * @param string $group
     *
     * @return Group
     */
    private function findGroup(User $user, $group)
    {
        $criteria = array(
            'name' => $group,
            'user' => $user
        );

        $entity = $this->getGroupEntity()->findOneBy($criteria);
        if ($entity === null) {
            $entity = $this
                ->createGroup($group, $user, new \DateTime())
                ->findGroup($user, $group);
        }

        return $entity;
    }

    /**
     * @param string    $name
     * @param User      $user
     * @param \DateTime $dateTime
     *
     * @return $this
     */
    private function createGroup($name, User $user, \DateTime $dateTime)
    {
        $entity = new Group;
        $entity
            ->setName($name)
            ->setUser($user)
            ->setDateCreated($dateTime);

        $entityManager = $this->getEntityManager();
        $entityManager->persist($entity);
        $entityManager->flush($entity);

        return $this;
    }

    /**
     * @param Transaction $transaction
     * @param User        $user
     * @param string      $item
     * @param string      $group
     * @param int         $amount
     * @param string      $currency
     * @param \DateTime   $dateTime
     *
     * @return Transaction
     * @throws \Exception
     */
    public function save(
        Transaction $transaction,
        User $user,
        $item,
        $group,
        $amount,
        $currency,
        \DateTime $dateTime
    ) {
        $this->getEntityManager()->beginTransaction();
        try {
            $currencyEntity = $this->findCurrency($currency);

            $transaction = $this->saveTransaction(
                $transaction,
                $user,
                $this->findItem($user, $item),
                $this->findGroup($user, $group),
                $currencyEntity,
                $this->getMoney($currencyEntity, $amount),
                $dateTime
            );
            $this->getEntityManager()->commit();
        } catch (\Exception $exc) {
            $this->getEntityManager()->rollback();

            throw $exc;
        }

        return $transaction;
    }

    /**
     * @param Transaction $entity
     * @param User        $user
     * @param Item        $item
     * @param Group       $group
     * @param Currency    $currency
     * @param MoneyClass  $price
     * @param \DateTime   $dateTime
     *
     * @return Transaction
     */
    private function saveTransaction(
        Transaction $entity,
        User $user,
        Item $item,
        Group $group,
        Currency $currency,
        MoneyClass $price,
        \DateTime $dateTime
    ) {
        $entity
            ->setUser($user)
            ->setItem($item)
            ->setGroup($group)
            ->setCurrency($currency)
            ->setPrice($price->getAmount())
            ->setDateCreated(new \DateTime)
            ->setDate($dateTime);

        $entityManager = $this->getEntityManager();
        $entityManager->persist($entity);
        $entityManager->flush($entity);

        return $entity;
    }
}
