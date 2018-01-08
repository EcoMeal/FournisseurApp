<?php

// To avoid the kernel exception due to WebtestCase...
$_SERVER['KERNEL_DIR'] = __DIR__ . '/../../app/';
use AppBundle\Entity\Product;
use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Behat\Hook\Scope\AfterScenarioScope;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Defines application features from the specific context.
 */
class CommonContext extends WebTestCase implements Context
{
	
	private $commonMessage;
	
	/**
	 * @AfterScenario
	 *
	 * Nettoie le message après chaque test
	 * */
	public function after()
	{
		// Clear the JSON message
		$this->commonMessage = null;
	}
	
	
	//Features
	
	/**
	 * Placeholder function when no actions can be assigned. To keep the flow:
	 * Given, When, Then.
	 * @When void
	 */
	public function void()
	{
	
	}
	
	/**
	 * @Then l'application renvoie un message d'erreur :errorMessage
	 */
	public function lapplicationRenvoieUnMessageDerreur($errorMessage)
	{
		$this->assertEquals($errorMessage, $this->commonMessage['error']);
	}
	
	public function getMessage() {
		return $this->commonMessage;
	}
	
	public function setMessage($message) {
		$this->commonMessage = $message;
	}
	
}