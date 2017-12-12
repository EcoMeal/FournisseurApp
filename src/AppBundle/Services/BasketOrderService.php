<?php

namespace AppBundle\Services;

use Doctrine\ORM\EntityManager;
use AppBundle\Entity\BasketOrder;
use AppBundle\Entity\Stock;
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
		if(empty($order_content)) {
			return (object) array("valid" => false, "errorMessage" => "No basket in the order");
		}
		
                $baskets = array();
                
		// Check the integrity of all the baskets
		foreach($order_content as $basket_id) {
			$basket = $this->em->getRepository("AppBundle:Basket")->find($basket_id);
                        array_push($baskets, $basket);
			//If the basket can't be found, returns false
			if(is_null($basket)) {
				return (object) array("valid" => false,
                                    "errorMessage" => "Invalid basket for id ".$basket_id);
			}
		}
                  
                return $this->checkOrderStock($baskets);
                
        }
        
        
        public function checkOrderStock($baskets)
        {
            //A map which will contain the required stock for each product in the basket list
            $stockNeeded = array();
            
            foreach($baskets as $basket) {
                
                foreach($basket->getProductList() as $product) {
                   
                    // If the product is already in the array, we increase the amount
                    if(in_array($product->getId(), $stockNeeded)) {
                        $stockNeeded[$product->getId()] += 1;
                    } else { //Otherwise we set it to 1
                        $stockNeeded[$product->getId()] = 1;
                    }
                   
               }
                  
            }
           
            // Checks that there is enough of each product
            foreach($stockNeeded as $product_id => $amount) {
                $stock = $this->em->getRepository("AppBundle:Stock")
                        ->findCurrentStockFor($product_id);
                $amountAvailable = $stock->getQuantity();
               	if($amountAvailable < $amount) {
                    return (object) array("valid" => false, 
                        "errorMessage" => "Not enough stock for product ".$product_id);
                }
            }
            return (object) array("valid" => true);
            
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
		if($this->checkOrder($order)) {
			$basketRepository = $this->em->getRepository("AppBundle:Basket");
			$basketOrder = new BasketOrder();
			$baskets = array();
			
			//Get all the baskets from database
			foreach($order->content as $basket_id) {
				$basket = $basketRepository->find($basket_id);
				foreach($basket->getProductList() as $product) {
					$stock = $this->em->getRepository("AppBundle:Stock")
						->findCurrentStockFor($product->getId());
					$newStock = new Stock();
					$newStock->setProduct($product);
					$currentDate = new DateTime();
					$currentDate->setTimestamp(time());
					$newStock->setDate($currentDate);
					$newStock->setQuantity($stock->getQuantity()-1);
					$this->em->persist($newStock);
					$this->em->flush($newStock);
				}
				array_push($baskets, $basket);
			}
			
			$basketOrder->setOrderContent($baskets);
			$date = new DateTime();
			$date->setTimestamp($order->delivery_time);
			$basketOrder->setDeliveryTime($date);
			$this->em->persist($basketOrder);
			$this->em->flush();
			return $basketOrder->getId();
		} else {
			return null;
		}
	}
	
	/**
	 * Returns the orders with their basket list ordered by Time
	 * @return Array<Basket>
	 */
	public function getAllOrdersWithBasketListOrderedByTime() {
		return $this->em->getRepository("AppBundle:BasketOrder")->findBy([], ['deliveryTime' => 'ASC']);
	}
	
	
}
