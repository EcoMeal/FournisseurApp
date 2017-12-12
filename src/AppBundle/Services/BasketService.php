<?php

namespace AppBundle\Services;

use Doctrine\ORM\EntityManager;

class BasketService {

    private $em;

    public function __construct(EntityManager $entityManager) {
        $this->em = $entityManager;
    }

    public function deleteBasket($id) {
        $basket = $this->em->getRepository("AppBundle:Basket")->findOneById($id);
        $this->em->remove($basket);
        $this->em->flush();
    }

    public function getAllBasketOrderedByName() {
        return $this->em->getRepository("AppBundle:Basket")->findBy([], ['name' => 'ASC']);
    }

    public function getAllBasketWithProductListOrderedByName() {
        return $this->em->getRepository("AppBundle:Basket")->getAllBasketWithProductList();
    }

    public function saveBasket($basket) {
        //On vérifie qu'il n'y a pas déjà un panier avec le même nom
        $basketWithSameName = $this->em->getRepository("AppBundle:Basket")->findOneByName($basket->getName());

        if (!is_null($basketWithSameName)) {
            return "Le panier existe déjà";
        } else {
            
            if(!is_null($basket->getImagePath())) {
                
                        // On enregistre le fichier
                        $file = $basket->getImagePath();

                        // Generate a unique name for the file before saving it
                        $fileName = md5(uniqid()).'.'.$file->guessExtension();

                        // Move the file to the directory where brochures are stored
                        $file->move(
                                $this->container->getParameter('image_directory'),
                                $fileName
                        );

                        $basket->setImagePath($fileName);
            }
            
            // On enregistre le panier
            $this->em->persist($basket);
            $this->em->flush();
        }
    }

}
