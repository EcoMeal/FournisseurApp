<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;

use AppBundle\Entity\Basket;
use AppBundle\Form\BasketType;

/* Services */
use AppBundle\Services\JsonFactory;

class BasketController extends Controller
{
    
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
     */
    public function basketAction(Request $request) {
        
        $basket = new Basket();
        $form = $this->createForm(BasketType::class, $basket);
        $form->handleRequest($request);
        
        //Doctrine manager
	$em = $this->getDoctrine()->getManager();
        
        $error = null;
	
        if($form->isSubmitted() && $form->isValid()) {
            
            //On vérifie qu'il n'y a pas déjà un panier avec le même nom
            $basketWithSameName = $em->getRepository("AppBundle:Basket")->findOneByName($basket->getName());
            
            if(!is_null($basketWithSameName)) {
                $error = "Le panier existe déjà";
            } else {
                // On enregistre le panier
                $em->persist($basket);
                $em->flush();
            }
            
        }
        
        //Get the existing products
         $product_list = $em->getRepository("AppBundle:Product")->findBy([], ['name' => 'ASC']);

        //Get the existing baskets
         $baskets = $em->getRepository("AppBundle:Basket")->findBy([], ['name' => 'ASC']);
        
        // Displays the basket
        return $this->render('AppBundle:Basket:add_basket.html.twig', array(
            "form" => $form->createView(),
            "basket_list" => $baskets, 
            "product_list" => $product_list,
            "error" => $error
        ));
        
        
         
    }
    
}
