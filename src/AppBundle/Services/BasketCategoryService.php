<?php

class BasketService
{
    private $em;
            
    public function __construct(EntityManager $entityManager){
        $this->em = $entityManager;
    }
    
    public function deleteBasketCategory($id)
    {
        
        $basket_list = $this->em->getRepository("AppBundle:Basket")->findAll();
        foreach($basket_list as $basket){
            if($basket->getCategory()->getId() == $id){
                return "Deletion impossible, the basket category is used by a product";
            }
        }
        
        $basket_category = $this->em->getRepository("AppBundle:BasketCategory")->findOneById($id);    
        $this->em->remove($basket_category);          
        $this->em->flush();
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
                return "La catégorie existe déjà";
            } else {
			
                // Save the category image if it exists
                if(!is_null($basketCategory->getImagePath())) {

                    // Get the file
                    $file = $basketCategory->getImagePath();

                    // Generate a unique name for the file before saving it
                    $fileName = md5(uniqid()).'.'.$file->guessExtension();

                    // Move the file to the directory where images are stored
                    $file->move(
                            $this->getParameter('image_directory'),
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
    
    
}

