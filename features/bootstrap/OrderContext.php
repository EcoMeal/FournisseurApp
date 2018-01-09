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
		//TODO clear orders
		$this->order_id = null;
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
		
		$order = array(
				"username" => "",
				"order_time" => "",
				"delivery_time" => $this->delivery_time,
				"content" => json_encode(array($this->basket_id))
		);
		$this->client->request(
				"POST", //Methode
				"/api/basket_order", //URI 
				array(), //Parametres
				array(), //Fichiers
				array("Content-Type" => "application/json"), //Headers
				json_encode($order)); // Contenu
		
		$response = $this->client->getResponse();
		$data = json_decode($response->getContent());
		
		$this->order_id = $data->order_id;
	}
	
	/**
	 * @Then le système me retourne un numéro de commande
	 */
	public function leSystemeMeRetourneUnNumeroDeCommande()
	{
		$this->assertNotNull($this->order_id);
	}
	
}