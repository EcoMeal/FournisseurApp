<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Stock;
use AppBundle\Form\StockType;
use AppBundle\Services\StockService;

class StockController extends Controller
{
    
    
    /**
     * @Route("/stock")
     */
    public function saveStockAction(Request $request, StockService $stockService)
    {
        
	// Doctrine manager
	$em = $this->getDoctrine()->getManager();
	
	// Error
	//$error = $stockService->updateProductStock($productID, $newStock);
        $error = NULL;
        
        $stock_history = $em->getRepository("AppBundle:Stock")->getCurrentStock();
			
        
	return $this->render('AppBundle:Stock:add_stock.html.twig',
                array("stock_history" => $stock_history,
                    "error" => $error
	));
		
    }
    
    
}
