<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Stock;
use AppBundle\Form\StockType;

class StockController extends Controller
{
    
    
    /**
     * @Route("/stock")
     */
    public function saveStockAction(Request $request)
    {
		
	$stock = new Stock();
	$form = $this->createForm(StockType::class, $stock);
	$form->handleRequest($request);
		
	//Doctrine manager
	$doct = $this->getDoctrine()->getManager();
		
	//Error
	$error = null;

	//En cas de formulaire valide
        if ($form->isValid()) { 

                // On enregistre le produit
                $doct->persist($stock);
                $doct->flush();
		
	}
        
        $stock_history = $doct->getRepository("AppBundle:Stock")->findBy([], ['date' => 'ASC']);
			
	return $this->render('AppBundle:Stock:add_stock.html.twig',
                array("form" => $form->createView(), "stock_history" => $stock_history,
                    "error" => $error
	));
		
    }
    
    
}
