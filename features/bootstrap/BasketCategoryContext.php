<?php

$_SERVER['KERNEL_DIR'] = __DIR__ . '/../../app/';
use Behat\Behat\Context\Context;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;


/**
 * Defines application features from the specific context.
 */
class BasketCategoryContext extends WebTestCase implements Context
{
	
	/* CONTEXTES UTILITAIRES */
	
	//Creation d'entités (paniers, produits, ...)
	private $entityCreationContext;
	
	//Fonctions utiles (par exemple compter des items sur une page)
	private $utilContext;
	
	private $client;
	
	public function __construct() {
		$this->client = static::createClient();
	}
	
	/** @BeforeScenario */
	public function getSubcontexts(BeforeScenarioScope $scope) {
		$environment = $scope->getEnvironment();
		$this->entityCreationContext = $environment->getContext("EntityCreationContext");
		$this->utilContext = $environment->getContext("UtilContext");
	}
	
	// FEATURES
	
	/**
	 * @Given il n'y a aucune catégorie de panier dans l'application
	 */
	public function ilNyAAucuneCategorieDePanierDansLapplication() {
		$crawler = $this->client->request('GET', '/basket_category');
		// By default there is already one card for the basket category creation
		// which has the same class
		$this->assertEquals(1, $crawler->filter('.card-image-label')->count(),
				"There is already a basket category in the application before the test run.");
	}
	
	/**
	 * @When j'ajoute la catégorie de panier :basket_category_name dans l'application
	 */
	public function jajouteLaCategorieDePanierDansLapplication($basket_category_name) {
		$this->entityCreationContext->createBasketCategory($basket_category_name, "placeholder.png");
	}
	
	/**
	 * @Then il y a une catégorie de panier :basket_category_name dans l'application
	 */
	public function ilYAUneCategorieDePanierDansLapplication($basket_category_name)	{
		$crawler = $this->client->request('GET', '/basket_category');
		$basket_category_count = $this->utilContext->getItemCardCount($crawler, $basket_category_name);
		$this->assertEquals(1, $basket_category_count, "Basket category '". $basket_category_name ."' count is incorrect");
	}
	
	/**
	 * @When j'ajoute la catégorie de panier :basket_category_name sans ajouter d'image
	 */
	public function jajouteLaCategorieDePanierSansAjouterDimage($basket_category_name)
	{
		$this->entityCreationContext->createBasketCategory($basket_category_name);
	}
	
	/**
	 * @Then il y a une seule catégorie de panier :basket_category_name avec l'image par défaut
	 */
	public function ilYAUneSeuleCategorieDePanierAvecLimageParDefaut($basket_category_name)
	{
		$crawler = $this->client->request('GET', '/basket_category');
		$imagePath = $crawler->filter('[data-name="'. $basket_category_name .'"] > img')->attr("src");
		$this->assertEquals("images/placeholder.png", $imagePath, "La catégorie de panier ne s'affiche pas avec l'image par défaut.");
	}
	
	/**
	 * @Given il existe une catégorie de panier :basket_category_name dans l'application
	 */
	public function ilExisteUneCategorieDePanierDansLapplication($basket_category_name)
	{
		$this->entityCreationContext->createBasketCategory($basket_category_name);
	}
	
	/**
	 * @Then la catégorie de panier :basket_category_name n'est pas crée parce qu'elle existe déjà
	 */
	public function laCategorieDePanierNestPasCreeParceQuelleExisteDeja($basket_category_name)
	{
		$crawler = $this->client->request('GET', '/basket_category');
		$basket_category_count = $this->utilContext->getItemCardCount($crawler, $basket_category_name);
		$this->assertEquals(1, $basket_category_count, "Basket category '". $basket_category_name ."' count is incorrect");
	}
	
	
}