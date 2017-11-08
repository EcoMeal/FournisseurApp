<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use AppBundle\Entity\Product;
use AppBundle\Form\ProductType;

class ProductController extends Controller
{
	



    /**
     * @Route("/product/clean")
     */
    public function cleanAllProductAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager(); 
        $connection = $em->getConnection();
        $platform   = $connection->getDatabasePlatform();
        $connection->executeUpdate($platform->getTruncateTableSQL('product', true /* whether to ccascade */));
        return $this->redirect('/product');
    }


    /**
     * @Route("/product")
     */
    public function saveProductAction(Request $request)
    {
		
	$product = new Product();
	$form = $this->createForm(ProductType::class, $product);
	$form->handleRequest($request);
		
	//Doctrine manager
	$doct = $this->getDoctrine()->getManager();
		
	//Error
	$error = null;

	//En cas de formulaire valide
        if ($form->isValid()) {
		
            // On enregistre la catégorie
            $doct->persist($product);
            $doct->flush();
	}
        $product_list = $doct->getRepository("AppBundle:Product")->findBy([], ['name' => 'ASC']);
			
	return $this->render('AppBundle:Product:add_product.html.twig',
                array("form" => $form->createView(), "product_list" => $product_list,
                    "error" => $error
	));
		
    }

}
