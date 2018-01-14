<?php

$_SERVER['KERNEL_DIR'] = __DIR__ . '/../../app/';
use Behat\Behat\Context\Context;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Behat\Hook\Scope\AfterScenarioScope;

/**
 * Defines application features from the specific context.
 */
class BasketContext extends WebTestCase implements Context
{
	
	/* CONTEXTES UTILITAIRES */
	
	//Creation d'entités (paniers, produits, ...)
	private $entityCreationContext;
	
	//Fonctions utiles (par exemple compter des items sur une page)
	private $utilContext;
	
	private $commonContext;
	
	//Client pour les requêtes
	private $client;
	
	//Message d'erreur retourné par certaines requêtes
	private $errorMessage;
	
	public function __construct() {
		$this->client = static::createClient();
	}
	
	/** 
	 * @BeforeScenario 
	 * 
	 * Charges les sous-contextes avant chaque test.
	 * */
	public function getSubcontexts(BeforeScenarioScope $scope) {
		$environment = $scope->getEnvironment();
		$this->entityCreationContext = $environment->getContext("EntityCreationContext");
		$this->utilContext = $environment->getContext("UtilContext");
		$this->commonContext = $environment->getContext("CommonContext");
	}
	
	// GIVEN
	
	/**
	 * @Given il n’y a aucun panier dans l'application
	 */
	public function ilNyAAucunPanierDansLapplication()
	{
		$crawler = $this->client->request('GET', '/basket');
		// By default there is already one card for the basket creation
		// which has the same class
		$this->assertEquals(1, $crawler->filter('.card-image-label')->count(),
				"There is already a basket in the application before the test run.");
	}
	
	/**
	 * @Given il existe un panier :basket_name
	 */
	public function ilExisteUnPanier($basket_name)
	{
		$this->entityCreationContext->createBasketFromScratch($basket_name);
	}
	
	/**
	 * @Given il existe une commande en cours avec le panier :basket_name
	 */
	public function ilExisteUneCommandeEnCoursAvecLePanier($basket_name)
	{
		$crawler = $this->client->request('GET', '/basket');
    	$filter = "[onclick*=\"deleteBasket('". $basket_name ."',\"]";
    	$basketId = $this->utilContext->getItemCardId($crawler, $filter);
    	$this->entityCreationContext->createOrder(array($basketId), 12345);
	}
	
	// WHEN

	/**
	 * @When j'ajoute le panier :basket_name
	 */
	public function jajouteLePanier($basket_name)
	{
		$crawler = $this->entityCreationContext->createBasketFromScratch($basket_name);
 		$this->commonContext->updateViewMessage($crawler);
	}
	
	/**
	 * @When je supprime le panier :basket_name
	 */
	public function jeSupprimeLePanier($basket_name)
	{
		$crawler = $this->client->request('GET', '/basket');
    	// Filter to find the correct onclick attribute for the basket.
    	$filter = "[onclick*=\"deleteBasket('". $basket_name ."',\"]";
    	$basketId = $this->utilContext->getItemCardId($crawler, $filter);
    	// With the basket ID we can now delete it.
    	$this->client->request('DELETE', '/basket/'.$basketId);
    	$this->commonContext->updateJsonMessage(json_decode($this->client->getResponse()->getContent(), true));
	}
	
	// THEN
	
	/**
	 * @Then le panier :basket_name est affiché
	 */
	public function lePanierEstAffiche($basket_name)
	{
		$crawler = $this->client->request('GET', '/basket');
		$basket_count = $this->utilContext->getItemCardCount($crawler, $basket_name);
		$this->assertEquals(1, $basket_count, "Basket '". $basket_name ."' count is incorrect");
	}
	
	/**
	 * @Then il n'y a plus de panier :basket_name
	 */
	public function ilNyAPlusDePanier($basket_name)
	{
		$crawler = $this->client->request('GET', '/basket');
		$basket_count = $this->utilContext->getItemCardCount($crawler, $basket_name);
		$this->assertEquals(0, $basket_count, "Basket '". $basket_name ."' count is incorrect");
	}
	
	/**
	 * @Then j'obtiens une erreur :error
	 */
	public function jobtiensUneErreur($error)
	{
		$this->assertEquals($error, $this->errorMessage, "The error message is incorrect or non existent");
	}
	
}