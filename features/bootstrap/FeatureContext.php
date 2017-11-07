<?php

// To avoid the kernel exception due to WebtestCase...
$_SERVER['KERNEL_DIR'] = __DIR__ . '/../../app/';

use Behat\Behat\Tester\Exception\PendingException;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;

use PHPUnit_Framework_Assert as Assert;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Defines application features from the specific context.
 */
class FeatureContext extends WebTestCase implements Context
{

    private $client = null;
    private $crawler = null;

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
        $category = "<div class=\"category\">";

	    $crawler = $this->client->request('GET', '/category');
	    $this->assertNotContains(
        $category,
            $this->client->getResponse()->getContent()
        );
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
	    $this->assertContains(
            $cate, $this->client->getResponse()->getContent()
        );
        //Clean the category.
        $this->client->request('GET', '/category/clean');
    }
}
