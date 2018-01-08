<?php

$_SERVER['KERNEL_DIR'] = __DIR__ . '/../../app/';
use Behat\Behat\Context\Context;

class UtilContext implements Context {
	
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
	
}