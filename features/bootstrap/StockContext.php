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
	
	// FEATURES
	
	/**
	 * @Given la quantité de :produit est :quantite
	 */
	public function laQuantiteDeEst($produit, $quantite)
	{
		$this->entityCreationContext->createProductFromScratch($produit);
		$this->entityCreationContext->setProductStock($produit, $quantite);
	}
	
	/**
	 * @When je met à jour la quantité de :produit à :quantite
	 */
	public function jeMetAJourLaQuantiteDeA($produit, $quantite)
	{
		$reponse = $this->entityCreationContext->setProductStock($produit, $quantite);
                echo "Reponse: \n";
                echo $reponse;
                echo "\n";
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