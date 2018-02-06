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
     * Contains the content of a BasketOrder (An array of BasketOrderContent)
     * 
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\BasketOrderContent", mappedBy="basketOrder", cascade={"persist"})
     * @ORM\JoinColumn(name="basket_order_id")
     */
    private $basketOrderContent;
    
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
     * @return Array<BasketOr> 
     * 
     */
    public function getBasketOrderContent() {
    	return $this->basketOrderContent;
    }
    
    /**
     * Set the baskets contained in the order
     * @param Array<BasketOrderContent> $basketOrderContent
     * @return $this
     */
    public function setBasketOrderContent($basketOrderContent) {
    	$this->basketOrderContent = $basketOrderContent;
    	return $this;
    }

}

