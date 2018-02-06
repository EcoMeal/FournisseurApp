<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * BasketOrderContent
 *
 * @ORM\Table(name="basket_order_content")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\BasketOrderContentRepository")
 */
class BasketOrderContent
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
     * 
     * @var int
     * 
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Basket", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $basket;
    
    /**
     * 
     * @var int
     * 
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\BasketOrder", inversedBy="basketOrderContent", cascade={"persist"})
     * @ORM\JoinColumn(name="basket_order_id", referencedColumnName="id")
     */
    private $basketOrder;

    /**
     * @var int
     *
     * @ORM\Column(name="quantity", type="integer")
     */
    private $quantity;


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }
    
     /**
     * Set basket
     *
     * @param Basket $basket
     *
     * @return BasketOrderContent
     */
    public function setBasket($basket) {
    	$this->basket = $basket;
    	
    	return $this;
    }
    
    /**
     * Get basket
     * 
     * @return the basket
     */
    public function getBasket() {
    	return $this->basket;
    }

    /**
     * Set quantity
     *
     * @param integer $quantity
     *
     * @return BasketOrderContent
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
    
        return $this;
    }
    
    public function setBasketOrder($basketOrder) {
    	$this->basketOrder = $basketOrder;
    	return $this;
    }
    
    public function getBasketOrder() {
    	return $this->basketOrder;
    }

    /**
     * Get quantity
     *
     * @return integer
     */
    public function getQuantity()
    {
        return $this->quantity;
    }
}

