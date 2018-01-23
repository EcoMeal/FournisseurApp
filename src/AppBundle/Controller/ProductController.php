<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use AppBundle\Entity\Product;
use AppBundle\Form\ProductType;
use AppBundle\Services\ProductService;

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
     * @Route("/product/{id}", requirements={"id" = "\d+"})
     * @Method({"DELETE"})
     * Deletes the product with the given id from the database.  
     */
    public function deleteProductAction($id, ProductService $productService)
    {
        $error = $productService->deleteProduct($id);
        return $this->json(array('error' => $error));
    }


    /**
     * @Route("/product")
     */
    public function saveProductAction(Request $request, ProductService $productService)
    {
		
		$product = new Product();
		$form = $this->createForm(ProductType::class, $product);
		$form->handleRequest($request);
	
		//Error
		$error = null;

		//En cas de formulaire valide
	    if ($form->isValid()) {
	    	$error = $productService->saveProduct($product);
		}
        $product_list =  $productService->getAllProductOrderedByName();               
			
		return $this->render('AppBundle:Product:add_product.html.twig',
                array("form" => $form->createView(), "product_list" => $product_list,
                    "error" => $error,
                	"success" => ""
		));
		
    }

}
