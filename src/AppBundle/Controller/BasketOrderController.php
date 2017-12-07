<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use AppBundle\Services\DeliveryService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

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
        $start = $request->query->get('start_time');
        $start_time = $date = new DateTime();
        $start_time->setTimestamp($start);
        $end = $request->query->get('end_time');
        $end_time = $date = new DateTime();
        $end_time->setTimestamp($end);
        $delivery_time = $deliveryService->deliveryTimeCalculation($start_time, $end_time);
        if ($delivery_time == null) {
            return new JsonResponse(NULL);
        } else {
            return new JsonResponse($delivery_time->getTimestamp());
        }
    }

}
