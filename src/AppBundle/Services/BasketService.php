<?php

class BasketService
{
    private $em;
            
    public function __construct(EntityManager $entityManager){
        $this->em = $entityManager;
    }
    
    public function deleteBasket($id)
    {
        $this->em = $this->getDoctrine()->getManager(); 

        $basket = $this->em->getRepository("AppBundle:Basket")->findOneById($id);    
        $this->em->remove($basket);          
        $this->em->flush();
    }
    
    public function getAllBasketOrderedByName()
    {
        return $this->em->getRepository("AppBundle:Basket")->findBy([], ['name' => 'ASC']);
    }
    
    public function saveBasket($basket)
    { 
        //On vérifie qu'il n'y a pas déjà un panier avec le même nom
        $basketWithSameName = $this->em->getRepository("AppBundle:Basket")->findOneByName($basket->getName());
            
        if(!is_null($basketWithSameName)) {
            return "Le panier existe déjà";
        } else {
            // On enregistre le panier
            $this->em->persist($basket);
            $this->em->flush();
        }
    }
  
    
    
}