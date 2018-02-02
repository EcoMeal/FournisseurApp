<?php
namespace AppBundle\Services;

use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
//use DateTime;

class DeliveryPromiseService
{
    private $em;
    
    private $container;
            
    public function __construct(EntityManager $entityManager, ContainerInterface $serviceContainer){
        $this->em = $entityManager;
        $this->container = $serviceContainer;
    }

    
    public function getAllDeliveryPromiseOrderedByDate()
    {
        return $this->em->getRepository("AppBundle:DeliveryPromise")->findBy([], ['deliveryDate' => 'ASC']);
    }
 
}