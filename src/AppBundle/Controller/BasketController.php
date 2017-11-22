<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class BasketController extends Controller
{
    
    /**
     * @Route("/api/basket")
     * @Method({"GET"})
     */
    public function getAllBaskets()
    {
        $em = $this->getDoctrine()->getManager(); 
        $baskets = $em->getRepository("AppBundle:Basket")->findAll();
        
        $response = new JsonResponse();
        $data = [];
        foreach ($baskets as $basket){
            
            $product_list = [];
            
            foreach ($basket->getProductList() as $product){  
               array_push($product_list, array(
                   "name" => $product->getName(),
                   "category" => $product->getCategory()->getName()
               ));
            }
            
            array_push($data, array(
                "name" => $basket->getName(),
                "price" => $basket->getPrice(),
                "category" => $basket->getCategory()->getName(),
                "category_image" => $product->getCategory()->getImagePath(),
                "products" => $product_list
            ));
            
      
        }
        $response->setData($data);
        return $response;
    }
    
}
