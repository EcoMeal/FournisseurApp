<?php
namespace AppBundle\Services;

use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

class BasketCategoryService
{
    private $em;
    
    private $container;
            
    public function __construct(EntityManager $entityManager, ContainerInterface $serviceContainer){
        $this->em = $entityManager;
        $this->container = $serviceContainer;
    }
    
    public function deleteBasketCategory($id)
    {
        
        $basket_list = $this->em->getRepository("AppBundle:Basket")->findAll();
        foreach($basket_list as $basket){
            if($basket->getCategory()->getId() == $id){
                return array(
                		"type" => "ERROR",
                		"message" => "Suppression impossible, la catégorie de panier est utilisée"
                );
            }
        }
        
        $basket_category = $this->em->getRepository("AppBundle:BasketCategory")->findOneById($id);    
        $this->em->remove($basket_category);          
        $this->em->flush();
        
        return array(
        		"type" => "SUCCESS",
        		"message" => "Catégorie supprimée"
        );
        
    }
    
    public function getAllBasketCategoryOrderedByName()
    {
        return $this->em->getRepository("AppBundle:BasketCategory")->findBy([], ['name' => 'ASC']);
    }
    
    public function saveBasketCategory($basketCategory)
    {
        	
        // Checks if the category already exists
        $basketCategoryWithSameName = $this->em->getRepository("AppBundle:BasketCategory")
                ->findOneByName($basketCategory->getName());

        if(!is_null($basketCategoryWithSameName)) {
            return "Ajout impossible, la catégorie de panier existe déjà";
        } else {
			
            // Save the category image if it exists
            if(!is_null($basketCategory->getImagePath())) {

                // Get the file
                $file = $basketCategory->getImagePath();

                // Generate a unique name for the file before saving it
                $fileName = md5(uniqid()).'.'.$file->guessExtension();

                // Move the file to the directory where images are stored
                $file->move(
                        $this->container->getParameter('image_directory'),
                        $fileName
                );

                // Update imagePath in the category entity
                $basketCategory->setImagePath($fileName);

            }

            // Save the category in database
            $this->em->persist($basketCategory);
            $this->em->flush();
    	}
    	
    }
    
     /**
     * Update an already existing category in the application.
     * 
     * @param $category the category to update
     * 
     * @return an error if the update failed, null otherwise
     */
    public function updateCategory($category)
    {
    	// Checks if the category already exists
       	$categoryWithSameName = $this->em->getRepository("AppBundle:BasketCategory")->findOneByName($category->getName());

       	// If it does, returns an error
        if(!is_null($categoryWithSameName)) {
        	return "Une catégorie utilise déjà ce nom.";
        } else {
            // Save the category in database
            $this->em->persist($category);
            $this->em->flush();     
        }
    }
    
     
    public function getCategory($id)
    {
        return $this->em->getRepository("AppBundle:BasketCategory")->findOneById($id);
    }
    
}

