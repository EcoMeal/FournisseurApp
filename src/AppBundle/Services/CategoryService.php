<?php

namespace AppBundle\Services;

use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

class CategoryService
{
    private $em;
    
    private $container;
            
    public function __construct(EntityManager $entityManager, ContainerInterface $serviceContainer){
        $this->em = $entityManager;
        $this->container = $serviceContainer;
    }
    
    public function deleteCategory($id)
    {
      
        $product_list = $this->em->getRepository("AppBundle:Product")->findAll();
        foreach($product_list as $product){
            if($product->getCategory()->getId() == $id){
                return "Deletion impossible, the category is affected to a product";
            }
        }
        
        $category = $this->em->getRepository("AppBundle:Category")->findOneById($id);    
        $this->em->remove($category);          
        $this->em->flush();
    }
    
     public function cleanAllCategory()
    {
        $category_list = $this->em->getRepository("AppBundle:Category")->findAll();
        
        for($i = 0; $i < count($category_list); $i++){
             $this->em->remove($category_list[$i]);
        }
           
        $this->em->flush();
    }
    
    public function getAllCategoriesOrdererByName()
    {
        return $this->em->getRepository("AppBundle:Category")->findBy([], ['name' => 'ASC']);
        
    }
    
    public function saveCategory($category)
    {
            // Checks if the category already exists
            $categoryWithSameName = $this->em->getRepository("AppBundle:Category")->findOneByName($category->getName());

            if(!is_null($categoryWithSameName)) {
                return $error = "La catégorie existe déjà";
            } else {
			
                // Save the category image if it exists
                if(!is_null($category->getImagePath())) {

                    // Get the file
                    $file = $category->getImagePath();

                    // Generate a unique name for the file before saving it
                    $fileName = md5(uniqid()).'.'.$file->guessExtension();

                    // Move the file to the directory where images are stored
                    $file->move(
                            $this->container->getParameter('image_directory'),
                            $fileName
                    );

                    // Update imagePath in the category entity
                    $category->setImagePath($fileName);

                }

                // Save the category in database
                $this->em->persist($category);
                $this->em->flush();
            }
    }
    
    
    
    
}

