<?php
// To avoid the kernel exception due to WebtestCase...
$_SERVER['KERNEL_DIR'] = __DIR__ . '/../../app/';
use Behat\Behat\Tester\Exception\PendingException;
use Behat\Behat\Context\Context;
use PHPUnit_Framework_Assert as Assert;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
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
        
	$this->assertEquals($crawler->filter('#listCategory')->count(), 0);
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
        $this->client->request('GET', '/category');
	$this->assertContains($cate, $this->client->getResponse()->getContent());
        //Clean the category.
        $this->client->request('GET', '/category/clean');
    }

    
    /**
     * @Given il n'y a aucun produit dans l'application
     */
    public function ilNyAAucunProduitDansLapplication()
    {   
	$crawler = $this->client->request('GET', '/product');
        // By default there is already one card for the product creation 
        // which has the same class
	$this->assertEquals($crawler->filter('.card-image-label')->count(), 1);
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
        
        $crawler = $this->client->request('GET', '/category');
        
	$this->assertEquals($crawler->filter('.card-image-label')->count(), 2);
        
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
  
        $product_count = $crawler->filter('.card-image-label')->reduce(
                function ($node, $i) {
            return false;       
           /* file_put_contents("test.txt", node->text());
                    if (strcmp($node->text(), $product) == 0) {
                        return true;
                    } else {
                        return false;
                    }*/
                }
        )->count();
        
	$this->assertEquals(5, $product_count);
        // Clean the product.
        $this->client->request('GET', '/product/clean');
    }
    
}
