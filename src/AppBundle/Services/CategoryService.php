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
    
    /**
     * Deletes a product category if it is unused.
     * 
     * @param $id the id of the category to delete
     * 
     * @return $error null if the deletion is successfull, an error message otherwise
     */
    public function deleteCategory($id)
    {
      
        $product_list = $this->em->getRepository("AppBundle:Product")->findAll();
        
        // First checks if there is an existing product using the category
        foreach($product_list as $product){
            if($product->getCategory()->getId() == $id){
            	//If there is at least one, return an error message
                return "Deletion impossible, the category is used by a product";
            }
        }
        
        // If not, deletes the category and returns null
        $category = $this->em->getRepository("AppBundle:Category")->findOneById($id);    
        $this->em->remove($category);          
        $this->em->flush();
    }
    
	/**
	 * Removes all the categories in the application.
	 */    
    public function cleanAllCategory()
    {
        $category_list = $this->em->getRepository("AppBundle:Category")->findAll();
        
        for($i = 0; $i < count($category_list); $i++){
             $this->em->remove($category_list[$i]);
        }
           
        $this->em->flush();
    }
    
    /**
     * Returns all the categories of the application, ordered by ascending name
     */
    public function getAllCategoriesOrdererByName()
    {
        return $this->em->getRepository("AppBundle:Category")->findBy([], ['name' => 'ASC']);
    }
    
    /**
     * Saves a category in the application.
     * 
     * @param $category the category to save
     * 
     * @return an error if the save failed, null otherwise
     */
    public function saveCategory($category)
    {
    	// Checks if the category already exists
       	$categoryWithSameName = $this->em->getRepository("AppBundle:Category")->findOneByName($category->getName());

       	// If so, returns an error
        if(!is_null($categoryWithSameName)) {
        	return $error = "La catégorie existe déjà";
        } else {
			
        	// Save the image in the upload folder
            $this->registerCategoryImage($category);
			// Save the category in database
            $this->em->persist($category);
            $this->em->flush();
                
		}
    }
    
    public function registerCategoryImage($category) {
    	
    	// Get the file
    	$file = $category->getImagePath();
    	
    	// Save the category image if it exists
    	if(!is_null($file)) {
    	
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
    }
    
}
