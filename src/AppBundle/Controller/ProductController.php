<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use AppBundle\Entity\Product;
use AppBundle\Form\ProductType;

class ProductController extends Controller
{
	



    /**
     * @Route("/product/clean")
     */
    public function cleanAllProductAction()
    {
        $em = $this->getDoctrine()->getManager(); 
        $product_list = $em->getRepository("AppBundle:Product")->findAll();
        
        for($i = 0; $i < count($product_list); $i++){
             $em->remove($product_list[$i]);
        }
           
        $em->flush();
        return $this->redirect('/product');
    }
    
    /**
     * @Route("/product/delete/{id}", requirements={"id" = "\d+"})
     * 
     * Deletes the product with the given id from the database.  
     */
    public function deleteProductAction($id)
    {
        $em = $this->getDoctrine()->getManager(); 

        $product = $em->getRepository("AppBundle:Product")->findOneById($id);    
        $em->remove($product);          
        $em->flush();
        return $this->redirect('/product');
    }


    /**
     * @Route("/product")
     */
    public function saveProductAction(Request $request)
    {
		
	$product = new Product();
	$form = $this->createForm(ProductType::class, $product);
	$form->handleRequest($request);
		
	//Doctrine manager
	$doct = $this->getDoctrine()->getManager();
		
	//Error
	$error = null;

	//En cas de formulaire valide
        if ($form->isValid()) {
            
            // Checks if the category already exists
            $productWithSameName = $doct->getRepository("AppBundle:Product")->findOneByName($product->getName());

            if(!is_null($productWithSameName)) {
                $error = "Le produit existe déjà";
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
                $doct->persist($product);
                $doct->flush();
                
                
            }
		
	}
        $product_list = $doct->getRepository("AppBundle:Product")->findBy([], ['name' => 'ASC']);
			
	return $this->render('AppBundle:Product:add_product.html.twig',
                array("form" => $form->createView(), "product_list" => $product_list,
                    "error" => $error
	));
		
    }

}
