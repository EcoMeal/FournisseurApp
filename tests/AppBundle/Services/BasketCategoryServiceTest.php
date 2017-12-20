<?php

namespace AppBundle\tests\Services;

use PHPUnit\Framework\TestCase;
use AppBundle\Services\BasketCategoryService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\Basket;
use AppBundle\Entity\BasketCategory;
use Symfony\Component\DependencyInjection\ContainerInterface;

class BasketCategoryServiceTest extends TestCase {

	private $basketCategoryService;
	
	private $em;
	
	private $container;
	
	private $basketRepository;
	
	private $basketCategoryRepository;

	//Fake Basket
	private $basket;
	
	//Fake Category;
	private $category;
	
	public function setUp() {
		$this->em = $this->createMock(EntityManager::class);
		$this->container = $this->createMock(ContainerInterface::class);
		$this->basket = $this->createMock(Basket::class);
		$this->category = $this->createMock(BasketCategory::class);
		$this->basketRepository = $this->createMock(EntityRepository::class);
		$this->basketCategoryRepository = $this->createMock(EntityRepository::class);
		$this->basketCategoryService = new BasketCategoryService($this->em, $this->container);
	}
	
	/**
	 * Test if an unused basket category is successfully deleted
	 */
	public function testUnusedCategoryCanBeDeleted() {
		
		$category_id = 1;
		
		//When getRepository BasketCategory called -> return mocked basketCategoryRepository
		$this->em->expects($this->at(1))
		->method('getRepository')
		->with("AppBundle:BasketCategory")
		->willReturn($this->basketCategoryRepository);
		
		//When getRepository Basket called -> return mocked basketRepository
		$this->em->expects($this->at(0))
		->method('getRepository')
		->with("AppBundle:Basket")
		->willReturn($this->basketRepository);
		
		//When basketRepository findAll is called, return an empty array
		$this->basketRepository->expects($this->once())
	    ->method('findAll')
   		->willReturn(array());
		
   		//Check that categoryRepository findOneById is called
   		$this->basketCategoryRepository->expects($this->once())
   		->method('__call')
   		->with(
   			$this->equalTo('findOneById'),
      		$this->equalTo([$category_id]))
   		->willReturn($this->category);
   		
   		//Expects a call to the entityManager to remove the basket category return by basketCategoryRepository
   		$this->em->expects($this->once())
   		->method('remove')
   		->with($this->category);
		
		$error = $this->basketCategoryService->deleteBasketCategory($category_id);
		$this->assertNull($error);
	}
	
	/**
	 * If a category is used by a basket then the category cannot be deleted
	 */
	public function testUsedCategoryCanNotBeDeleted() {
		
		$category_id = 1;
		
		//When basket->getCategory called, returns the fake category
		$this->basket->expects($this->once())
		->method('getCategory')
		->willReturn($this->category);
		
		//When category->getId called, returns the current category id
		$this->category->expects($this->once())
		->method('getId')
		->willReturn($category_id);
		
		//When getRepository Basket called -> return mocked basketRepository
		$this->em->expects($this->once())
		->method('getRepository')
		->with("AppBundle:Basket")
		->willReturn($this->basketRepository);
		
		//When basketRepository findAll is called, return an empty array
		$this->basketRepository->expects($this->once())
		->method('findAll')
		->willReturn(array($this->basket));
		
		$error = $this->basketCategoryService->deleteBasketCategory($category_id);
		$this->assertEquals("Deletion impossible, the basket category is used by a basket", $error);
	}
	
}