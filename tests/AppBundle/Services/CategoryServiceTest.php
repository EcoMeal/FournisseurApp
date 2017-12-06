<?php

use AppBundle\Services\CategoryService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use AppBundle\Entity\Product;
use AppBundle\Entity\Category;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class CategoryServiceTest extends TestCase {
	
	private $categoryService;
	
	private $em;
	
	private $container;
	
	//Repository for Products
	private $productRepository;
	
	//Repository for Categories
	private $categoryRepository;
	
	//Fake Product
	private $product;
	
	//Fake Category;
	private $category;

	public function setUp() {
		$this->em = $this->createMock(EntityManager::class);
		$this->productRepository = $this->createMock(EntityRepository::class);
		$this->categoryRepository = $this->createMock(EntityRepository::class);
		$this->container = $this->createMock(ContainerInterface::class);
		$this->product = $this->createMock(Product::class);
		$this->category = $this->createMock(Category::class);
		$this->categoryService = new CategoryService($this->em, $this->container);
	}
	
	/**
	 * Tests if an unused category is successfully deleted
	 */
	public function testUnusedCategoryCanBeDeleted() {
		
		$category_id = 1;
		
		//When getRepository Category called -> return mocked categoryRepository
		$this->em->expects($this->at(1))
		->method('getRepository')
		->with("AppBundle:Category")
		->willReturn($this->categoryRepository);
		
		//When getRepository Product called -> return mocked productRepository
		$this->em->expects($this->at(0))
		->method('getRepository')
		->with("AppBundle:Product")
		->willReturn($this->productRepository);
		
		//When productRepository findAll is called, return an empty array
		$this->productRepository->expects($this->once())
	    ->method('findAll')
   		->willReturn(array());
		
   		//Check that categoryRepository findOneById is called
   		$this->categoryRepository->expects($this->once())
   		->method('__call')
   		->with(
   			$this->equalTo('findOneById'),
      		$this->equalTo([$category_id]))
   		->willReturn($this->category);
   		
   		//Expects a call to the entityManager to remove the category return by categoryRepository
   		$this->em->expects($this->once())
   		->method('remove')
   		->with($this->category);
		
		$error = $this->categoryService->deleteCategory($category_id);
		$this->assertNull($error);
	}
	
	public function testUnusedCategoryWithExistingProductsCanBeDeleted() {
		
		$category_id = 1;
		
		//When product->getCategory called, returns the fake category
		$this->product->expects($this->once())
		->method('getCategory')
		->willReturn($this->category);
		
		//When category->getId called, returns another id than the current category
		$this->category->expects($this->once())
		->method('getId')
		->willReturn(2);
		
		//When getRepository Category called -> return mocked categoryRepository
		$this->em->expects($this->at(1))
		->method('getRepository')
		->with("AppBundle:Category")
		->willReturn($this->categoryRepository);
		
		//When getRepository Product called -> return mocked productRepository
		$this->em->expects($this->at(0))
		->method('getRepository')
		->with("AppBundle:Product")
		->willReturn($this->productRepository);
		
		//When productRepository findAll is called, return an empty array
		$this->productRepository->expects($this->once())
		->method('findAll')
		->willReturn(array($this->product));
		
		//Check that categoryRepository findOneById is called
   		$this->categoryRepository->expects($this->once())
   		->method('__call')
   		->with(
   			$this->equalTo('findOneById'),
      		$this->equalTo([$category_id]))
   		->willReturn($this->category);
   		
   		//Expects a call to the entityManager to remove the category return by categoryRepository
   		$this->em->expects($this->once())
   		->method('remove')
   		->with($this->category);
		
		$error = $this->categoryService->deleteCategory($category_id);
		$this->assertNull($error);
	}
	
	public function testUsedCategoryCanNotBeDeleted() {
		
		$category_id = 1;
		
		//When product->getCategory called, returns the fake category
		$this->product->expects($this->once())
		->method('getCategory')
		->willReturn($this->category);
		
		//When category->getId called, returns the current category id
		$this->category->expects($this->once())
		->method('getId')
		->willReturn($category_id);
		
		//When getRepository Product called -> return mocked productRepository
		$this->em->expects($this->once())
		->method('getRepository')
		->with("AppBundle:Product")
		->willReturn($this->productRepository);
		
		//When productRepository findAll is called, return an empty array
		$this->productRepository->expects($this->once())
		->method('findAll')
		->willReturn(array($this->product));
		
		$error = $this->categoryService->deleteCategory($category_id);
		$this->assertEquals("Deletion impossible, the category is affected to a product", $error);
	}
	
	public function testCleanAllCategoriesWhenThereIsNoneDoesntDoAnything() {
		
		//When getRepository Category called -> return mocked categoryRepository
		$this->em->expects($this->once())
		->method('getRepository')
		->with("AppBundle:Category")
		->willReturn($this->categoryRepository);
		
		//When productRepository findAll is called, return an empty array
		$this->categoryRepository->expects($this->once())
		->method('findAll')
		->willReturn(array());
		
		//Checks that the entityManager doesn't remove anything
		$this->em->expects($this->never())
		->method('remove');
		
		$this->categoryService->cleanAllCategory();
		
	}
	
	public function testCleanAllCategoriesWhenThereAreCategoriesRemovesIt() {
		
		//When getRepository Category called -> return mocked categoryRepository
		$this->em->expects($this->once())
		->method('getRepository')
		->with("AppBundle:Category")
		->willReturn($this->categoryRepository);
		
		//When productRepository findAll is called, return an empty array
		$this->categoryRepository->expects($this->once())
		->method('findAll')
		->willReturn(array($this->category));
		
		//Checks that the entityManager doesn't remove anything
		$this->em->expects($this->once())
		->method('remove')
		->with($this->category);
		
		$this->categoryService->cleanAllCategory();
		
	}
	
	public function testGetAllCategoriesCallsEntityManagerWithRightParameters() {
		//When getRepository Category called -> return mocked categoryRepository
		$this->em->expects($this->once())
		->method('getRepository')
		->with("AppBundle:Category")
		->willReturn($this->categoryRepository);
		
		//check that categoryRepository findBy is called with the right parameters
		$this->categoryRepository->expects($this->once())
		->method('findBy')
		->with(
			$this->equalTo([]),
      		$this->equalTo(["name" => "ASC"])
		);
		
		//We don't care about the result, it doesn't depend on the service
		$this->categoryService->getAllCategoriesOrdererByName();
		
	}
	
	public function testSavingNewCategorySucceeds() {
		//Creates a mock category to be saved
		$categoryToSave = $this->createMock(Category::class);
		
		//Expects a call to getName
		$categoryToSave->expects($this->once())
		->method('getName')
		->willReturn('unknown_category_name');
		
		//When getRepository Category called -> return mocked categoryRepository
		$this->em->expects($this->once())
		->method('getRepository')
		->with("AppBundle:Category")
		->willReturn($this->categoryRepository);
		
		//check that categoryRepository findBy is called with the right parameters
		$this->categoryRepository->expects($this->once())
		->method('__call')
		->with(
			$this->equalTo("findOneByName"),
			$this->equalTo(["unknown_category_name"])
		)
		->willReturn(null);

		$this->em->expects($this->once())
		->method('persist')
		->with($categoryToSave);
		
		$error = $this->categoryService->saveCategory($categoryToSave);
		$this->assertNull($error);
	}
	
	public function testSavingExistingCategoryFails() {
		
		//Creates a mock category to be saved
		$categoryToSave = $this->createMock(Category::class);
		
		//Expects a call to getName
		$categoryToSave->expects($this->once())
		->method('getName')
		->willReturn('existing_category_name');
		
		//When getRepository Category called -> return mocked categoryRepository
		$this->em->expects($this->once())
		->method('getRepository')
		->with("AppBundle:Category")
		->willReturn($this->categoryRepository);
		
		//check that categoryRepository findBy is called with the right parameters
		$this->categoryRepository->expects($this->once())
		->method('__call')
		->with(
				$this->equalTo("findOneByName"),
				$this->equalTo(["existing_category_name"])
		)
		->willReturn($this->category);
		
		$error = $this->categoryService->saveCategory($categoryToSave);
		
		$this->assertEquals("La catégorie existe déjà", $error);
		
	}
	
	public function testSavingCategoryImageSucceeds() {
		
		$file = $this->createMock(UploadedFile::class);
		
		$file->expects($this->once())
		->method('guessExtension')
		->willReturn("png");
		
		$file->expects($this->once())
		->method('move');
		
		//Checks that the imagePath is set
		$this->category->expects($this->once())
		->method('setImagePath');
		
		//Check that the container image_directory is requested
		$this->container->expects($this->once())
		->method('getParameter');
		
		$this->category->expects($this->once())
		->method('getImagePath')
		->willReturn($file);
		
		$this->categoryService->registerCategoryImage($this->category);
	}
	
	public function testSavingNullCategoryImageDoesntDoAnything() {
		//The category image is null
		$this->category->expects($this->once())
		->method('getImagePath')
		->willReturn(null);
		
		//Checks that the imagePath is NOT set
		$this->category->expects($this->never())
		->method('setImagePath');
		
		//Check that the container image_directory is NOT requested
		$this->container->expects($this->never())
		->method('getParameter');
		
		$this->categoryService->registerCategoryImage($this->category);
		
	}
	
}