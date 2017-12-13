<?php

namespace AppBundle\tests\Services;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use \DateTime;
use AppBundle\Services\DeliveryService;
use Doctrine\ORM\EntityManager;
use AppBundle\Repository\BasketOrderRepository;

class DeliveryServiceTest extends WebTestCase {

    private $start;
    private $end;
    private $mockEntity;
    private $mockRepository;
    private $delivery_time;
    private $deliveryService;

    
    public function setup() {
        $this->start = new DateTime();
        $this->start->setTimestamp(1355310732); // 12/12/2012 12:12:12
        $this->end = new DateTime();
        $this->end->setTimestamp(1355314332); // 12/12/2012 13:12:12
        $this->mockEntity = $this->createMock(EntityManager::class);
        $this->mockRepository = $this->createMock(BasketOrderRepository::class);
        $this->mockEntity->method('getRepository')
                ->willReturn($this->mockRepository); // Permet de mocker le repository de notre entityManager
        $this->deliveryService = new DeliveryService($this->mockEntity);
        $this->delivery_time = clone $this->start;
    }

    /**
     * Vérify that the date is the first one of the time slot if possible
     */
    public function testDeliveryTimeCalculation() {
        $taken_delivery_time = array();
        $this->mockRepository->method('getOrdersBetween')
                ->willReturn(array('id'=>1,'deliveryTime'=>$taken_delivery_time)); // Aucun horaire n'est réservé
        $res = $this->deliveryService->deliveryTimeCalculation($this->start, $this->end);

        $this->assertEquals($this->delivery_time, $res); // 12/12/2012 12:12:12
    }

    /**
     * Verify that the date is the second one of the time slot when the first one is taken
     */
    public function testDeliveryTimeCalculationWithFirstTaken() {
        $taken_delivery_time = array(array('id'=>1,'deliveryTime'=>clone $this->start));
        $this->mockRepository->method('getOrdersBetween')
                ->willReturn($taken_delivery_time); // Le premier horaire est réservé
        //echo var_dump($taken_delivery_time);
        $this->delivery_time->add($this->deliveryService->getTimeInterval());
        $res = $this->deliveryService->deliveryTimeCalculation($this->start, $this->end);

        $this->assertEquals($this->delivery_time, $res); // 12/12/2012 12:14:12
    }

    /**
     * Verify that null is returned when every date of the time slot are taken
     */
   public function testDeliveryTimeCalculationFull() {
        $taken_delivery_time = array();
        while ($this->delivery_time <= $this->end) { // Crée un tableau contenant tous les horaires possibles entre la date de début et de fin
            array_push($taken_delivery_time, array('id'=>1,'deliveryTime'=>clone $this->delivery_time));
            $this->delivery_time->add($this->deliveryService->getTimeInterval());
        }

        $this->mockRepository->method('getOrdersBetween')
                ->willReturn($taken_delivery_time); // Tous les horaires sont réservés
        $res = $this->deliveryService->deliveryTimeCalculation($this->start, $this->end);

        $this->assertEquals(NULL, $res); // NULL
    }
    
    /**
     * Verify that the last date is returned when every date except the last one are taken
     */
    public function testDeliveryTimeCalculationFullExceptLast() {
        $taken_delivery_time = array();
        
        while ($this->delivery_time <= $this->end) { // Crée un tableau contenant tous les horaires possibles entre la date de début et de fin
            array_push($taken_delivery_time, array('id'=>1,'deliveryTime'=>clone $this->delivery_time));
            $this->delivery_time->add($this->deliveryService->getTimeInterval());
        }
        array_pop($taken_delivery_time);
        
        $this->mockRepository->method('getOrdersBetween')
                ->willReturn($taken_delivery_time); // Tous les horaires sont réservés sauf le dernier
        $res = $this->deliveryService->deliveryTimeCalculation($this->start, $this->end);

        $this->assertEquals(1355314332, $res->getTimestamp()); // 12/12/2012 13:12:12
    }

}
