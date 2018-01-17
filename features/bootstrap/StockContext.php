<?php
// To avoid the kernel exception due to WebtestCase...
$_SERVER['KERNEL_DIR'] = __DIR__ . '/../../app/';
use AppBundle\Entity\Product;
use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Defines application features from the specific context.
 */
class StockContext extends WebTestCase implements Context {
	
	/* CONTEXTES UTILITAIRES */
	
	//Creation d'entités (paniers, produits, ...)
	private $entityCreationContext;
	
	//Fonctions utiles (par exemple compter des items sur une page)
	private $utilContext;
	
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
	}
	
	// FEATURES
	
	/**
	 * @Given la quantité de :produit est :quantite
	 */
	public function laQuantiteDeEst($produit, $quantite)
	{
		$this->entityCreationContext->createProductFromScratch($produit);
                
                $data = array($produit => $quantite);
		$this->entityCreationContext->setProductStock($data);
	}

	
	/**
	 * @When je met à jour la quantité de :produit à :quantite
	 */
	public function jeMetAJourLaQuantiteDeA($produit, $quantite)
	{
                $data = array($produit => $quantite);
		$this->entityCreationContext->setProductStock($data);
	}
        
        /**
        * @When je met à jour la quantité de :product1 à :quantity1 et :product2 à :quantity2
        */
        public function jeMetAJourLaQuantiteDeAEtA($product1, $quantity1, $product2, $quantity2)
        {
            
            $data = array($product1 => $quantity1, $product2 => $quantity2);
            
            $this->entityCreationContext->setProductStock($data);
        }

	
	/**
	 * @Then la quantité de :produit est de :quantite
	 */
	public function laQuantiteDeEstDe($produit, $quantite)
	{
		$crawler = $this->client->request('GET', '/stock');
		
		$id = $crawler->filter('[data-name="'. $produit .'"]')->attr("data-id");
		
		$amount = $crawler->filter('input[id="product'. $id .'"]')->attr("value");
	
		$this->assertEquals($quantite, $amount);
	}
	
}