<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DeliveryPromise
 *
 * @ORM\Table(name="delivery_promise")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\DeliveryPromiseRepository")
 */
class DeliveryPromise
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
     * @ORM\Column(name="deliveryDate", type="datetime")
     */
    private $deliveryDate;

    /**
     * Contains the content of the promise order (An array of StockPromise)
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\StockPromise", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $deliveryContent;
    
    /**
     * @ORM\OneToOne(targetEntity="UserBundle\Entity\Company")
     * @ORM\JoinColumn(nullable=false)
     */
    private $company;

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
     * Set deliveryDate
     *
     * @param \DateTime $deliveryDate
     *
     * @return DeliveryPromise
     */
    public function setDeliveryDate($deliveryDate)
    {
        $this->deliveryDate = $deliveryDate;

        return $this;
    }

    /**
     * Get deliveryDate
     *
     * @return \DateTime
     */
    public function getDeliveryDate()
    {
        return $this->deliveryDate;
    }
    
    /**
     * Set the delivery content
     *
     * @param Array<StockPromise>
     *
     * @return DeliveryPromise
     */
    public function setDeliveryContent($deliveryContent)
    {
        $this->deliveryContent = $deliveryContent;

        return $this;
    }
    
    /**
     * Get the delivery content
     *
     * @return an array of StockPromise.
     */
    public function getDeliveryContent()
    {
        return $this->deliveryContent;
    }
    
    public function getCompany() {
    	return $this->company;
    }
    
    public function setCompany($company) {
    	$this->company = $company;
    	return $this;
    }
}

