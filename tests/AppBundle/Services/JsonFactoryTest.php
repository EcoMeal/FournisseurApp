<?php

use PHPUnit\Framework\TestCase;
use AppBundle\Services\JsonFactory;
use AppBundle\Entity\Product;
use AppBundle\Entity\Category;
use AppBundle\Entity\Basket;
use AppBundle\Entity\BasketCategory;

class JsonFactoryTest extends TestCase
{
    
    protected $jsonFactory;
    
    public function setUp() {
        $this->jsonFactory = new JsonFactory();
    }
    
    public function testEmptyBasketListReturnsEmptyArray() {
        
        $baskets = array();
        $basketsAsJson = $this->jsonFactory->getBaskets($baskets);
        $this->assertEquals([], $basketsAsJson);
        
    }
    
    public function testNullBasketListReturnsEmptyArray() {
        
        $baskets = null;
        $basketsAsJson = $this->jsonFactory->getBaskets($baskets);
        $this->assertEquals([], $basketsAsJson);
        
    }
    
    public function testOneBasketWithNoProductsIsCorrectlyReturned() {
        
        $category = new BasketCategory();
        $category->setName("Arnaque");
        $basket = new Basket();
        $basket->setName("Panier vide");
        $basket->setCategory($category);
        $basket->setPrice(10);
        $basket->setProductList(null);
        $baskets = array($basket);
        $basketsAsJson = $this->jsonFactory->getBaskets($baskets);
        $expectedJson = "[{\"name\":\"Panier vide\",\"price\":10,\"category\":\"Arnaque\",\"category_image\":null,\"products\":[]}]";
        $this->assertEquals($expectedJson, json_encode($basketsAsJson));
        
    }
    
    public function testEmptyProductListReturnsEmptyArray() {
        
        $products = [];
        $productsAsJson = $this->jsonFactory->getProducts($products);
        $this->assertEquals([], $productsAsJson);
        
    }
    
    public function testNullProductListReturnsEmptyArray() {
        
        $products = null;
        $productsAsJson = $this->jsonFactory->getProducts($products);
        $this->assertEquals([], $productsAsJson);
        
    }
    
    public function testProductListWithOneProductIsCorrectlyReturned() {
        
        $category = new Category();
        $category->setName("Viande");
        $product = new Product();
        $product->setName("Steak Charal");
        $product->setCategory($category);
        $products = array($product);
        $productsAsJson = $this->jsonFactory->getProducts($products);
        $expectedJson = "[{\"name\":\"Steak Charal\",\"category\":\"Viande\"}]";
        $this->assertEquals($expectedJson, json_encode($productsAsJson));
        
    }
    
}


