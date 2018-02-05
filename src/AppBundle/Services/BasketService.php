<?php

namespace AppBundle\Services;

use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

class BasketService {

    private $em;
    private $container;

    public function __construct(EntityManager $entityManager, ContainerInterface $serviceContainer) {
        $this->em = $entityManager;
        $this->container = $serviceContainer;
    }

    public function deleteBasket($id) {

        //First, we check if there is a basketOrder using the basket
        $orders = $this->em->getRepository("AppBundle:BasketOrder")->getAllOrdersWithBasketId($id);

        if (!is_null($orders) && !empty($orders)) {
            return "Ce panier a été commandé. Veuillez attendre la fin de la commande avant de le supprimer";
        }

        $basket = $this->em->getRepository("AppBundle:Basket")->findOneById($id);

        $basket->setProductList(array());
        $this->em->persist($basket);
        $this->em->flush();
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

            if (!is_null($basket->getImagePath())) {

                // On enregistre le fichier
                $file = $basket->getImagePath();

                // Generate a unique name for the file before saving it
                $fileName = md5(uniqid()) . '.' . $file->guessExtension();

                // Move the file to the directory where brochures are stored
                $file->move(
                        $this->container->getParameter('image_directory'), $fileName
                );

                $basket->setImagePath($fileName);
            }

            // On enregistre le panier
            $this->em->persist($basket);
            $this->em->flush();
        }
    }

    /**
     * Update an already existing basket in the application.
     * 
     * @param $basket the basket to update
     * 
     * @return an error if the update failed, null otherwise
     */
    public function updateBasket($basket) {
        // Checks if the basket already exists
        $basketWithSameName = $this->em->getRepository("AppBundle:Basket")->findOneByName($basket->getName());

        // If it does, returns an error
        if (!is_null($basketWithSameName)) {
            return "Un panier utilise déjà ce nom.";
        } else {
            // Save the basket in database
            $this->em->persist($basket);
            $this->em->flush();
        }
    }

    public function getBasket($id) {
        return $this->em->getRepository("AppBundle:Basket")->findOneById($id);
    }

}
