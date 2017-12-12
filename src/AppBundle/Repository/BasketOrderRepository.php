<?php

namespace AppBundle\Repository;

/**
 * BasketOrderRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class BasketOrderRepository extends \Doctrine\ORM\EntityRepository {

    /**
     * Returns the orders between two dates
     * @param dateTime $start
     * @param dateTime $end
     * @return Array<Basket>
     */
    public function getOrdersBetween($start, $end) {
        $qb = $this->createQueryBuilder('o')
                ->where('o.deliveryTime BETWEEN :start AND :end');
        $qb->setParameter(':start', $start);
        $qb->setParameter(':end', $end);
        return $qb->getQuery()->getArrayResult();
    }

    public function getAllOrdersWithBasketListOrderedByTime() {
        $qb = $this->createQueryBuilder('o')
                ->leftJoin('o.orderContent ', "basket")
                ->addSelect('basket')
                ->leftJoin('basket.product_list ', "product")
                ->addSelect('product');
        return $qb->getQuery()->getArrayResult();
    }

}
