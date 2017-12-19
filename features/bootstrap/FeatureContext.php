<?php
// To avoid the kernel exception due to WebtestCase...
$_SERVER['KERNEL_DIR'] = __DIR__ . '/../../app/';
use AppBundle\Entity\Product;
use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\AfterScenarioScope;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Defines application features from the specific context.
 */
class FeatureContext extends WebTestCase implements Context
{
    private $client = null;
    
    /*
     * Objet contenant les messages de retour des appels aux contr�leurs
     * quand ceux-ci renvoient du JSON
     */
    private $jsonMessage;
    
    private $errorMessage;
    
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
    
    // Category Feature --------------------------------------------------------
    
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
        $this->createProductCategory($category_name);
    }
    
    /**
     * @Then il y a une categorie :category_name dans l'application
     */
    public function ilYAUneCategorieDansLapplication($category_name)
    {
        $crawler = $this->client->request('GET', '/category');
	
        $category_count = $this->getItemCardCount($crawler, $category_name); 
        
	$this->assertEquals(1, $category_count, "Category '". $category_name ."' count is incorrect");
    }
    
    /**
     * @Given il existe la categorie :category_name dans l'application
     */
    public function ilExisteLaCategorieDansLapplication($category_name)
    {  
      $this->createProductCategory($category_name);  
    }

    /**
     * @When je supprime la categorie :category_name dans l'application
     */
    public function jeSupprimeLaCategorieDansLapplication($category_name)
    {
        $crawler = $this->client->request('GET', '/category');
        // Filter to find the correct onclick attribute for the product.
        $filter = "[onclick*=\"deleteCategory('". $category_name ."',\"]";       

        $categoryID =  $this->getItemCardId($crawler, $filter);             
       
        // With the category ID we can now delete it.
        $this->client->request('DELETE', '/category/'.$categoryID);
    }

    /**
     * @Then la categorie :category_name n'est plus affichée dans l'application
     */
    public function laCategorieNestPlusAfficheeDansLapplication($category_name)
    {
        $crawler = $this->client->request('GET', '/category');
	
        $category_count = $this->getItemCardCount($crawler, $category_name);  
        
	$this->assertEquals(0, $category_count, "Category '". $category_name ."' count is incorrect");
    }
    
    /**
     * @Given il existe la categorie :category_name
     */
    public function ilExisteLaCategorie($category_name)
    {
     $this->createProductCategory($category_name);
    }

    
    /**
     * @When je crée une category :category_name dans l'application
     */
    public function jeCreeUneCategoryDansLapplication($category_name)
    {
      $this->createProductCategory($category_name);
    }

    /**
     * @Then la categorie :category_name n'est pas crée car elle existe deja
     */
    public function laCategorieNestPasCreeCarElleExisteDeja($category_name)
    {  
      $crawler = $this->client->request('GET', '/category');
	
      $category_count = $this->getItemCardCount($crawler, $category_name);  
      $this->assertEquals(1, $category_count, "Category '". $category_name ."' count is incorrect");
    }
    
     /**
     * @Given je cree la categorie :category_name avec une image
     */
    public function jeCreeLaCategorieAvecUneImage($category_name)
    {
        $this->createProductCategory($category_name, "placeholder.png");
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
        $this->createProductCategory($category_name);
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
        $this->createProductFromScratch("test", NULL, $product_category_name);
    }

    /**
     * @When j'essaie de supprimer la categorie :category_name
     */
    public function jessaieDeSupprimerLaCategorie($category_name)
    {
        $crawler = $this->client->request('GET', '/category');
        // Filter to find the correct onclick attribute for the product.
        $filter = "[onclick*=\"deleteCategory('". $category_name ."',\"]";
       
        $categoryID = $this->getItemCardId($crawler, $filter);
       
        // With the category  ID we can now delete it.
        $this->client->request('DELETE', '/category/'.$categoryID);
        $this->jsonMessage = json_decode($this->client->getResponse()->getContent(), true); 
    }


    // Category Feature --------------------------------------------------------

    
    // Product Feature ---------------------------------------------------------
    
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
       $this->createProductCategory($category_name);       
    }
    
    /**
     * @When j'ajoute le produit :product_name dans l'application
     */
    public function jajouteLeProduitDansLapplication($product_name)
    {
       $this->createProduct($product_name);
    }

    /**
     * @Then il y a un produit :product_name dans l'application
     */
    public function ilYAUnProduitDansLapplication($product_name)
    {    
	$crawler = $this->client->request('GET', '/product');
        
        $product_count = $this->getItemCardCount($crawler, $product_name);     
        
	$this->assertEquals(1, $product_count, "The product '". $product_name."' count is incorrect.");    
    }

    /**
     * @Given il existe le produit :product_name dans l'application
     */
    public function ilExisteLeProduitDansLapplication($product_name)
    {
       $this->createProductFromScratch($product_name);      
    }

    /**
     * @When je supprime le produit :product_name dans l'application
     */
    public function jeSupprimeLeProduitDansLapplication($product_name)
    {
        $crawler = $this->client->request('GET', '/product');
        // Filter to find the correct onclick attribute for the product.
        $filter = "[onclick*=\"deleteProduct('". $product_name ."',\"]";
       
        $productID = $this->getItemCardId($crawler, $filter);
       
        // With the product ID we can now delete it.
        $this->client->request('DELETE', '/product/'.$productID);
    }

    /**
     * @Then le produit :product_name n'est plus affichée dans l'application
     */
    public function leProduitNestPlusAfficheeDansLapplication($product_name)
    {
        $crawler = $this->client->request('GET', '/product');
        
        $product_count = $this->getItemCardCount($crawler, $product_name);     
        
	$this->assertEquals(0, $product_count, "The product '". $product_name."' count is incorrect.");    
    }
    
    /**
     * @Given il existe le produit :product_name
     */
    public function ilExisteLeProduit($product_name)
    {
        $this->createProductFromScratch($product_name);
    }

    /**
     * @When je crée un produit :product_name dans l'application
     */
    public function jeCreeUnProduitDansLapplication($product_name)
    {
         $this->createProductFromScratch($product_name);
    }

    /**
     * @Then le produit :product_name n'est pas crée car il existe deja
     */
    public function leProduitNestPasCreeCarIlExisteDeja($product_name)
    {
        $crawler = $this->client->request('GET', '/product');
        
        $product_count = $this->getItemCardCount($crawler, $product_name);     
        
	$this->assertEquals(1, $product_count, "The product 'test' count is incorrect.");
    }

    /**
     * @Given je cree le produit :product_name avec une image
     */
    public function jeCreeLeProduitAvecUneImage($product_name)
    {
       $this->createProductFromScratch($product_name, "placeholder.png");
    }

    /**
     * Placeholder function when no actions can be assigned. To keep the flow:
     * Given, When, Then.
     * @When void
     */
    public function void()
    {
      
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
        $this->createProductFromScratch($product_name);
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
        
        $this->createProductFromScratch($product_name);
        $this->createBasketCategory("test_basket_category");
        
        $crawler = $this->client->request('GET', '/product');
        // Filter to find the correct onclick attribute for the given product.
        $filter = "[onclick*=\"deleteProduct('". $product_name ."',\"]";
       
        $productID = $this->getItemCardId($crawler, $filter);
        
       
        $this->createBasket("test_basket", array($productID));
        
    }

    /**
     * @When j'essaie de supprimer le produit :product_name
     */
    public function jessaieDeSupprimerLeProduit($product_name)
    {
        $crawler = $this->client->request('GET', '/product');
        // Filter to find the correct onclick attribute for the product.
        $filter = "[onclick*=\"deleteProduct('". $product_name ."',\"]";
       
        $productID = $this->getItemCardId($crawler, $filter);
       
        // With the product ID we can now delete it.
        $this->client->request('DELETE', '/product/'.$productID);
        $this->jsonMessage = json_decode($this->client->getResponse()->getContent(), true); 
                
    }
    
    /**
     * @Then l'application renvoie un message d'erreur :errorMessage
     */
    public function lapplicationRenvoieUnMessageDerreur($errorMessage)
    {
        $this->assertEquals($errorMessage, $this->jsonMessage['error']);
    }
    
    
    // Product Feature ---------------------------------------------------------

    // Basket category Feature -------------------------------------------------
    
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
	
        $basket_category_count = $this->getItemCardCount($crawler, $basket_category_name); 
        
	$this->assertEquals(1, $basket_category_count, "Basket category '". $basket_category_name ."' count is incorrect");
    }
    
    // Basket category Feature -------------------------------------------------
    
    // Basket Feature ----------------------------------------------------------
    
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
        $this->createBasketFromScratch($basket_name);
    }

    /**
     * @Then le panier :basket_name est affiché
     */
    public function lePanierEstAffiche($basket_name)
    {
        $crawler = $this->client->request('GET', '/basket');
	
        $basket_count = $this->getItemCardCount($crawler, $basket_name); 
        
	$this->assertEquals(1, $basket_count, "Basket '". $basket_name ."' count is incorrect");
    }
    
    /**
     * @Given j’ai un panier :basket_name disponible
     */
    public function jaiUnPanierDisponible($basket_name)
    {
        $this->createBasketFromScratch($basket_name);
    }

    /**
     * @When j’ajoute un nouveau panier :basket_name
     */
    public function jajouteUnNouveauPanier($basket_name)
    {
        $crawler = $this->createBasketFromScratch($basket_name);
        $this->errorMessage = trim($crawler->filter(".alert-danger")->text());
    }

    /**
     * @Then j'obtiens une erreur :error
     */
    public function jobtiensUneErreur($error)
    {
        $this->assertEquals($error, $this->errorMessage, "The error message is incorrect or non existent");
    }
    
    // Basket Feature ----------------------------------------------------------
    
    // Utils functions ---------------------------------------------------------
    
    /**
     * 
     * @param type $crawler The crawler with the HTML DOM from the page we want loaded.
     * @param type $label Count the occurrences for the item with the given label
     * @return type The number of item with the given name that appears on the page 
     */
    public function getItemCardCount($crawler, $label)
    {
        return $crawler->filter('.card-image-label')->reduce(
                function ($node, $i) use($label) {
                    // If the item text match the given text, keep it in the node list.
                    if (strcmp(trim($node->text()), $label) == 0) {
                        return true;
                    } else {
                        return false;
                    }
                }
        // return the number of item in the list.
        )->count();
    }        
    
    public function getItemCardId($crawler, $filter)
    {
       // DEBUG echo "Filter = ".$filter;
       // On the js function, we can find the item ID.
       $node_attribute = $crawler->filter($filter)->attr("onclick");
       // DEBUG echo "Node attribute = ".$node_attribute;
       // The product ID is located in the 4th index.
        $itemID = explode("'", $node_attribute)[3];
        return $itemID;

    }
    
    public function createBasketFromScratch($basket_name,
            $basket_category_name = "testBasketCategory", $product_name = "testProduct",
            $product_category_name = "testProductCategory") {
        
        //product category
        $this->createProductCategory($product_category_name);
        
        //product
        $this->createProduct($product_name);
        
        //basket category
        $this->createBasketCategory($basket_category_name);
        
        //basket
        return $this->createBasket($basket_name);
        
    }

    public function createProductFromScratch($product_name, $imagePath = NULL,
            $product_category_name = "testProductCategory")
    {
        // First I create a category.
        $this->createProductCategory($product_category_name);
        
        // Then I create a product.
        return $this->createProduct($product_name, $imagePath);
    }
    
   
    public function createProductCategory($category, $imagePath = NULL)
    {
      $crawler = $this->client->request('GET', '/category');
      
      $form = $crawler->selectButton('Valider')->form();
      // Set the task values
      $form->setValues(array('appbundle_category[name]' => $category));
      
      if($imagePath != NULL){
          $image = new UploadedFile("web/images/".$imagePath, "test.png", "image/png");
          $form->setValues(array('appbundle_category[imagePath]' => $image));
      }
      // submit the form
      return $this->client->submit($form);
    }
 
    public function createProduct($product, $imagePath = NULL)
    {
      $crawler = $this->client->request('GET', '/product');
      
      $form = $crawler->selectButton('Valider')->form();
      // Set the task values
      $form->setValues(array('appbundle_product[name]' => $product));
      
      if($imagePath != NULL){
          $image = new UploadedFile("web/images/".$imagePath, "test.png", "image/png");
          $form->setValues(array('appbundle_product[imagePath]' => $image));
      }
      
      // submit the form
      return $this->client->submit($form);
    }
    
    public function createBasketCategory($basketCategory, $imagePath = NULL) {
        $crawler = $this->client->request('GET', '/basket_category');
        $form = $crawler->selectButton('Valider')->form();
        // Set the task values
        $form->setValues(array('appbundle_basketcategory[name]' => $basketCategory));
        
        if($imagePath != NULL){
          $image = new UploadedFile("web/images/".$imagePath, "test.png", "image/png");
          $form->setValues(array('appbundle_basketcategory[imagePath]' => $image));
        }
      
        // submit the form
        return $this->client->submit($form);
    }
    
    public function createBasket($basket, $product_list = NULL) {
        $crawler = $this->client->request('GET', '/basket');
        $form = $crawler->selectButton('Valider')->form();
        // Set the task values
        $form->setValues(array('appbundle_basket[name]' => $basket));
        $form->setValues(array('appbundle_basket[price]' => 10));
        if($product_list != NULL){
            $form->setValues(array('appbundle_basket[product_list]' => $product_list));
        }
        // submit the form
        return $this->client->submit($form);
    }
       
    // Utils functions ---------------------------------------------------------
            

   // Clean up
    
    /** @AfterScenario */
    public function after(AfterScenarioScope $scope)
    {
       // Clear the json message
       $this->jsonMessage = null;
       
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
    
}