<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use AppBundle\Services\DeliveryService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use DateTime;

class BasketOrderController extends Controller {

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
            return new JsonResponse(NULL);
        } else {
            return new JsonResponse(array("deliveryTime" => $delivery_time->getTimestamp()));
        }
    }

}
