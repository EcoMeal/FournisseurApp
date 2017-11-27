<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;

use AppBundle\Entity\Basket;
use AppBundle\Form\BasketType;

class BasketController extends Controller
{
    
    /**
     * Returns all the baskets stored in the database as json.
     * 
     * @Route("/api/basket")
     * @Method({"GET"})
     */
    public function getAllBaskets() {
        $em = $this->getDoctrine()->getManager(); 
        $baskets = $em->getRepository("AppBundle:Basket")->findAll();
        return new JsonResponse($this->getBasketsAsJson($baskets));
    }
    
    /**
     * Transforms a list of baskets to json format.
     * 
     * @param $baskets the list of baskets
     */
    private function getBasketsAsJson($baskets) {
        
        //An array which stores all the baskets
        $data = [];
        
        foreach ($baskets as $basket){
            
            $product_list = [];
            
            //List of products in the basket
            foreach ($basket->getProductList() as $product){  
               array_push($product_list, array(
                   "name" => $product->getName(),
                   "category" => $product->getCategory()->getName()
               ));
            }
            
            //General informations of the basket
            array_push($data, array(
                "name" => $basket->getName(),
                "price" => $basket->getPrice(),
                "category" => $basket->getCategory()->getName(),
                "category_image" => $product->getCategory()->getImagePath(),
                "products" => $product_list
            ));
      
        }
    }
    
    /**
     * @Route("/basket")
     */
    public function basketAction(Request $request) {
        
        $basket = new Basket();
        $form = $this->createForm(BasketType::class, $basket);
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()) {
            
            
            
        } else {
             // Displays the basket
            return $this->render('AppBundle:Basket:add_basket.html.twig', array(
                "form" => $form->createView()
            ));
        }
         
    }
    
}
