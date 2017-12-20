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
	}
	
	/**
	 * @AfterScenario
	 *
	 * Nettoie la BDD après chaque test.
	 * */
	public function after(AfterScenarioScope $scope)
	{
		// Clear the error message.
		$errorMessage = null;
			
		// Clean all the products.
		$this->client->request('GET', '/product/clean');
	
		// Clean all the categories.
		$this->client->request('GET', '/category/clean');
			
		// Clean all the baskets
		$this->client->request('GET', '/basket/clean');
			
		// Clean all the basket categories.
		$this->client->request('GET', '/basket_category/clean');
			
	}
	
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
	 * @When je crée un panier :basket_name dans l'application
	 */
	public function jeCreeUnPanierDansLapplication($basket_name)
	{
		$this->entityCreationContext->createBasketFromScratch($basket_name);
	}
	
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
	 * @Given j’ai un panier :basket_name disponible
	 */
	public function jaiUnPanierDisponible($basket_name)
	{
		$this->entityCreationContext->createBasketFromScratch($basket_name);
	}
	
	/**
	 * @When j’ajoute un nouveau panier :basket_name
	 */
	public function jajouteUnNouveauPanier($basket_name)
	{
		$crawler = $this->entityCreationContext->createBasketFromScratch($basket_name);
		$this->errorMessage = trim($crawler->filter(".alert-danger")->text());
	}
	
	/**
	 * @Then j'obtiens une erreur :error
	 */
	public function jobtiensUneErreur($error)
	{
		$this->assertEquals($error, $this->errorMessage, "The error message is incorrect or non existent");
	}
	
}