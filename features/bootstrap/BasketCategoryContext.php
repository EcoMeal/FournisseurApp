<?php

$_SERVER['KERNEL_DIR'] = __DIR__ . '/../../app/';
use Behat\Behat\Context\Context;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Behat\Hook\Scope\AfterScenarioScope;

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
	
	/**
	 * @AfterScenario
	 *
	 * Nettoie la BDD après chaque test.
	 * */
	public function after(AfterScenarioScope $scope)
	{
		// Clean all the basket categories.
		$this->client->request('GET', '/basket_category/clean');
	}
	
	// FEATURES
	
	/**
	 * @Given il n'y a aucune catégorie de panier dans l'application
	 */
	public function ilNyAAucuneCategorieDePanierDansLapplication()
	{
		$crawler = $this->client->request('GET', '/basket_category');
		// By default there is already one card for the basket category creation
		// which has the same class
		$this->assertEquals(1, $crawler->filter('.card-image-label')->count(),
				"There is already a basket category in the application before the test run.");
	}
	
	/**
	 * @When j'ajoute la catégorie de panier :basket_category_name dans l'application
	 */
	public function jajouteLaCategorieDePanierDansLapplication($basket_category_name)
	{
		$crawler = $this->client->request('GET', '/basket_category');
	
		$form = $crawler->selectButton('Valider')->form();
		// Set the task values
		$form->setValues(array('appbundle_basketcategory[name]' => $basket_category_name));
		// submit the form
		$this->client->submit($form);
	}
	
	/**
	 * @Then il y a une catégorie de panier :basket_category_name dans l'application
	 */
	public function ilYAUneCategorieDePanierDansLapplication($basket_category_name)
	{
		$crawler = $this->client->request('GET', '/basket_category');
	
		$basket_category_count = $this->utilContext->getItemCardCount($crawler, $basket_category_name);
	
		$this->assertEquals(1, $basket_category_count, "Basket category '". $basket_category_name ."' count is incorrect");
	}
	
}