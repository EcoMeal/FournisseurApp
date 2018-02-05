<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

use AppBundle\Services\DeliveryPromiseService;
use AppBundle\Services\ProductService;
use AppBundle\Services\StockService;

class DeliveryPromiseController extends Controller
{

    
    /**
     * @Route("confirm-delivery")
     */
    public function getAllDeliveryPromiseAction(DeliveryPromiseService $deliveryPromiseService, StockService $stockService, Request $request) {
        $error = NULL;
        	
		$rawData = $request->getContent();		
        
		if(!empty($rawData)) {
                
            $deliveryPromise = json_decode($rawData);
       
            if($deliveryPromise != null){    
                $error = $deliveryPromiseService->updateDeliveryPromise($stockService, $deliveryPromise);         
            } else {
                $error = "Erreur sur le format JSON.";
            }         
        }
        
        $delivery_promise_list = $deliveryPromiseService->getAllDeliveryPromiseOrderedByDate();
                
        return $this->render('AppBundle:DeliveryPromise:get_all_delivery_promise.html.twig', array(
           "delivery_list" => $delivery_promise_list,
           "error" => $error,
        ));
    }
    
   
    /**
     * @Route("delivery")
     */
    public function addDeliveryPromiseAction(DeliveryPromiseService $deliveryPromiseService, ProductService $productService, Request $request) {

		$error = NULL;
		
	    // The delivery promise format (product_id=quantityPromise&product_id2=quantityPromise2).	
		$rawData = $request->getContent();
		
		$user = $this->container->get('security.token_storage')->getToken()->getUser();
		
		$em = $this->getDoctrine()->getManager();
		$company = NULL;
		if($user->hasRole("ROLE_ADMIN")) {
			$company_id = $request->query->get('company');
			$company = $em->getRepository("UserBundle:Company")->find($company_id);
		} else {
			$company = $em->getRepository("UserBundle:Company")->findOneBy(array("user" => $user));
		}
		
		if(!empty($rawData)) {
	                
	        // Multiples promises.
			if(strstr($rawData, "&")) {
				$deliveryPromiseItemList = explode("&", $rawData);
			} 
            // Only one promise.
            else {
				$deliveryPromiseItemList = array($rawData);
			}

            $error = $deliveryPromiseService->createDeliveryPromise($company, $deliveryPromiseItemList);
        }
        
        if(!is_null($company)) {
	        $product_list = $productService->getAllProductByCompany($company);
        }
		
        $delivery_promise = $deliveryPromiseService->getDeliveryPromiseFor($company);
        $companies = $em->getRepository("UserBundle:Company")->findAll();
        
        return $this->render('AppBundle:DeliveryPromise:add_delivery_promise.html.twig', array(
            "companies" => $companies,
           "product_list" => $product_list,
            "error" => $error,
        	"delivery_promise" => $delivery_promise,        		
        ));
         
    }

}
