<?php
namespace AppBundle\Services;

use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use AppBundle\Entity\DeliveryPromise;
use AppBundle\Entity\StockPromise;
use AppBundle\Services\StockService;
use DateTime;

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
    
    
    public function updateDeliveryPromise(StockService $stockService, $deliveryPromiseJson)
    {
        $error = NULL;
        
        $deliveryPromise = $this->em->getRepository("AppBundle:DeliveryPromise")->findOneById($deliveryPromiseJson->id);
        
        if($deliveryPromise == null){
            return "Erreur: le bon de livraison n'existe pas.";
        }
        
        $confirmedStockPromiseList = $deliveryPromiseJson->confirmed;
        
        $size = count($confirmedStockPromiseList);
        for($i = 0; $i < $size; $i ++) {
            
            $stockPromiseID = $confirmedStockPromiseList[$i];
            $stockPromise = $this->em->getRepository("AppBundle:StockPromise")->findOneById($stockPromiseID);
           
            
            // The stock promise exist and is confirmed.
            if($stockPromise != null && $this->deliveryPromiseContains($deliveryPromise, $stockPromise)){
                $error .= $stockService->updateProductStock($stockPromise->getProduct()->getId(), $stockPromise->getQuantity());
                
            } else {
                $error .= "La promesse de stock n".$confirmedStockPromiseList[$i]." n'existe pas pour le bon"
                        ." de commande n".$deliveryPromise->getId()." et n'a pas été traité.";
            }
        }
        // Remove the delivery promise.
        $this->em->remove($deliveryPromise);          
        $this->em->flush();
        
        return $error;
    }
 
    public function deliveryPromiseContains($deliveryPromise, $stockPromise){
        
        $deliveryContent = $deliveryPromise->getDeliveryContent();
        
        $size = count($deliveryContent);
        for($i = 0; $i < $size; $i ++) {          
           if($deliveryContent[$i] == $stockPromise){
                return true;
           }   
        }
        return false;
    }
    
    
    public function createDeliveryPromise($itemUpdateList)
    {
        $deliveryPromise = new DeliveryPromise();       
        $deliveryContent = array();
       
        $size = count($itemUpdateList);
        for($i = 0; $i < $size; $i ++) {
				
            $data = explode("=", $itemUpdateList[$i]);
            $productID = $data[0];
            $promiseQuantity = $data[1];
	
            $stockPromise = new StockPromise();
            $product = $this->em->getRepository("AppBundle:Product")->findOneById($productID);
            
            if($product == null){
                return "Erreur le produit avec l'id ".$productID." n'existe pas. Le bon de livraison à été annulé.";
            }
            if($promiseQuantity <= 0){
                return "Erreur la quantité d'un produit à livrer ne peut pas être inférieure ou égale à 0.";
            }
            $stockPromise->setProduct($product);
            $stockPromise->setQuantity($promiseQuantity);
            
            // Add the stock promise to the delivery content.
            array_push($deliveryContent, $stockPromise);           	
        }
        
        $deliveryPromise->setDeliveryContent($deliveryContent);
        $dateTime = new DateTime();
        $dateTime->setTimestamp(time());
        $deliveryPromise->setDeliveryDate($dateTime);
        
        $this->em->persist($deliveryPromise);
        $this->em->flush();     
    }
    
}