<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * BasketOrder
 *
 * @ORM\Table(name="basket_order")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\BasketOrderRepository")
 */
class BasketOrder
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="delivery_time", type="datetime")
     */
    private $deliveryTime;


    /**
     * Contains the content of a BasketOrder (An array of Baskets)
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Basket", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $order_content;
    
    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set deliveryTime
     *
     * @param \DateTime $deliveryTime
     *
     * @return BasketOrder
     */
    public function setDeliveryTime($deliveryTime)
    {
        $this->deliveryTime = $deliveryTime;

        return $this;
    }

    /**
     * Get deliveryTime
     *
     * @return \DateTime
     */
    public function getDeliveryTimeFormated()
    {
        return $this->deliveryTime->format('H:i');
    }
    
    /**
     * Returns the baskets contained in the order
     * @return Array<Basket> 
     * 
     */
    public function getOrderContent() {
    	return $this->order_content;
    }
    
    /**
     * Set the basket contained in the order
     * @param Array<Basket> $order_content
     * @return $this
     */
    public function setOrderContent($order_content) {
    	$this->order_content = $order_content;
    	return $this;
    }

}

