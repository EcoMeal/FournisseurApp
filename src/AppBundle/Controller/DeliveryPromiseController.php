<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

use AppBundle\Services\DeliveryPromiseService;
use AppBundle\Services\ProductService;
use AppBundle\Services\StockService;
use Symfony\Component\HttpFoundation\JsonResponse;

class DeliveryPromiseController extends Controller
{

    
    /**
     * @Route("confirm-delivery")
     */
    public function getAllDeliveryPromiseAction(DeliveryPromiseService $deliveryPromiseService, StockService $stockService, Request $request) {
        $error = NULL;
        	
		$deliveryPromiseID = $request->getContent();		
        
		if(!empty($deliveryPromiseID)) {
            $error = $deliveryPromiseService->confirmDeliveryPromise($stockService, $deliveryPromiseID);              
        } else {
            $error = "Aucune donnée reçue.";
        }
        
        if($error != NULL){
            return new JsonResponse(array("error" => $error));
        } else {
            return new JsonResponse(array("success" => "Le bon de commande à été validé."));
        }

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
           if(!is_null($company_id)){
              $company = $em->getRepository("UserBundle:Company")->findOneById($company_id);
           }
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
            
            if($error != NULL){
            	return new JsonResponse(array("error" => $error));
            } else {
            	return new JsonResponse(array("success" => "Le bon de commande à été crée."));
            }
            
        }

        if(!is_null($company)) {
	        $product_list = $productService->getAllProductByCompany($company);
            $delivery_promise = $deliveryPromiseService->getDeliveryPromiseFor($company);
        } else {
            $product_list = NULL;
            $delivery_promise = NULL;
        }
        
        $companies = $em->getRepository("UserBundle:Company")->findAll();
        
        return $this->render('AppBundle:DeliveryPromise:add_delivery_promise.html.twig', array(
            "companies" => $companies,
            "product_list" => $product_list,
            "error" => $error,
        	"delivery_promise" => $delivery_promise,        		
        ));
         
    }

}
