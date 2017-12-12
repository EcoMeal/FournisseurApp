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

    /**
     * Returns all the Orders with the content of the baskets and the product of these baskets
     * @return Array<BasketOrder>
     */
    public function getAllOrdersWithBasketListOrderedByTime() {
        $qb = $this->createQueryBuilder('o')
                ->leftJoin('o.orderContent ', "basket")
                ->addSelect('basket')
                ->leftJoin('basket.product_list ', "product")
                ->addSelect('product')
                ->orderBy('o.deliveryTime');
        return $qb->getQuery()->getArrayResult();
    }

    /**
     * Returns the order and its content corresponding to the $id
     * @param integer $id
     * @return BasketOrder
     */
    public function getOrderWithBasketList($id) {
        $qb = $this->createQueryBuilder('o')
                ->leftJoin('o.orderContent ', "basket")
                ->addSelect('basket')
                ->leftJoin('basket.product_list ', "product")
                ->addSelect('product')
                ->where('o.id = :id')
                ->orderBy('o.deliveryTime');
        $qb->setParameter(':id', $id);
        return $qb->getQuery()->getArrayResult();
    }

}
