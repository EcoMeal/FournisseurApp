<?php
namespace AppBundle\Services;

use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use AppBundle\Entity\Stock;

class ProductService
{
    private $em;
    
    private $container;
            
    public function __construct(EntityManager $entityManager, ContainerInterface $serviceContainer){
        $this->em = $entityManager;
        $this->container = $serviceContainer;
    }
    
    public function deleteProduct($id)
    {
        
        $basket_list = $this->em->getRepository("AppBundle:Basket")->findAll();
        foreach($basket_list as $basket){
            foreach($basket->getProductList() as $product){
                if($product->getId() == $id){
                    return "Deletion impossible, the product is used in a basket";
                }
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
                                $this->container->getParameter('image_directory'),
                                $fileName
                        );

                        $product->setImagePath($fileName);

                }

                // On enregistre le produit
                $this->em->persist($product);
                $this->em->flush();
                
                // Not working yet...
                
               /* $product2 = $this->em->getRepository("AppBundle:Product")->findOneById($product->getId());
                        
                echo "Product field = ".$product2->getId();
                // On enregistre un stock de base pour ce produit.
                $stock = new Stock();
                $stock->setProduct($product2);
                $stock->setQuantity(0);
                $stock->setDate(date("Y-m-d H:i:s"));
                $this->em->persist($stock);
                $this->em->flush();*/
            }
    }
    
    
}