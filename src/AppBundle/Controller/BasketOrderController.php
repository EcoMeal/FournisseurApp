<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class BasketOrderController extends Controller
{
    private $em ;
    private $basketOrderRepository;
    private $time_interval;
    
    function __construct($em = NULL, $basketOrderRepository = NULL) {
         $this->$time_interval = new DateInterval('P2M');
         if (!is_null($em)){
            $this->$em = $em;
         }else{
            $this->$em = $this->getDoctrine()->getManager();
         }
         if (!is_null($basketOrderRepository)){
            $this->$basketOrderRepository = $basketOrderRepository;
         }else{
            $this->$basketOrderRepository = $em->getRepository("AppBundle:BasketOrder");
         }
         
    }
    
     /**
     * Returns a delivery time in the time slot 
     * 
     * @Route("/api/deliveryTime_calculation")
     * @Method({"POST"})
     */
    public function returnDeliveryTime(Request $request) {
        $start = $request->request->get('start_time');
        $end = $request->request->get('end_time');
        $delivery_time = $this->deliveryTimeCalculation($start,$end);
        return $delivery_time;
    }
    
    /**
     * Calculate a free delivery time between $start and $end
     * @param dateTime $start
     * @param dateTime $end
     * @return dateTime the free delivery time or NULL if no free delivery time is available
     */
    public function deliveryTimeCalculation($start,$end){
        $taken_delivery_times = $basketOrderRepository->getOrdersBetween($start,$end); // The delivery_times already taken by other customers
        $delivery_time = $start;
        while ($delivery_time <= $end) {
            // The $delivery_time isn't contained in $taken_delivery_time
            if (!in_array($delivery_time,$taken_delivery_times)){
                return delivery_time;
            }else{
                $delivery_time->add($this->$time_interval);
            }
        }
        return NULL;
    }
}
