<?php
// To avoid the kernel exception due to WebtestCase...
$_SERVER['KERNEL_DIR'] = __DIR__ . '/../../app/';
use AppBundle\Entity\Product;
use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Behat\Hook\Scope\AfterScenarioScope;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Defines application features from the specific context.
 */
class ProductContext extends WebTestCase implements Context
{
    /* CONTEXTES UTILITAIRES */
	
	//Creation d'entités (paniers, produits, ...)
	private $entityCreationContext;
	
	//Fonctions utiles (par exemple compter des items sur une page)
	private $utilContext;
	
	//Fonctions communes à plusieurs features
	private $commonContext;
	
	//Client pour les requêtes
	private $client;
    
    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     */
    public function __construct()
    {
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
     * Nettoie la BDD après chaque test
     * */
    public function after()
    {
    	// Clean all the products.
		$this->client->request('GET', '/product/clean');
	
		// Clean all the categories.
		$this->client->request('GET', '/category/clean');
			
		// Clean all the baskets
		$this->client->request('GET', '/basket/clean');
			
		// Clean all the basket categories.
		$this->client->request('GET', '/basket_category/clean');
    	 
    }
    
    // FEATURES
    
    /**
     * @Given il n'y a aucun produit dans l'application
     */
    public function ilNyAAucunProduitDansLapplication()
    {   
		$crawler = $this->client->request('GET', '/product');
        // By default there is already one card for the product creation 
        // which has the same class
		$this->assertEquals(1, $crawler->filter('.card-image-label')->count(),
                "There is already a product in the application before the test run.");
    }
    
    /**
     * @Given il existe une catégorie :category_name dans l'application.
     */
    public function ilExisteUneCategorieDansLapplication($category_name)
    {
       $this->entityCreationContext->createProductCategory($category_name);       
    }
    
    /**
     * @When j'ajoute le produit :product_name dans l'application
     */
    public function jajouteLeProduitDansLapplication($product_name)
    {
       $this->entityCreationContext->createProduct($product_name);
    }

    /**
     * @Then il y a un produit :product_name dans l'application
     */
    public function ilYAUnProduitDansLapplication($product_name)
    {    
		$crawler = $this->client->request('GET', '/product');
        $product_count = $this->utilContext->getItemCardCount($crawler, $product_name);     
		$this->assertEquals(1, $product_count, "The product '". $product_name."' count is incorrect.");    
    }

    /**
     * @Given il existe le produit :product_name dans l'application
     */
    public function ilExisteLeProduitDansLapplication($product_name)
    {
       $this->entityCreationContext->createProductFromScratch($product_name);      
    }

    /**
     * @When je supprime le produit :product_name dans l'application
     */
    public function jeSupprimeLeProduitDansLapplication($product_name)
    {
        $crawler = $this->client->request('GET', '/product');
        // Filter to find the correct onclick attribute for the product.
        $filter = "[onclick*=\"deleteProduct('". $product_name ."',\"]";
       
        $productID = $this->utilContext->getItemCardId($crawler, $filter);
       
        // With the product ID we can now delete it.
        $this->client->request('DELETE', '/product/'.$productID);
    }

    /**
     * @Then le produit :product_name n'est plus affichée dans l'application
     */
    public function leProduitNestPlusAfficheeDansLapplication($product_name)
    {
        $crawler = $this->client->request('GET', '/product');
        $product_count = $this->utilContext->getItemCardCount($crawler, $product_name);     
		$this->assertEquals(0, $product_count, "The product '". $product_name."' count is incorrect.");    
    }
    
    /**
     * @Given il existe le produit :product_name
     */
    public function ilExisteLeProduit($product_name)
    {
        $this->entityCreationContext->createProductFromScratch($product_name);
    }

    /**
     * @When je crée un produit :product_name dans l'application
     */
    public function jeCreeUnProduitDansLapplication($product_name)
    {
         $this->entityCreationContext->createProductFromScratch($product_name);
    }

    /**
     * @Then le produit :product_name n'est pas crée car il existe deja
     */
    public function leProduitNestPasCreeCarIlExisteDeja($product_name)
    {
        $crawler = $this->client->request('GET', '/product');
        $product_count = $this->utilContext->getItemCardCount($crawler, $product_name);     
		$this->assertEquals(1, $product_count, "The product 'test' count is incorrect.");
    }

    /**
     * @Given je cree le produit :product_name avec une image
     */
    public function jeCreeLeProduitAvecUneImage($product_name)
    {
       $this->entityCreationContext->createProductFromScratch($product_name, "placeholder.png");
    }

    /**
     * @Then le produit :product_name s'affiche avec son image
     */
    public function leProduitSafficheAvecSonImage($product_name)
    {
        $crawler = $this->client->request('GET', '/product');
        $imagePathRaw = $crawler->filter("[onclick*=\"deleteProduct('". $product_name ."',\"] + img")->attr("src");
        $match_pattern = "/uploads/images/";
        $imagePath = substr($imagePathRaw, 0, strlen($match_pattern));
        $this->assertEquals($match_pattern, $imagePath, "Le produit ne "
                . "s'affiche pas avec son image.");
    }

    /**
     * @Given je cree le produit :product_name sans image
     */
    public function jeCreeLeProduitSansImage($product_name)
    {
        $this->entityCreationContext->createProductFromScratch($product_name);
    }

    /**
     * @Then le produit :product_name s'affiche avec l'image par défaut
     */
    public function leProduitSafficheAvecLimageParDefaut($product_name)
    {
        $crawler = $this->client->request('GET', '/product');
        $imagePath = $crawler->filter("[onclick*=\"deleteProduct('". $product_name ."',\"] + img")->attr("src");
        $this->assertEquals("images/placeholder.png", $imagePath, "Le produit ne"
                . "s'affiche pas avec l'image par default.");
    }
    
    
    /**
     * @Given il existe un produit :product_name utilisé dans un panier
     */
    public function ilExisteUnProduitUtiliseDansUnPanier($product_name)
    {
        $this->entityCreationContext->createProductFromScratch($product_name);
        $this->entityCreationContext->createBasketCategory("test_basket_category");
        $crawler = $this->client->request('GET', '/product');
        // Filter to find the correct onclick attribute for the given product.
        $filter = "[onclick*=\"deleteProduct('". $product_name ."',\"]";
        $productID = $this->utilContext->getItemCardId($crawler, $filter);
        $this->entityCreationContext->createBasket("test_basket", array($productID));
    }

    /**
     * @When j'essaie de supprimer le produit :product_name
     */
    public function jessaieDeSupprimerLeProduit($product_name)
    {
        $crawler = $this->client->request('GET', '/product');
        // Filter to find the correct onclick attribute for the product.
        $filter = "[onclick*=\"deleteProduct('". $product_name ."',\"]";
        $productID = $this->utilContext->getItemCardId($crawler, $filter);
        // With the product ID we can now delete it.
        $this->client->request('DELETE', '/product/'.$productID);
        $this->commonContext->updateJsonMessage(json_decode($this->client->getResponse()->getContent(), true)); 
    }
	  	
}