<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Services\BasketCategoryService;
use AppBundle\Entity\BasketCategory;
use AppBundle\Form\BasketCategoryType;
use AppBundle\Services\BasketCategoryService;

class BasketCategoryController extends Controller
{
    
    /**
     * @Route("/basket_category/clean")
     * 
     * Deletes all the basket categories from the database. 
     * Used for functional testing only.
     * Should be removed or updated later as it is not secured.
     * 
     */
    public function cleanAllBasketCategoryAction()
    {
        $em = $this->getDoctrine()->getManager(); 

        $basket_category_list = $em->getRepository("AppBundle:BasketCategory")->findAll();
        
        for($i = 0; $i < count($basket_category_list); $i++){
             $em->remove($basket_category_list[$i]);
        }
           
        $em->flush();
        return $this->redirect('/basket_category');
    }
    
    
    /**
     * @Route("/basket_category/{id}", requirements={"id" = "\d+"})
     * @Method({"DELETE"})
     * Deletes the basket category with the given id from the database.  
     */
    public function deleteBasketCategoryAction($id,
            BasketCategoryService $basketCategoryService)
    {
        $error = $basketCategoryService->deleteBasketCategory($id);
        return $this->json(array('error' => $error));
    }
    
      
    /**
     * @Route("/basket_category")
     */
    public function saveBasketCategoryAction(Request $request,
            BasketCategoryService $basketCategoryService)
    {
		
        $basketCategory = new BasketCategory();
        $form = $this->createForm(BasketCategoryType::class, $basketCategory);
        $form->handleRequest($request);

        // Error flag
        $error = null;

	// If the form is being processed and if it is valid
        if ($form->isSubmitted() && $form->isValid()) {
	    $error = $basketCategoryService->saveBasketCategory($basketCategory);
        }

        // Retrieves all the categories from the database
        $categories = $basketCategoryService->getAllBasketCategoryOrderedByName();
        
        // Displays the form, the categories and the errors if there are any
        return $this->render('AppBundle:BasketCategory:add_basket_category.html.twig', array(
                'categories' => $categories, "form" => $form->createView(), "error" => $error
        ));
		
    }
    
}
