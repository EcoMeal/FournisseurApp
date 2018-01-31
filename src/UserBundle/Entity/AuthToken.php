<?php

namespace UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AuthToken
 *
 * @ORM\Table(name="auth_token")
 * @ORM\Entity(repositoryClass="UserBundle\Repository\AuthTokenRepository")
 */
class AuthToken
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
     * @ORM\Column(name="token", type="string", length=255, unique=true)
     */
    private $token;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="creationTime", type="datetimetz")
     */
    private $creationTime;
    
    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @var User
     */
    private $user;


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
     * Set token
     *
     * @param string $token
     *
     * @return AuthToken
     */
    public function setToken($token)
    {
        $this->token = $token;
    
        return $this;
    }

    /**
     * Get token
     *
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Set creationTime
     *
     * @param \DateTime $creationTime
     *
     * @return AuthToken
     */
    public function setCreationTime($creationTime)
    {
        $this->creationTime = $creationTime;
    
        return $this;
    }

    /**
     * Get creationTime
     *
     * @return \DateTime
     */
    public function getCreationTime()
    {
        return $this->creationTime;
    }
    
    /**
     * Get User
     * 
     * @return User
     */
    public function getUser()
    {
    	return $this->user;
    }
    
    /**
     * Sets the user.
     * 
     * @param User $user
     * 
     * @return \UserBundle\Entity\AuthToken
     */
    public function setUser(User $user)
    {
    	$this->user = $user;
    	
    	return $this;
    }
    
}

