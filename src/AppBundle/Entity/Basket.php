<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Basket
 *
 * @ORM\Table(name="basket")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\BasketRepository")
 */
class Basket
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
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, unique=true)
     */
    private $name;

    /**
     * @var float
     *
     * @ORM\Column(name="price", type="float")
     */
    private $price;
    
     /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\BasketCategory", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $category;
    
    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Product", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $product_list;

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
     * Set name
     *
     * @param string $name
     *
     * @return Basket
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set price
     *
     * @param float $price
     *
     * @return Basket
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return float
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set category
     *
     * @param category
     *
     * @return Basket
     */
    public function setCategory($category)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return BasketCategory
     */
    public function getCategory()
    {
        return $this->category;
    }
    
    /**
     * Set product list
     * 
     * @param product_list
     * 
     * @return Basket
     */
    public function setProductList($product_list) {
        $this->product_list = $product_list;
        return $this;
    }
    
    /**
     * Get product list for this basket
     * 
     * @return an array of Product
     */
    public function getProductList() {
        return $this->product_list;
    }
}

