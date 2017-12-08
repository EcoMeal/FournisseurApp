<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Services\BasketService;
use AppBundle\Services\ProductService;

use AppBundle\Entity\Basket;
use AppBundle\Form\BasketType;

/* Services */
use AppBundle\Services\JsonFactory;

class BasketController extends Controller
{
    
    /**
     * @Route("/basket/clean")
     * 
     * Deletes all the basket categories from the database. 
     * Used for functional testing only.
     * Should be removed or updated later as it is not secured.
     * 
     */
    public function cleanAllBasketAction()
    {
        $em = $this->getDoctrine()->getManager(); 

        $basket_list = $em->getRepository("AppBundle:Basket")->findAll();
        
        for($i = 0; $i < count($basket_list); $i++){
             $em->remove($basket_list[$i]);
        }
           
        $em->flush();
        return $this->redirect('/basket');
    }
    
    
    /**
     * @Route("/basket/{id}", requirements={"id" = "\d+"})
     * @Method({"DELETE"})
     * Deletes the basket with the given id from the database.  
     */
    public function deleteBasketAction($id, BasketService $basketService)
    {
        $error = $basketService->deleteBasket($id);
        return $this->json(array('error' => $error));
    }
    
    /**
     * Returns all the baskets stored in the database as JSON.
     * 
     * @Route("/api/basket")
     * @Method({"GET"})
     */
    public function getAllBaskets(JsonFactory $jsonFactory) {
        $em = $this->getDoctrine()->getManager(); 
        $baskets = $em->getRepository("AppBundle:Basket")->findAll();
        return new JsonResponse($jsonFactory->getBaskets($baskets));
    }
    
    /**
     * @Route("/basket")
     * 
     */
    public function saveBasketAction(Request $request, BasketService $basketService,
            ProductService $productService) {
        
        $basket = new Basket();
        $form = $this->createForm(BasketType::class, $basket);
        $form->handleRequest($request);
              
        $error = null;
	
        if($form->isSubmitted() && $form->isValid()) {         
            $error = $basketService->saveBasket($basket);     
        }
        
        //Get the existing products
         $product_list = $productService->getAllProductOrderedByName();

        //Get the existing baskets
         $baskets = $basketService->getAllBasketWithProductListOrderedByName();
                 
        
        // Displays the basket
        return $this->render('AppBundle:Basket:add_basket.html.twig', array(
            "form" => $form->createView(),
            "basket_list" => $baskets, 
            "product_list" => $product_list,
            "error" => $error
        ));
        
        
         
    }
    
}
