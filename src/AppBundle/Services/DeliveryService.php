<?php

namespace AppBundle\Services;

use Doctrine\ORM\EntityManager;
use DateInterval;

/**
 * Control every time relative method/attribute
 */
class DeliveryService {

    private $time_interval; // Représente l'intervalle de temps entre deux commandes
    private $em;
    private $basketOrderRepository;

    function __construct(EntityManager $entityManager) { // L'entityManager peut être définit manuellement (dans le but de le mocker) ou automatiquement
        $this->time_interval = new DateInterval('PT2M'); // PT.<NbMinute>.M
        $this->em = $entityManager;
        $this->basketOrderRepository = $this->em->getRepository("AppBundle:BasketOrder");
    }

    /**
     * Getter for the minimal interval between two orders
     * @return DateInterval
     */
    public function getTimeInterval() {
        return $this->time_interval;
    }

    /**
     * Test if the $tab contains the value $delivery_time
     * @param type $tab
     * @param type $delivery_time
     * @return boolean TRUE if the value is contained in the $tab
     */
    private function contains($tab, $delivery_time) {
        foreach ($tab as &$time) {
            if ($time->getTimestamp() == $delivery_time->getTimestamp()) {
                return True;
            }
        }
        return False;
    }

    /**
     * Calculate a free delivery time between $start and $end
     * @param dateTime $start
     * @param dateTime $end
     * @return dateTime the free delivery time or NULL if no free delivery time is available
     */
    public function deliveryTimeCalculation($start, $end) {
        $taken_delivery_times = $this->basketOrderRepository->getOrdersBetween($start, $end);
        $delivery_time = $start;
        while ($delivery_time <= $end) { // Teste chaque delivery_time possible entre $start et $end avec un pas de $time_interval
            if (!$this->contains(array_column($taken_delivery_times, 'deliveryTime'), $delivery_time)) {
                return $delivery_time;
            } else {
                $delivery_time->add($this->getTimeInterval());
            }
        }
        return NULL;
    }

}
