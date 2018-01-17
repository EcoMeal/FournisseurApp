<?php

$_SERVER['KERNEL_DIR'] = __DIR__ . '/../../app/';
use Behat\Behat\Context\Context;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Defines application features from the specific context.
 */
class EntityCreationContext extends WebTestCase implements Context
{
	
	private $client;
	
	public function __construct() {
		$this->client = static::createClient();
	}
	
	public function createBasketFromScratch($basket_name,
			$basket_category_name = "testBasketCategory", $product_name = "testProduct",
			$product_category_name = "testProductCategory") {
	
		//product category
		$this->createProductCategory($product_category_name);

		//product
		$this->createProduct($product_name);

		//basket category
		$this->createBasketCategory($basket_category_name);

		//basket
		return $this->createBasket($basket_name);
	
	}
	
	public function createProductFromScratch($product_name, $imagePath = NULL,
			$product_category_name = "testProductCategory")
	{
		// First I create a category.
		$this->createProductCategory($product_category_name);
	
		// Then I create a product.
		return $this->createProduct($product_name, $imagePath);
	}
	
	 
	public function createProductCategory($category, $imagePath = NULL)
	{
		$crawler = $this->client->request('GET', '/category');
	
		$form = $crawler->selectButton('Valider')->form();
		// Set the task values
		$form->setValues(array('appbundle_category[name]' => $category));
	
		if($imagePath != NULL){
			$image = new UploadedFile("web/images/".$imagePath, "test.png", "image/png");
			$form->setValues(array('appbundle_category[imagePath]' => $image));
		}
		// submit the form
		return $this->client->submit($form);
	}
	
	public function createProduct($product, $imagePath = NULL)
	{
		$crawler = $this->client->request('GET', '/product');
		
		$form = $crawler->selectButton('Valider')->form();
		// Set the task values
		$form->setValues(array('appbundle_product[name]' => $product));
	
		if($imagePath != NULL){
			$image = new UploadedFile("web/images/".$imagePath, "test.png", "image/png");
			$form->setValues(array('appbundle_product[imagePath]' => $image));
		}
	
		// submit the form
		return $this->client->submit($form);
	}
	
	public function createBasketCategory($basketCategory, $imagePath = NULL) {
		$crawler = $this->client->request('GET', '/basket_category');
		
		$form = $crawler->selectButton('Valider')->form();
		
		// Set the task values
		$form->setValues(array('appbundle_basketcategory[name]' => $basketCategory));
	
		if($imagePath != NULL){
			$image = new UploadedFile("web/images/".$imagePath, "test.png", "image/png");
			$form->setValues(array('appbundle_basketcategory[imagePath]' => $image));
		}
	
		// submit the form
		return $this->client->submit($form);
	}
	
	public function createBasket($basket, $product_list = NULL) {
		$crawler = $this->client->request('GET', '/basket');
		$form = $crawler->selectButton('Valider')->form();
		// Set the task values
		$form->setValues(array('appbundle_basket[name]' => $basket));
		$form->setValues(array('appbundle_basket[price]' => 10));
		if($product_list != NULL){
			$form->setValues(array('appbundle_basket[product_list]' => $product_list));
		}
		// submit the form
		return $this->client->submit($form);
	}
        
    public function createOrder($basketIdList, $delivery_time){
            
        $order = array(
			"username" => "",
			"order_time" => "",
			"delivery_time" => $delivery_time,
			"content" => json_encode($basketIdList)
		);
		
        $this->client->request(
			"POST", //Methode
			"/api/basket_order", //URI 
			array(), //Parametres
			array(), //Fichiers
			array("Content-Type" => "application/json"), //Headers
			json_encode($order)); // Contenu
            
        $response = $this->client->getResponse();
        $data = json_decode($response->getContent());
            
        // Return the order id.
        return $data->order_id;
    }
	/*
	public function setProductStock($product, $stock) {
		$crawler = $this->client->request('GET', '/stock');
		$id = $crawler->filter('[data-name="'. $product .'"]')->attr("data-id");
		$modifQuantite = $id."=".$stock;
		$this->client->request(
			"POST", //Methode
			"/stock", //URI
			array(), //Parametres
			array(), //Fichiers
			array("Content-Type" => "application/json"), //Headers
			$modifQuantite); // Contenu
	}*/
        
        public function setProductStock($array) {
		$crawler = $this->client->request('GET', '/stock');
		
                
                
                $nb_items = count($array);
                $i = 0;
               
                $modifQuantite = "";
                foreach($array as $product_name => $stock){
                    
                    $id = $crawler->filter('[data-name="'. $product_name .'"]')->attr("data-id");
                    
                    $modifQuantite .= $id."=".$stock;
                    if($i < $nb_items - 1){
                        $modifQuantite .= "&";
                    }
                    $i++;
                }
                
		$modifQuantite = $id."=".$stock;
		return $this->client->request(
			"POST", //Methode
			"/stock", //URI
			array(), //Parametres
			array(), //Fichiers
			array("Content-Type" => "application/json"), //Headers
			$modifQuantite); // Contenu*/
	}
	
}