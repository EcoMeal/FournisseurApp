<?php

namespace AppBundle\Services;

use Doctrine\ORM\EntityManager;
use AppBundle\Entity\BasketOrder;
use DateTime;

class BasketOrderService {

    private $em;

    public function __construct(EntityManager $entityManager) {
        $this->em = $entityManager;
    }

    /**
     * Checks if an order is valid.
     *
     * @param $order the order in a JSON format
     *
     * @return boolean true if the order is valid, false otherwise
     */
    public function checkOrder($order) {

        $userName = $order->username;
        //TODO verification on username

        $orderTime = $order->order_time;
        //TODO verification on order_time;

        $deliveryTime = $order->delivery_time;
        //we should probably check that the delivery_time is correct or whatever

        $order_content = $order->content;
        //Check that there is at list one basket in the order
        if (empty($order_content)) {
            return false;
        }

        //Check the integrity of all the baskets
        foreach ($order_content as $basket_id) {
            $basket = $this->em->getRepository("AppBundle:Basket")->find($basket_id);
            //If the basket can't be found, returns false
            if (is_null($basket)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Saves an order in the database.
     *
     * @param $order the order in a JSON format
     *
     * @return number the id of the order in the database if it was successfully saved
     * 	null otherwise
     */
    public function saveOrder($order) {
        //Check if the order is valid
        if ($this->checkOrder($order)) {
            $basketRepository = $this->em->getRepository("AppBundle:Basket");
            $basketOrder = new BasketOrder();
            $baskets = array();

            //Get all the baskets from database
            foreach ($order->content as $basket_id) {
                $basket = $basketRepository->find($basket_id);
                array_push($baskets, $basket);
            }

            $basketOrder->setOrderContent($baskets);
            $date = new \DateTime();
            $date->setTimestamp($order->delivery_time);
            $basketOrder->setDeliveryTime($date);
            $this->em->persist($basketOrder);
            $this->em->flush();
            return $basketOrder->getId();
        } else {
            return null;
        }
    }
    public function getAllOrdersWithBasketListOrderedByTime()
    {
        return $this->em->getRepository("AppBundle:BasketOrder")->findBy([], ['deliveryTime' => 'ASC']);
    }
    

}
