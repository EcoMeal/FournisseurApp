<?php

// To avoid the kernel exception due to WebtestCase...
$_SERVER['KERNEL_DIR'] = __DIR__ . '/../../app/';

use Behat\Behat\Context\Context;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Defines application features from the specific context.
 */
class CommonContext extends WebTestCase implements Context
{
	
	private $commonMessage;
	
	/**
	 * @AfterScenario
	 *
	 * Nettoie le message après chaque test
	 * */
	public function after()
	{
             echo "In common context";
            // Clean all the products.
            //$this->client->request('GET', '/product/clean');
            // Clean all the categories.
            //$this->client->request('GET', '/category/clean');	
            // Clean all the baskets
            //$this->client->request('GET', '/basket/clean');		
            // Clean all the basket categories.
            //$this->client->request('GET', '/basket_category/clean');
            // Clean all the orders.
            //$this->client->request('GET', '/orders/clean');
            
            // Clear the JSON message.    
            $this->commonMessage = null;
	}
	
	
	//Features
	
	/**
	 * Placeholder function when no actions can be assigned. To keep the flow:
	 * Given, When, Then.
	 * @When void
	 */
	public function void()
	{
	
	}
	
	/**
	 * @Then l'application renvoie un message d'erreur :errorMessage
	 */
	public function lapplicationRenvoieUnMessageDerreur($errorMessage)
	{
		$this->assertEquals($errorMessage, $this->commonMessage['error']);
	}
	
	public function getMessage() {
		return $this->commonMessage;
	}
	
	public function setMessage($message) {
		$this->commonMessage = $message;
	}
	
}