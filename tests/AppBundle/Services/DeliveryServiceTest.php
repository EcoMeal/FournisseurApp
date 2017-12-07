<?php

namespace AppBundle\tests\Services;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use \DateTime;
use PHPUnit\Framework\TestCase;
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
        $this->start->setTimestamp(1355310732);
        $this->end = new DateTime();
        $this->end->setTimestamp(1355314332);
        $this->mockEntity = $this->createMock(EntityManager::class);
        $this->mockRepository = $this->createMock(BasketOrderRepository::class);
        $this->mockEntity->method('getRepository')
                ->willReturn($this->mockRepository);
        $this->deliveryService = new DeliveryService($this->mockEntity);
        $this->delivery_time = clone $this->start;
    }

    public function testDeliveryTimeCalculation() {
        $taken_delivery_time = array();
        $this->mockRepository->method('getOrdersBetween')
                ->willReturn($taken_delivery_time);
        $res = $this->deliveryService->deliveryTimeCalculation($this->start, $this->end);

        $this->assertEquals($this->delivery_time, $res);
    }

    public function testDeliveryTimeCalculationWithFirstTaken() {
        $taken_delivery_time = array(clone $this->start);
        $this->mockRepository->method('getOrdersBetween')
                ->willReturn($taken_delivery_time);
        $this->delivery_time->add($this->deliveryService->getTimeInterval());
        $res = $this->deliveryService->deliveryTimeCalculation($this->start, $this->end);

        $this->assertEquals($this->delivery_time, $res);
    }

   public function testDeliveryTimeCalculationFull() {
        $taken_delivery_time = array();
        while ($this->delivery_time <= $this->end) {
            array_push($taken_delivery_time, clone $this->delivery_time);
            $this->delivery_time->add($this->deliveryService->getTimeInterval());
        }

        $this->mockRepository->method('getOrdersBetween')
                ->willReturn($taken_delivery_time);
        $res = $this->deliveryService->deliveryTimeCalculation($this->start, $this->end);

        $this->assertEquals(NULL, $res);
    }
    
    public function testDeliveryTimeCalculationFullExceptLast() {
        $taken_delivery_time = array();
        
        while ($this->delivery_time <= $this->end) {
            array_push($taken_delivery_time, clone $this->delivery_time);
            $this->delivery_time->add($this->deliveryService->getTimeInterval());
        }
        array_pop($taken_delivery_time);
        
        $this->mockRepository->method('getOrdersBetween')
                ->willReturn($taken_delivery_time);
        $res = $this->deliveryService->deliveryTimeCalculation($this->start, $this->end);

        $this->assertEquals(1355314332, $res->getTimestamp());
    }

}
