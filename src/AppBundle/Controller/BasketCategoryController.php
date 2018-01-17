<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\BasketCategory;
use AppBundle\Form\BasketCategoryType;
use AppBundle\Services\BasketCategoryService;
use AppBundle\Services\JsonFactory;
use Symfony\Component\HttpFoundation\JsonResponse;

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
     * Returns all the baskets categories of the application as JSON
     * 
     * @Route("/api/basket_category")
     * @Method({"GET"})
     */
    public function getAllBasketCategories(JsonFactory $jsonFactory) {
    	$em = $this->getDoctrine()->getManager();
    	$basketCategories = $em->getRepository("AppBundle:BasketCategory")->findAll();
    	return new JsonResponse($jsonFactory->getBasketCategories($basketCategories));
    }
    
    
    /**
     * @Route("/basket_category/{id}", requirements={"id" = "\d+"})
     * @Method({"DELETE"})
     * Deletes the basket category with the given id from the database.  
     */
    public function deleteBasketCategoryAction($id, BasketCategoryService $basketCategoryService) {
    	$retour = $basketCategoryService->deleteBasketCategory($id);
        $response = null;
         if($retour['type'] == "ERROR") {
         	$response = array("error" => $retour['message']);
        } else {
         	$response = array("success" => $retour['message']);
        }
        return $this->json($response);
    }
    
      
    /**
     * @Route("/basket_category")
     */
    public function saveBasketCategoryAction(Request $request,
            BasketCategoryService $basketCategoryService) {
		
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
                'categories' => $categories, 
        		"form" => $form->createView(), 
        		"error" => $error,
        		"success" => ""
        ));
		
    }
    
    
     /**
     * @Route("/basket_category/{id}", requirements={"id" = "\d+"})
     * @Method({"PUT"})
     * 
     * Update an already existing category.
     * 
     */
    public function updateCategoryAction($id, Request $request, BasketCategoryService $categoryService)
    {

        $category = $categoryService->getCategory($id);
       
        // Error flag
        $error = null;
        
        // Wrong category id.
        if($category == null){
            $error = "La catégorie à mettre à jour n'existe pas";
        } else {
		$newName = $request->getContent();
		
		if(!empty($newName)) {
                    $category->setName($newName);
                    $error = $categoryService->updateCategory($category);
            }
        }
        
        if($error){
            return new JsonResponse(array("error" => $error));
        } else {
             return new JsonResponse(array("success" => "Le nom de la catégorie à bien été mis à jour."));
        }
  
    }
    
}
