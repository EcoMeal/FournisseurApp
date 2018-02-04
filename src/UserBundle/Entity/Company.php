<?php

namespace UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Company
 *
 * @ORM\Table(name="company")
 * @ORM\Entity(repositoryClass="UserBundle\Repository\CompanyRepository")
 */
class Company
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
     * @ORM\Column(name="address", type="string", length=255)
     */
    private $address;

    /**
     * @var string
     *
     * @ORM\Column(name="companyname", type="string", length=255, unique=true)
     */
    private $companyname;

    /**
     * @var int
     *
     * @ORM\Column(name="siret", type="bigint", unique=true)
     */
    private $siret;

    /**
     * @var string
     *
     * @ORM\Column(name="phone", type="string", length=10, nullable=true)
     */
    private $phone;

    /**
     * @ORM\OneToOne(targetEntity="UserBundle\Entity\User", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;
    
    /**
	 * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Category")
	 * @ORM\JoinTable(name="company_category",
	 *      joinColumns={@ORM\JoinColumn(name="company_id", referencedColumnName="id")},
	 *      inverseJoinColumns={@ORM\JoinColumn(name="category_id", referencedColumnName="id", unique=true)}
	 * )
	 */
    private $categories;

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
     * Set address
     *
     * @param string $address
     *
     * @return Company
     */
    public function setAddress($address)
    {
        $this->address = $address;
    
        return $this;
    }

    /**
     * Get address
     *
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set companyname
     *
     * @param string $companyname
     *
     * @return Company
     */
    public function setCompanyname($companyname)
    {
        $this->companyname = $companyname;
    
        return $this;
    }

    /**
     * Get companyname
     *
     * @return string
     */
    public function getCompanyname()
    {
        return $this->companyname;
    }

    /**
     * Set siret
     *
     * @param integer $siret
     *
     * @return Company
     */
    public function setSiret($siret)
    {
        $this->siret = $siret;
    
        return $this;
    }

    /**
     * Get siret
     *
     * @return integer
     */
    public function getSiret()
    {
        return $this->siret;
    }

    /**
     * Set phone
     *
     * @param integer $phone
     *
     * @return Company
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
    
        return $this;
    }

    /**
     * Get phone
     *
     * @return integer
     */
    public function getPhone()
    {
        return $this->phone;
    }
    
    /**
     * Set user
     *
     * @param string $user
     *
     * @return Company
     */
    public function setUser($user)
    {
    	$this->user = $user;
    
    	return $this;
    }
    
    /**
     * Get user
     *
     * @return User
     */
    public function getUser()
    {
    	return $this->user;
    }
    
    public function setCategories($categories) {
    	$this->categories = $categories;
    	return $this;
    }
    
    public function getCategories() {
    	return $this->categories;
    }
}

