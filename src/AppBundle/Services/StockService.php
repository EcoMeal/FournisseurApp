<?php
namespace AppBundle\Services;

use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use AppBundle\Entity\Stock;
use DateTime;

class StockService
{
    private $em;
    private $container;
            
    public function __construct(EntityManager $entityManager, ContainerInterface $serviceContainer){
        $this->em = $entityManager;
        $this->container = $serviceContainer;
    }
 
    public function updateProductStock($productID, $newStock)
    {
        // Add a more recent stock for this product.
        $stock = new Stock();
        
        $product = $this->em->getRepository("AppBundle:Product")->findOneById($productID);
        $stock->setProduct($product);
        $stock->setQuantity($newStock);        
        $dateTime = new DateTime();
        $dateTime->setTimestamp(time());
        $stock->setDate($dateTime);
                
        $this->em->perist($stock);
        $this->em->flush();
    }
    
}