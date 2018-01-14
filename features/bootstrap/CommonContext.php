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

	// Message d'erreur renvoyé quand on appelle un contrôleur API qui renvoie du JSON
	private $jsonErrorMessage;
	
	// Message d'information renvoyé quand on appelle un contrôleur API qui renvoie du JSON
	private $jsonInfoMessage;
	
	// Message d'erreur affiché quand on appelle un formulaire retournant une vue twig
	private $viewErrorMessage;
	
	// Message d'information affiché quand on appelle un formulaire retournant une vue twig
	private $viewInfoMessage;
	
	private $client;
	
	public function __construct() {
		$this->client = static::createClient();
	}
	
	/**
	 * @AfterScenario
	 *
	 * Nettoie le message après chaque test
	 * */
	public function after() {
		
		$this->jsonErrorMessage = null;
		$this->jsonInfoMessage = null;
		$this->viewErrorMessage = null;
		$this->viewInfoMessage = null;
		
		// Clean all the products.
		$this->client->request('GET', '/product/clean');
		
		// Clean all the categories.
		$this->client->request('GET', '/category/clean');
			
		// Clean all the baskets
		$this->client->request('GET', '/basket/clean');
			
		// Clean all the basket categories.
		$this->client->request('GET', '/basket_category/clean');
		
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
	public function lapplicationRenvoieUnMessageDerreur($errorMessage) {
		$this->assertEquals($errorMessage, $this->jsonErrorMessage);
	}
	
	/**
	 * @Then l'application renvoie un message d'information :infoMessage
	 */
	public function lapplicationRenvoieUnMessageDInformation($infoMessage) {
		$this->assertEquals($infoMessage, $this->jsonInfoMessage);
	}
	
	/**
	 * @Then un message d'erreur s'affiche qui dit :messageText
	 */
	public function unMessageDErreurSAfficheQuiDit($messageText) {
		$this->assertEquals($messageText, $this->viewErrorMessage);
	}
	
	/**
	 * @Then un message d'information s'affiche qui dit :messageText
	 */
	public function unMessageDInformationSAfficheQuiDit($messageText) {
		$this->assertEquals($messageText, $this->viewInfoMessage);
	}
	
	public function updateJsonMessage($content) {
		if(array_key_exists("error", $content)) {
			$this->jsonErrorMessage = $content["error"];
		} else if(array_key_exists("success", $content)) {
			$this->jsonInfoMessage = $content["success"];
		}
	}
	
	public function updateViewMessage($crawler) {
		$error = $crawler->filter("#error-message > p");
		if($error->count() == 1) {
			$this->viewErrorMessage = $error->text();
		}
		$info = $crawler->filter("#success-message > p");
		if($info->count() == 1) {
			$this->viewInfoMessage = $info->text();
		}
	}
	
}