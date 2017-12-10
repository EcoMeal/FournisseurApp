<?php

namespace AppBundle\tests\Services;

use PHPUnit\Framework\TestCase;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use AppBundle\Services\BasketOrderService;
use AppBundle\Entity\Basket;

class BasketOrderServiceTest extends TestCase {
	
	private $em;
	
	private $basketRepository;
	
	private $basketOrderRepository;
	
	private $basketOrderService;
	
	public function setUp() {
		$this->em = $this->createMock(EntityManager::class);
		$this->basketRepository = $this->createMock(EntityRepository::class);
		$this->basketOrderRepository = $this->createMock(EntityRepository::class);
		$this->basketOrderService = new BasketOrderService($this->em);
	}
	
	public function testEmptyOrderIsNotAccepted() {
		$order = (object) array(
				"username" => "",
				"order_time" => "",
				"delivery_time" => "",
				"content" => array()
		);
		$accepted = $this->basketOrderService->checkOrder($order);
		$this->assertFalse($accepted);
	}
	
	public function testInvalidOrderIsNotAccepted() {
		$order = (object) array(
				"username" => "",
				"order_time" => "",
				"delivery_time" => "",
				"content" => array(1)
		);
		
		$this->em->expects($this->once())
		->method('getRepository')
		->willReturn($this->basketRepository);
		
		$this->basketRepository->expects($this->once())
		->method('find')
		->with(1)
		->willReturn(null);
		
		$accepted = $this->basketOrderService->checkOrder($order);
		$this->assertFalse($accepted);
	}
	
	public function testValidOrderIsAccepted() {
		
		$basket = new Basket();
		
		$order = (object) array(
				"username" => "",
				"order_time" => "",
				"delivery_time" => "",
				"content" => array(1)
		);
		
		$this->em->expects($this->once())
		->method('getRepository')
		->willReturn($this->basketRepository);
		
		$this->basketRepository->expects($this->once())
		->method('find')
		->with(1)
		->willReturn($basket);
		
		$accepted = $this->basketOrderService->checkOrder($order);
		$this->assertTrue($accepted);
	}
	
	public function testSaveOrderFailsOnInvalidOrder() {
		$order = (object) array(
				"username" => "",
				"order_time" => "",
				"delivery_time" => "",
				"content" => array()
		);
		$order_id = $this->basketOrderService->saveOrder($order);
		$this->assertNull($order_id);
	}
	
	public function testSaveOrderSucceedsOnValidOrder() {
		
		$fake_order_id = 1;
		
		$basket = $this->createMock(Basket::class);
		
		//A valid order object
		$order = (object) array(
				"username" => "",
				"order_time" => "",
				"delivery_time" => "",
				"content" => array(1)
		);
		
		//When getRepository called the first time, returns the basketRepository
		$this->em->expects($this->any())
		->method('getRepository')
		->willReturn($this->basketRepository);
		
		//When the basketRepository is called with the id in the order, return a basket
		$this->basketRepository->expects($this->any())
		->method('find')
		->with(1)
		->willReturn($basket);
			
		$order_id = $this->basketOrderService->saveOrder($order);
		$this->assertEquals($fake_order_id, $order_id);
	}
	
}