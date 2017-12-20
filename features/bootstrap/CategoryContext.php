<?php

$_SERVER['KERNEL_DIR'] = __DIR__ . '/../../app/';
use Behat\Behat\Context\Context;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Behat\Hook\Scope\AfterScenarioScope;

class CategoryContext extends WebTestCase implements Context {
	
	/* CONTEXTES UTILITAIRES */
	
	//Creation d'entités (paniers, produits, ...)
	private $entityCreationContext;
	
	//Fonctions utiles (par exemple compter des items sur une page)
	private $utilContext;
	
	//Client pour les requêtes
	private $client;
		
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
	
	/**
	 * @AfterScenario
	 *
	 * Nettoie la BDD après chaque test.
	 * */
	public function after()
	{
		// Clean all the categories.
		$this->client->request('GET', '/category/clean');
	}
	
	// FEATURES
	
	/**
	 * @Given il n'y a aucune categorie dans l'application
	 */
	public function ilNyAAucuneCategorieDansLapplication()
	{
		$crawler = $this->client->request('GET', '/category');
	
		// By default there is already one card for the category creation
		// which has the same class
		$this->assertEquals(1, $crawler->filter('.card-image-label')->count(),
				"A category already exist before the test run");
	}
	
	/**
	 * @When j'ajoute la categorie :category_name dans l'application
	 */
	public function jajouteLaCategorieDansLapplication($category_name)
	{
		$this->entityCreationContext->createProductCategory($category_name);
	}
	
	/**
	 * @Then il y a une categorie :category_name dans l'application
	 */
	public function ilYAUneCategorieDansLapplication($category_name)
	{
		$crawler = $this->client->request('GET', '/category');
	
		$category_count = $this->utilContext->getItemCardCount($crawler, $category_name);
	
		$this->assertEquals(1, $category_count, "Category '". $category_name ."' count is incorrect");
	}
	
	/**
	 * @Given il existe la categorie :category_name dans l'application
	 */
	public function ilExisteLaCategorieDansLapplication($category_name)
	{
		$this->entityCreationContext->createProductCategory($category_name);
	}
	
	/**
	 * @When je supprime la categorie :category_name dans l'application
	 */
	public function jeSupprimeLaCategorieDansLapplication($category_name)
	{
		$crawler = $this->client->request('GET', '/category');
		// Filter to find the correct onclick attribute for the product.
		$filter = "[onclick*=\"deleteCategory('". $category_name ."',\"]";
	
		$categoryID =  $this->utilContext->getItemCardId($crawler, $filter);
		 
		// With the category ID we can now delete it.
		$this->client->request('DELETE', '/category/'.$categoryID);
	}
	
	/**
	 * @Then la categorie :category_name n'est plus affichée dans l'application
	 */
	public function laCategorieNestPlusAfficheeDansLapplication($category_name)
	{
		$crawler = $this->client->request('GET', '/category');
	
		$category_count = $this->utilContext->getItemCardCount($crawler, $category_name);
	
		$this->assertEquals(0, $category_count, "Category '". $category_name ."' count is incorrect");
	}
	
	/**
	 * @Given il existe la categorie :category_name
	 */
	public function ilExisteLaCategorie($category_name)
	{
		$this->entityCreationContext->createProductCategory($category_name);
	}
	
	
	/**
	 * @When je crée une category :category_name dans l'application
	 */
	public function jeCreeUneCategoryDansLapplication($category_name)
	{
		$this->entityCreationContext->createProductCategory($category_name);
	}
	
	/**
	 * @Then la categorie :category_name n'est pas crée car elle existe deja
	 */
	public function laCategorieNestPasCreeCarElleExisteDeja($category_name)
	{
		$crawler = $this->client->request('GET', '/category');
	
		$category_count = $this->utilContext->getItemCardCount($crawler, $category_name);
		$this->assertEquals(1, $category_count, "Category '". $category_name ."' count is incorrect");
	}
	
	/**
	 * @Given je cree la categorie :category_name avec une image
	 */
	public function jeCreeLaCategorieAvecUneImage($category_name)
	{
		$this->entityCreationContext->createProductCategory($category_name, "placeholder.png");
	}
	
	/**
	 * @Then la categorie :category_name s'affiche avec son image
	 */
	public function laCategorieSafficheAvecSonImage($category_name)
	{
		$crawler = $this->client->request('GET', '/category');
	
		$imagePathRaw = $crawler->filter("[onclick*=\"deleteCategory('". $category_name ."',\"] + img")->attr("src");
		$match_pattern = "/uploads/images/";
		$imagePath = substr($imagePathRaw, 0, strlen($match_pattern));
	
		$this->assertEquals($match_pattern, $imagePath, "La catégorie ne "
				. "s'affiche pas avec son image.");
	}
	
	/**
	 * @Given je cree la categorie :category_name sans image
	 */
	public function jeCreeLaCategorieSansImage($category_name)
	{
		$this->entityCreationContext->createProductCategory($category_name);
	}
	
	/**
	 * @Then la categorie :category_name s'affiche avec l'image par défaut
	 */
	public function laCategorieSafficheAvecLimageParDefaut($category_name)
	{
		$crawler = $this->client->request('GET', '/category');
	
		$imagePath = $crawler->filter("[onclick*=\"deleteCategory('". $category_name ."',\"] + img")->attr("src");
	
		$this->assertEquals("images/placeholder.png", $imagePath, "La catégorie ne"
				. "s'affiche pas avec l'image par default.");
	}
	
	/**
	 * @Given il existe une categorie :product_category_name utilisé par un produit
	 */
	public function ilExisteUneCategorieUtiliseParUnProduit($product_category_name)
	{
		$this->entityCreationContext->createProductFromScratch("test", NULL, $product_category_name);
	}
	
	/**
	 * @When j'essaie de supprimer la categorie :category_name
	 */
	public function jessaieDeSupprimerLaCategorie($category_name)
	{
		$crawler = $this->client->request('GET', '/category');
		// Filter to find the correct onclick attribute for the product.
		$filter = "[onclick*=\"deleteCategory('". $category_name ."',\"]";
		 
		$categoryID = $this->utilContext->getItemCardId($crawler, $filter);
		 
		// With the category  ID we can now delete it.
		$this->client->request('DELETE', '/category/'.$categoryID);
		$this->commonContext->setMessage(json_decode($this->client->getResponse()->getContent(), true));
	}
	
}