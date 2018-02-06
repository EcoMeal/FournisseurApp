<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * StockPromise
 *
 * @ORM\Table(name="stock_promise")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\StockPromiseRepository")
 */
class StockPromise
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
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Product", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $product;
    
    /**
     * @var int
     *
     * @ORM\Column(name="quantity", type="integer")
     */
    private $quantity;


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
     * Set quantity
     *
     * @param integer $quantity
     *
     * @return StockPromise
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get quantity
     *
     * @return int
     */
    public function getQuantity()
    {
        return $this->quantity;
    }
    
    /**
     * Set the promise product
     *
     * @param Product
     *
     * @return StockPromise
     */
    public function setProduct($product)
    {
        $this->product = $product;

        return $this;
    }
    
    /**
     * Get the promise product
     *
     * @return the promise product
     */
    public function getProduct()
    {
        return $this->product;
    }
}

