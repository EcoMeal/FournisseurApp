<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

use AppBundle\Entity\DeliveryPromise;
use AppBundle\Form\DeliveryPromiseType;
use AppBundle\Services\DeliveryPromiseService;

class DeliveryPromiseController extends Controller
{
    /**
     * @Route("delivery/list")
     */
    public function getAllDeliveryPromiseAction(DeliveryPromiseService $deliveryPromiseService)
    {
        
       // $deliveryList = array();//$deliveryPromiseService->getAllDeliveryPromiseOrderedByDate();    
        
        return $this->render('AppBundle:DeliveryPromise:get_all_delivery_promise.html.twig', array(
           
        ));
    }
    
    
     /**
     * @Route("delivery")
     */
    public function addDeliveryPromiseAction(Request $request)
    {
        $delivery = new DeliveryPromise();
    	$form = $this->createForm(DeliveryPromiseType::class, $delivery);
    	$form->handleRequest($request);
        
         if ($form->isValid()) {
             // save the delivery promise.
         }
        
         //rint_r($form);
         
        return $this->render('AppBundle:DeliveryPromise:add_delivery_promise.html.twig', 
        		array("form" => $form->createView())
        );
    }

}
