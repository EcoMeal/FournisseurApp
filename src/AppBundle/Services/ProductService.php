<?php

class CategoryService
{
    private $em;
            
    public function __construct(EntityManager $entityManager){
        $this->em = $entityManager;
    }
    
  
    public function deleteProduct($id)
    {
       
        $basket_list = $this->em->getRepository("AppBundle:Basket")->findAll();
        foreach($basket_list->getProductList() as $product){
            if($product->getId() == $id){
                return "Deletion impossible, the product is used by a basket";
            }
        }
       
        $product = $this->em->getRepository("AppBundle:Product")->findOneById($id);    
        $this->em->remove($product);          
        $this->em->flush();
    }
    
    public function getAllProductOrderedByName()
    {
        return $this->em->getRepository("AppBundle:Product")->findBy([], ['name' => 'ASC']);
    }
    
    public function saveProduct($product){
         
            // Checks if the category already exists
            $productWithSameName = $this->em->getRepository("AppBundle:Product")->findOneByName($product->getName());

            if(!is_null($productWithSameName)) {
                return "Le produit existe déjà";
            } else {
                
                 if(!is_null($product->getImagePath())) {
                
                        // On enregistre le fichier
                        $file = $product->getImagePath();

                        // Generate a unique name for the file before saving it
                        $fileName = md5(uniqid()).'.'.$file->guessExtension();

                        // Move the file to the directory where brochures are stored
                        $file->move(
                                $this->getParameter('image_directory'),
                                $fileName
                        );

                        $product->setImagePath($fileName);

                }

                // On enregistre le produit
                $this->em->persist($product);
                $this->em->flush();
            }
    }
    
    
}