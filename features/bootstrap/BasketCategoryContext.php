<?php

$_SERVER['KERNEL_DIR'] = __DIR__ . '/../../app/';
use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

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
	
	private $commonContext;
	
	private $client;
	
	public function __construct() {
		$this->client = static::createClient();
	}
	
	/** @BeforeScenario */
	public function getSubcontexts(BeforeScenarioScope $scope) {
		$environment = $scope->getEnvironment();
		$this->entityCreationContext = $environment->getContext("EntityCreationContext");
		$this->utilContext = $environment->getContext("UtilContext");
		$this->commonContext = $environment->getContext("CommonContext");
	}
	
	// GIVEN
	
	/**
	 * @Given il n'y a aucune catégorie de panier
	 */
	public function ilNyAAucuneCategorieDePanier() {
		$crawler = $this->client->request('GET', '/basket_category');
		$this->assertEquals(1, $crawler->filter('.card-image-label')->count(),
				"There is already a basket category in the application before the test run.");
	}
	
	/**
	 * @Given il existe une catégorie de panier :basket_category_name
	 */
	public function ilExisteUneCategorieDePanier($basket_category_name)
	{
		$this->entityCreationContext->createBasketCategory($basket_category_name);
	}
	
	// WHEN
	
	/**
	 * @When j'ajoute la catégorie de panier :basket_category_name
	 */
	public function jajouteLaCategorieDePanier($basket_category_name) {
		$crawler = $this->entityCreationContext->createBasketCategory($basket_category_name, "placeholder.png");
		$this->commonContext->updateViewMessage($crawler, "error-submit-basket-category");
	}
	
	/**
	 * @When j'ajoute la catégorie de panier :basket_category_name sans ajouter d'image
	 */
	public function jajouteLaCategorieDePanierSansAjouterDimage($basket_category_name) {
		$cli = $this->entityCreationContext->createBasketCategory($basket_category_name);
	}
	
 	/**
     * @When je supprime la catégorie de panier :basket_category_name
     */
    public function jeSupprimeLaCategorieDePanier($basket_category_name) {
    	
    	$crawler = $this->client->request('GET', '/basket_category');
    	// Filter to find the correct onclick attribute for the basket category.
    	$filter = "[onclick*=\"deleteCategory('". $basket_category_name ."',\"]";
    	 
    	$basketCategoryId = $this->utilContext->getItemCardId($crawler, $filter);
    	 
    	// With the basket category ID we can now delete it.
    	$this->client->request('DELETE', '/basket_category/'.$basketCategoryId);
    	$this->commonContext->updateJsonMessage(json_decode($this->client->getResponse()->getContent(), true));
    }

    /**
     * @Given il y a un panier :basket_name dans la catégorie :basket_category_name
     */
    public function ilYAUnPanierDansLaCategorie($basket_name, $basket_category_name)
    {
    	$this->entityCreationContext->createBasketFromScratch($basket_name, $basket_category_name);
    }
	
	// THEN
	
	/**
	 * @Then il y a une catégorie de panier :basket_category_name
	 */
	public function ilYAUneCategorieDePanier($basket_category_name)	{
		$crawler = $this->client->request('GET', '/basket_category');
		$basket_category_count = $this->utilContext->getItemCardCount($crawler, $basket_category_name);
		$this->assertEquals(1, $basket_category_count, "Basket category '". $basket_category_name ."' count is incorrect");
	}
	
	/**
	 * @Then il y a une catégorie de panier :basket_category_name avec l'image par défaut
	 */
	public function ilYAUneCategorieDePanierAvecLimageParDefaut($basket_category_name)
	{
		$crawler = $this->client->request('GET', '/basket_category');
		$imagePath = $crawler->filter('[data-name="'. $basket_category_name .'"] > img')->attr("src");
		$this->assertEquals("images/placeholder.png", $imagePath, "La catégorie de panier ne s'affiche pas avec l'image par défaut.");
	}
	
	/**
	 * @Then il n'y a plus de catégorie de panier :basket_category_name
	 */
	public function ilNyAPlusDeCategorieDePanier($basket_category_name) {
		$crawler = $this->client->request('GET', '/basket_category');
		$basket_category_count = $this->utilContext->getItemCardCount($crawler, $basket_category_name);
		$this->assertEquals(0, $basket_category_count, "Basket category '". $basket_category_name ."' count is incorrect");
	}
	
}