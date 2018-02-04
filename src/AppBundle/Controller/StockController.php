<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Stock;
use AppBundle\Services\StockService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class StockController extends Controller
{
   	
	/**
	 * @Route("/stock")
	 */
	public function saveStockAction(Request $request, StockService $stockService) {
		
		// Doctrine manager
		$em = $this->getDoctrine()->getManager();
		$error = NULL;
		
		$rawData = $request->getContent();
		
		if(!empty($rawData)) {
			if(strstr($rawData, "&")) {
				$itemUpdateList = explode("&", $rawData);
			} else {
				$itemUpdateList = array($rawData);
			}
			
			$size = count($itemUpdateList);
			
			for($i = 0; $i < $size; $i ++) {
				
				$data = explode("=", $itemUpdateList[$i]);
				$productID = $data[0];
				$newStock = $data[1];
				
				$error = $stockService->updateProductStock($productID, $newStock);
				
			}
		}
		
		$stock_history = $em->getRepository("AppBundle:Stock")->getCurrentStock();
		
		return $this->render('AppBundle:Stock:add_stock.html.twig', array(
				"stock_history" => $stock_history,
				"error" => $error,
				"success" => ""
		));
	}

	/**
	 * @Route("/stats")
	 */
	public function statsStockAction(Request $request, StockService $stockService) {
		$em = $this->getDoctrine()->getManager();

		$top_product = $em->getRepository("AppBundle:Stock")->getCurrentStockLimit(5);
		return $this->render('AppBundle:Stock:history_stock.html.twig', array(
				"top_product" => $top_product,
		));
	}

}
