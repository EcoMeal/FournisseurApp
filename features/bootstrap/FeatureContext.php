<?php
// To avoid the kernel exception due to WebtestCase...
$_SERVER['KERNEL_DIR'] = __DIR__ . '/../../app/';
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
     * @When j'ajoute la categorie :cate dans l'application
     */
    public function jajouteLaCategorieDansLapplication($cate)
    {
        $this->createProductCategory($cate);
    }
    
    /**
     * @Then il y a une categorie :cate dans l'application
     */
    public function ilYAUneCategorieDansLapplication($cate)
    {
        $crawler = $this->client->request('GET', '/category');
	
        $category_count = $this->getItemCardCount($crawler, $cate); 
        
	$this->assertEquals(1, $category_count, "Category '". $cate ."' count is incorrect");
    }
    
    /**
     * @Given il existe la categorie :cate dans l'application
     */
    public function ilExisteLaCategorieDansLapplication($cate)
    {  
      $this->createProductCategory($cate);  
    }

    /**
     * @When je supprime la categorie :cate dans l'application
     */
    public function jeSupprimeLaCategorieDansLapplication($cate)
    {
        $crawler = $this->client->request('GET', '/category');
        // Filter to find the correct onclick attribute for the product.
        $filter = "[onclick*=\"deleteCategory('". $cate ."',\"]";       

        $categoryID =  $this->getItemCardId($crawler, $filter);             
       
        // With the category ID we can now delete it.
        $this->client->request('DELETE', '/category/'.$categoryID);
    }

    /**
     * @Then la categorie :cate n'est plus affichée dans l'application
     */
    public function laCategorieNestPlusAfficheeDansLapplication($cate)
    {
        $crawler = $this->client->request('GET', '/category');
	
        $category_count = $this->getItemCardCount($crawler, $cate);  
        
	$this->assertEquals(0, $category_count, "Category '". $cate ."' count is incorrect");
    }
    
    /**
     * @Given il existe la categorie test
     */
    public function ilExisteLaCategorieTest()
    {
     $this->createProductCategory("test");
    }

    
    /**
     * @When je crée une category test dans l'application
     */
    public function jeCreeUneCategoryTestDansLapplication()
    {
      $this->createProductCategory("test");
    }

    /**
     * @Then la categorie test n'est pas crée car elle existe deja
     */
    public function laCategorieTestNestPasCreeCarElleExisteDeja()
    {
      $cate = "test";    
      $crawler = $this->client->request('GET', '/category');
	
      $category_count = $this->getItemCardCount($crawler, $cate);  
      $this->assertEquals(1, $category_count, "Category '". $cate ."' count is incorrect");
    }
    
     /**
     * @Given je cree la categorie avec une image
     */
    public function jeCreeLaCategorieAvecUneImage()
    {
        $this->createProductCategoryWithImage("test", "placeholder.png");
    }

    /**
     * @Then la categorie s'affiche avec son image
     */
    public function laCategorieSafficheAvecSonImage()
    {
        $crawler = $this->client->request('GET', '/category');
        
        $imagePathRaw = $crawler->filter("[onclick*=\"deleteCategory('test',\"] + img")->attr("src");
        $match_pattern = "/uploads/images/";
        $imagePath = substr($imagePathRaw, 0, strlen($match_pattern));
        
        $this->assertEquals($match_pattern, $imagePath, "La catégorie ne "
                . "s'affiche pas avec son image.");
    }

    /**
     * @Given je cree la categorie sans image
     */
    public function jeCreeLaCategorieSansImage()
    {
        $this->createProductCategory("test");
    }

    /**
     * @Then la categorie s'affiche avec l'image par défaut
     */
    public function laCategorieSafficheAvecLimageParDefaut()
    {
        $crawler = $this->client->request('GET', '/category');
        
        $imagePath = $crawler->filter("[onclick*=\"deleteCategory('test',\"] + img")->attr("src");
        
        $this->assertEquals("images/placeholder.png", $imagePath, "La catégorie ne"
                . "s'affiche pas avec l'image par default.");
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
     * @Given il existe une catégorie dans l'application.
     */
    public function ilExisteUneCategorieDansLapplication()
    {
       $this->createProductCategory("test");       
    }
    
    /**
     * @When j'ajoute le produit :product dans l'application
     */
    public function jajouteLeProduitDansLapplication($product)
    {
       $this->createProduct($product);
    }

    /**
     * @Then il y a un produit :product dans l'application
     */
    public function ilYAUnProduitDansLapplication($product)
    {    
	$crawler = $this->client->request('GET', '/product');
        
        $product_count = $this->getItemCardCount($crawler, $product);     
        
	$this->assertEquals(1, $product_count, "The product '". $product."' count is incorrect.");    
    }

    /**
     * @Given il existe le produit :product dans l'application
     */
    public function ilExisteLeProduitDansLapplication($product)
    {
       $this->createProductFromScratch($product);      
    }

    /**
     * @When je supprime le produit :product dans l'application
     */
    public function jeSupprimeLeProduitDansLapplication($product)
    {
        $crawler = $this->client->request('GET', '/product');
        // Filter to find the correct onclick attribute for the product.
        $filter = "[onclick*=\"deleteProduct('". $product ."',\"]";
       
        $productID = $this->getItemCardId($crawler, $filter);
       
        // With the product ID we can now delete it.
        $this->client->request('DELETE', '/product/'.$productID);
    }

    /**
     * @Then le produit :product n'est plus affichée dans l'application
     */
    public function leProduitNestPlusAfficheeDansLapplication($product)
    {
        $crawler = $this->client->request('GET', '/product');
        
        $product_count = $this->getItemCardCount($crawler, $product);     
        
	$this->assertEquals(0, $product_count, "The product '". $product."' count is incorrect.");    
    }
    
    /**
     * @Given il existe le produit test
     */
    public function ilExisteLeProduitTest()
    {
        $this->createProductFromScratch("test");
    }

    /**
     * @When je crée un produit test dans l'application
     */
    public function jeCreeUnProduitTestDansLapplication()
    {
         $this->createProductFromScratch("test");
    }

    /**
     * @Then le produit test n'est pas crée car il existe deja
     */
    public function leProduitTestNestPasCreeCarIlExisteDeja()
    {
        $crawler = $this->client->request('GET', '/product');
        
        $product_count = $this->getItemCardCount($crawler, "test");     
        
	$this->assertEquals(1, $product_count, "The product 'test' count is incorrect.");
    }

    /**
     * @Given je cree le produit avec une image
     */
    public function jeCreeLeProduitAvecUneImage()
    {
       $this->createProductFromScratchWithImage("test", "placeholder.png");
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
     * @Then le produit s'affiche avec son image
     */
    public function leProduitSafficheAvecSonImage()
    {
       
        $crawler = $this->client->request('GET', '/product');
        
        $imagePathRaw = $crawler->filter("[onclick*=\"deleteProduct('test',\"] + img")->attr("src");
        $match_pattern = "/uploads/images/";
        $imagePath = substr($imagePathRaw, 0, strlen($match_pattern));
        
        $this->assertEquals($match_pattern, $imagePath, "Le produit ne "
                . "s'affiche pas avec son image.");
    }

    /**
     * @Given je cree le produit sans image
     */
    public function jeCreeLeProduitSansImage()
    {
        $this->createProductFromScratch("test");
    }

    /**
     * @Then le produit s'affiche avec l'image par défaut
     */
    public function leProduitSafficheAvecLimageParDefaut()
    {
        $crawler = $this->client->request('GET', '/product');
        
        $imagePath = $crawler->filter("[onclick*=\"deleteProduct('test',\"] + img")->attr("src");
        
        $this->assertEquals("images/placeholder.png", $imagePath, "Le produit ne"
                . "s'affiche pas avec l'image par default.");
    }
    
    
    /**
     * @Given il existe un produit :test utilisé dans un panier
     */
    public function ilExisteUnProduitUtiliseDansUnPanier($product)
    {
        $this->createProductFromScratch($product);
        $this->createBasketCategory("test_basket_category");
        $this->createBasket("test_basket");
    }

    /**
     * @When j'essaie de supprimer le produit :product
     */
    public function jessaieDeSupprimerLeProduit($product)
    {
        $crawler = $this->client->request('GET', '/product');
        // Filter to find the correct onclick attribute for the product.
        $filter = "[onclick*=\"deleteProduct('". $product ."',\"]";
       
        $productID = $this->getItemCardId($crawler, $filter);
       
        // With the product ID we can now delete it.
        $this->client->request('DELETE', '/product/'.$productID);
        $this->jsonMessage = json_decode($this->client->getResponse()->getContent(), true); 
                
    }
    
    /**
     * @Then l'application renvoie un message d'erreur :arg1
     */
    public function lapplicationRenvoieUnMessageDerreur($errorMessage)
    {
        $this->assertEquals($errorMessage, json_encode($this->jsonMessage));
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
     * @When j'ajoute la catégorie de panier :basketCate dans l'application
     */
    public function jajouteLaCategorieDePanierDansLapplication($basketCate)
    {
        $crawler = $this->client->request('GET', '/basket_category');
      
        $form = $crawler->selectButton('Valider')->form();
        // Set the task values
        $form->setValues(array('appbundle_basketcategory[name]' => $basketCate));
        // submit the form
        $this->client->submit($form);
    }

    /**
     * @Then il y a une catégorie de panier :basketCate dans l'application
     */
    public function ilYAUneCategorieDePanierDansLapplication($basketCate)
    {
        $crawler = $this->client->request('GET', '/basket_category');
	
        $basket_category_count = $this->getItemCardCount($crawler, $basketCate); 
        
	$this->assertEquals(1, $basket_category_count, "Basket category '". $basketCate ."' count is incorrect");
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
     * @When je crée un panier :basket dans l'application
     */
    public function jeCreeUnPanierDansLapplication($basket)
    {
        $this->createBasketFromScratch($basket);
    }

    /**
     * @Then le panier :basket est affiché
     */
    public function lePanierEstAffiche($basket)
    {
        $crawler = $this->client->request('GET', '/basket');
	
        $basket_count = $this->getItemCardCount($crawler, $basket); 
        
	$this->assertEquals(1, $basket_count, "Basket '". $basket ."' count is incorrect");
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
    
    public function createBasketFromScratch($basket) {
        
        //product category
        $this->createProductCategory("test_product_category");
        
        //product
        $this->createProduct("test_product");
        
        //basket category
        $this->createBasketCategory("test_basket_category");
        
        //basket
        $this->createBasket($basket);
        
    }

    public function createProductFromScratch($product)
    {
        // First I create a category.
        $this->createProductCategory("test");
        
        // Then I create a product.
        $this->createProduct($product);
    }
    
     public function createProductFromScratchWithImage($product, $productImage)
    {
        // First I create a category.
        $this->createProductCategory("test");
        
        // Then I create a product.
        $this->createProductWithImage($product, $productImage);
    }
    
    public function createProductCategory($category)
    {
      $crawler = $this->client->request('GET', '/category');
      
      $form = $crawler->selectButton('Valider')->form();
      // Set the task values
      $form->setValues(array('appbundle_category[name]' => $category));
      // submit the form
      $this->client->submit($form);
    }
    
    public function createProductCategoryWithImage($category, $imagePath)
    {
        $image = new UploadedFile("web/images/".$imagePath, "test.png", "image/png");
        $crawler = $this->client->request('GET', '/category');

        $form = $crawler->selectButton('Valider')->form();
        // Set the task values
        $form->setValues(array('appbundle_category[name]' => $category));
        $form->setValues(array('appbundle_category[imagePath]' => $image));
        // submit the form
        $this->client->submit($form);
    }
    
    public function createProduct($product)
    {
      $crawler = $this->client->request('GET', '/product');
      
      $form = $crawler->selectButton('Valider')->form();
      // Set the task values
      $form->setValues(array('appbundle_product[name]' => $product));
      // submit the form
      $this->client->submit($form);
    }
    
    public function createProductWithImage($product, $imagePath)
    {
      $image = new UploadedFile("web/images/".$imagePath, "test.png", "image/png");
      $crawler = $this->client->request('GET', '/product');
      
      $form = $crawler->selectButton('Valider')->form();
      // Set the task values
      $form->setValues(array('appbundle_product[name]' => $product));
      $form->setValues(array('appbundle_product[imagePath]' => $image));
      // submit the form
      $this->client->submit($form);
    }
    
    public function createBasketCategory($basketCategory) {
        $crawler = $this->client->request('GET', '/basket_category');
        $form = $crawler->selectButton('Valider')->form();
        // Set the task values
        $form->setValues(array('appbundle_basketcategory[name]' => $basketCategory));
        // submit the form
        $this->client->submit($form);
    }
    
    public function createBasket($basket) {
        $crawler = $this->client->request('GET', '/basket');
        $form = $crawler->selectButton('Valider')->form();
        // Set the task values
        $form->setValues(array('appbundle_basket[name]' => $basket));
        $form->setValues(array('appbundle_basket[price]' => 10));
        // submit the form
        $this->client->submit($form);
    }
    
    
    // Utils functions ---------------------------------------------------------
            

   // Clean up
    
    /** @AfterScenario */
    public function after(AfterScenarioScope $scope)
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
    
}
