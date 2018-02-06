<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use AppBundle\Services\DeliveryService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use AppBundle\Services\BasketOrderService;
use DateTime;

class BasketOrderController extends Controller {

    
     /**
     * @Route("/orders/clean")
     * 
     * Deletes all the clients orders from the database. 
     * Used for functional testing only.
     * Should be removed or updated later as it is not secured.
     * 
     */
    public function cleanAllOrdersAction()
    {
        $em = $this->getDoctrine()->getManager(); 

        $order_list = $em->getRepository("AppBundle:BasketOrder")->findAll();
        
        for($i = 0; $i < count($order_list); $i++){
             $em->remove($order_list[$i]);
        }
           
        $em->flush();
        return $this->redirect('/orders');
    }
    
    
    /**
     * Returns a delivery time in the time slot encoded in JSON
     * 
     * QueryParameter(start_time)
     * QueryParameter(end_time)
     * @Route("/api/dtime_calculation")
     * @Method({"GET"})
     * 
     */
    public function returnDeliveryTime(Request $request, DeliveryService $deliveryService) {
        $start_time = new DateTime();
        $start_time->setTimestamp($request->query->get('start_time')); // Récupère la variable start_time en GET
        $end_time = new DateTime();
        $end_time->setTimestamp($request->query->get('end_time')); // Récupère la variable end_time en GET

        $delivery_time = $deliveryService->deliveryTimeCalculation($start_time, $end_time); // Calcule un horaire pour la commande compris dans la plage horaire donnée
        if ($delivery_time == null) {
            return new JsonResponse(array("deliveryTime" => 0));
        } else {
            return new JsonResponse(array("deliveryTime" => $delivery_time->getTimestamp()));
        }
    }

    /**
     * Saves an order in the database and returns it's id in JSON format.
     *
     * @Route("/api/basket_order")
     * @Method({"POST"})
     */
    public function saveBasketOrder(Request $request, BasketOrderService $basketOrderService) {

        // Retrieve the content of the request
        $content = $request->getContent();
        
        
        // Check if the content is not empty
        if (!is_null($content)) {
            $order = json_decode($content);
        	
        	//check if the JSON is well formed
            if (!is_null($order) && !empty($order)) {
            	
             	$order->content = str_replace("\"", "", $order->content);
                $order->content = str_replace("'", "", $order->content);
               	$order->content = json_decode($order->content);
            	
                $result = $basketOrderService->checkOrder($order);
                if ($result->valid) {
                	//save the order
                    $order_id = $basketOrderService->saveOrder($order);
                    return new JsonResponse(array("order_id" => $order_id), 200);
                } else {
                    return new JsonResponse($result, 400);
                }
            }
        }

        return new JsonResponse(null, 400);
    }

    /**
     * Show the orders of the Application with their content
     * @Route("/orders")
     * 
     */
    public function orders_display(Request $request, BasketOrderService $basketOrderService) {

        //Get the existing orders with their baskets
        $orders = $basketOrderService->getAllOrdersWithBasketListOrderedByTime();

        // Displays the orders with their baskets
        return $this->render('AppBundle:BasketOrder:view_order.html.twig', array(
                    "order_list" => $orders
        ));
    }

//     /**
//      * Show the order corresponding to the $id in GET with its content
//      * QueryParameter($id_order)
//      * @Route("/order")
//      * @Method({"GET"})
//      */
//     public function order_summary(Request $request, BasketOrderService $basketOrderService) {
//         $id = $request->query->get('id_order'); // Get the $id_order GET variable
//         //Get the order with its baskets
//         $order = $basketOrderService->getOrder($id);

//         var_dump($order);
//         // Display the order and its content
//         return $this->render('AppBundle:BasketOrder:recap_order.html.twig', array(
//                     "order" => $order
//         ));
//     }

}
