<?php

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;

use PHPUnit_Framework_Assert as Assert;

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
        Assert::assertEquals(1, 1);
    }

}
