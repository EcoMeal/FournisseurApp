<?php
// To avoid the kernel exception due to WebtestCase...
$_SERVER['KERNEL_DIR'] = __DIR__ . '/../../app/';
use Behat\Behat\Tester\Exception\PendingException;
use Behat\Behat\Context\Context;
use PHPUnit_Framework_Assert as Assert;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Behat\Behat\Hook\Scope\AfterScenarioScope;

/**
 * Defines application features from the specific context.
 */
class FeatureContext extends WebTestCase implements Context
{
    private $client = null;
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
      $crawler = $this->client->request('GET', '/category');
      
      $form = $crawler->selectButton('Valider')->form();
      // Set the task values
      $form->setValues(array('appbundle_category[name]' => $cate));
      // submit the form
      $this->client->submit($form);
    }
    
    /**
     * @Then il y a une categorie :cate dans l'application
     */
    public function ilYAUneCategorieDansLapplication($cate)
    {
        $crawler = $this->client->request('GET', '/category');
	
        // By default there is already one card for the category creation 
        // which has the same class
        $category_count = $this->getItemCardCount($crawler, $cate); 
        
	$this->assertEquals(1, $category_count, "Category '". $cate ."' count is incorrect");
    }
    
    /**
     * @Given il existe la categorie :cate dans l'application
     */
    public function ilExisteLaCategorieDansLapplication($cate)
    {
      $crawler = $this->client->request('GET', '/category');
      
      $form = $crawler->selectButton('Valider')->form();
      // Set the task values
      $form->setValues(array('appbundle_category[name]' => $cate));
      // submit the form
      $this->client->submit($form);
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
        $this->client->request('GET', '/category/delete/'.$categoryID);
    }

    /**
     * @Then la categorie :cate n'est plus affichée dans l'application
     */
    public function laCategorieNestPlusAfficheeDansLapplication($cate)
    {
         $crawler = $this->client->request('GET', '/category');
	
        // By default there is already one card for the category creation 
        // which has the same class
        $category_count = $this->getItemCardCount($crawler, $cate);  
        
	$this->assertEquals(0, $category_count, "Category '". $cate ."' count is incorrect");
    }   

    
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
        $crawler = $this->client->request('GET', '/category');
      
        $form = $crawler->selectButton('Valider')->form();
        // Set the category values
        $form->setValues(array('appbundle_category[name]' => "testCategory"));
        // submit the form
        $this->client->submit($form);              
    }
    
    /**
     * @When j'ajoute le produit :product dans l'application
     */
    public function jajouteLeProduitDansLapplication($product)
    {
        $crawler = $this->client->request('GET', '/product');
      
        $form = $crawler->selectButton('Valider')->form();
        // Set the product values
        $form->setValues(array('appbundle_product[name]' => $product));
        // submit the form
        $this->client->submit($form);
        
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
        // First I create a category.
        $crawler = $this->client->request('GET', '/category');
      
        $form = $crawler->selectButton('Valider')->form();
        // Set the category values
        $form->setValues(array('appbundle_category[name]' => "testCategory"));
        // submit the form
        $this->client->submit($form);     
        
        // Then I create a product.
        $crawler2 = $this->client->request('GET', '/product');
      
        $form2 = $crawler2->selectButton('Valider')->form();
        // Set the product values
        $form2->setValues(array('appbundle_product[name]' => $product));
        // submit the form
        $this->client->submit($form2);   
    }

    /**
     * @When je supprime le produit :product dans l'application
     */
    public function jeSupprimeLeProduitDansLapplication($product)
    {
        $crawler = $this->client->request('GET', '/category');
        // Filter to find the correct onclick attribute for the product.
        $filter = "[onclick*=\"deleteProduct('". $product ."',\"]";
       
        $productID = $this->getItemCardId($crawler, $filter);
       
        // With the product ID we can now delete it.
        $this->client->request('GET', '/product/delete/'.$productID);
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
        // Filter to find the correct onclick attribute for the product.
        //$filter = "[onclick*=\"deleteProduct('". $product ."',\"]";
        echo "Filter = ".$filter;
        // On the js function, we can find the item ID.
        $node_attribute = $crawler->filter($filter)->attr("onclick");
        echo "Node attribute = ".$node_attribute;
        // The product ID is located in the 4th index.
        $itemID = explode("'", $node_attribute)[3];
        return $itemID;
    }
      
    
    /** @AfterScenario */
    public function after(AfterScenarioScope $scope)
    {
        // Clean all the categories hence all the products.
        $this->client->request('GET', '/category/clean');    
    }
    
}
