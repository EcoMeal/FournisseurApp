<?php

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;

//require_once 'PHPUnit/Framework/Assert/Functions.php';

/**
 * Defines application features from the specific context.
 */
class FeatureContext implements Context
{
    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     */
    public function __construct()
    {
    }

	/**
     * @Given un produit :produit
     */
    public function unProduit($produit)
    {
       // throw new PendingException();
    }

    /**
     * @Given il n'y a aucun produit dans l'application
     */
    public function ilNYAAucunProduitDansLApplication()
    {
      //  throw new PendingException();
    }

    /**
     * @When j'ajoute le produit :produit dans l'application
     */
    public function jAjouteLeProduitDansLApplication($produit)
    {
       // throw new PendingException();
    }

    /**
     * @Then il y a un produit :produit dans l'application
     */
    public function ilYAUnProduitDansLApplication($produit)
    {
       // PHPUnit_Framework_Assert::assertCount(1, 1);
    }

}
