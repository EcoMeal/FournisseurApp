<?php

$_SERVER['KERNEL_DIR'] = __DIR__ . '/../../app/';
use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Defines application features from the specific context.
 */
class OrderContext extends WebTestCase implements Context {
	
	/* CONTEXTES UTILITAIRES */
	
	//Creation d'entités (paniers, produits, ...)
	private $entityCreationContext;
	
	//Fonctions utiles (par exemple compter des items sur une page)
	private $utilContext;
	
	private $client;
	
	//Le timestamp pour l'horaire de livraison
	private $delivery_time;
	
	//L'identifiant du panier commandé
	private $basket_id;
	
	//L'identifiant de la commande
	private $order_id;
	
    // The crawler to check data within an html page.
    private $crawler;
        
	public function __construct() {
		$this->client = static::createClient();
	}
	
	/** @BeforeScenario */
	public function getSubcontexts(BeforeScenarioScope $scope) {
		$environment = $scope->getEnvironment();
		$this->entityCreationContext = $environment->getContext("EntityCreationContext");
		$this->utilContext = $environment->getContext("UtilContext");
	}
	
	/** @AfterScenario */
	public function after() {
  	    $this->order_id = null;
        $this->crawler = null;
        $this->basket_id = null;
	}
	
	// FEATURES
	
	/**
	 * @Given j'ai choisi mon horaire de livraison
	 */
	public function jaiChoisiMonHoraireDeLivraison()
	{
		$this->delivery_time = "12345";
	}
	
	/**
	 * @Given j'ai choisi un panier disponible
	 */
	public function jaiChoisiUnPanierDisponible()
	{
		// Créer un panier
		$this->entityCreationContext->createBasketFromScratch("Panier délicieux");
		//TODO ajouter du stock au contenu du panier
		// Récupérer l'id du panier
		$this->client->request("GET", "/api/basket");
		$response = $this->client->getResponse();
		$baskets = json_decode($response->getContent(), true);
		$this->basket_id = $baskets[0]['id'];
	}
	
	/**
	 * @When je valide ma commande
	 */
	public function jeValideMaCommande()
	{
		$this->order_id = $this->entityCreationContext->createOrder(array($this->basket_id),
                        $this->delivery_time);
	}
	     
        /**
         * @Given il existe une commande
         */
        public function ilExisteUneCommande()
        {
            
           $productName = "testProduct";
           // Create the basket that will be ordered.
           $this->entityCreationContext->createBasketFromScratch("Panier délicieux",
                   "testBasketCategory", $productName);
           
           // Set the stock of the product.
            $this->entityCreationContext->setProductStock($productName, 1);
            
            
            // Récupérer l'id du panier
            $this->client->request("GET", "/api/basket");
            $response = $this->client->getResponse();
            $baskets = json_decode($response->getContent(), true);
            $this->basket_id = $baskets[0]['id'];
            
            // Create the order.
            $this->order_id = $this->entityCreationContext->createOrder(array($this->basket_id),"12345");
                    
        }

        /**
         * @When je vais sur la page des commandes
         */
        public function jeVaisSurLaPageDesCommandes()
        {
            $this->crawler = $this->client->request("GET", "/orders");
        }

        /**
         * @Then le système m'affiche la commande
         */
        public function leSystemeMafficheLaCommande()
        {
            $orderId = $this->order_id;
            
            // Find the commmand on the page.
            $orderCount = $this->crawler->filter(".card-title-command>.card-title-block:first-child")->reduce(
				function ($node, $i) use ($orderId) {
                                    // If the item text match the given text, keep it in the node list.
                                    // Split the string to get the words.
                                    $text = explode(' ', trim($node->text()));
                                    // The basket order is the last word from the node text.
                                    $currentNodeOrderId = array_pop($text);                      
                
                                    if (strcmp($currentNodeOrderId, $orderId) == 0) {
					return true;
                                    } else {
					return false;
                                    }
				}
                        // return the number of item in the list.
			)->count();
            // There is a card with the command id.
            $this->assertEquals(1, $orderCount, "The order count is incorrect.");    
        }
        
	/**
	 * @Then le système me retourne un numéro de commande
	 */
	public function leSystemeMeRetourneUnNumeroDeCommande()
	{
		$this->assertNotNull($this->order_id);
	}
	
}