<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use AppBundle\Entity\Category;
use AppBundle\Form\CategoryType;
use AppBundle\Services\CategoryService;

class CategoryController extends Controller
{
	
    /**
     * @Route("/category/clean")
     * 
     * Deletes all the categories from the database. 
     * Used for functional testing only.
     * Should be removed or updated later as it is not secured.
     * 
     */
    public function cleanAllCategoryAction(CategoryService $categoryService)
    {
        $categoryService->cleanAllCategory();
        return $this->redirect('/category');
    }
    
    /**
     * @Route("/category/{id}", requirements={"id" = "\d+"})
     *
     * @Method({"DELETE"})
     * Deletes the category with the given id from the database.  
     */
    public function deleteCategoryAction($id, CategoryService $categoryService)
    {
        $error = $categoryService->deleteCategory($id);
        return $this->json(array('error' => $error));
    }


    /**
     * @Route("/category")
     * @Method({"POST", "GET"})
     * 
     * Show the available categories on the application and save any new category
     * given in POST.
     */
    public function saveCategoryAction(Request $request, CategoryService $categoryService)
    {
        
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
        
        // Error flag
        $error = null;

		// If the form is being processed and if it is valid
        if ($form->isSubmitted() && $form->isValid()) {	
        	$error = $categoryService->saveCategory($category);
        }

        // Retrieves all the categories from the database
        $categories =  $categoryService->getAllCategoriesOrdererByName();
                
        // Displays the form, the categories and the errors if there are any
        return $this->render('AppBundle:Category:add_category.html.twig', array(
                'categories' => $categories, 
        		"form" => $form->createView(), 
        		"error" => $error,
        		"success" => ""
        ));
		
    }
    
    /**
     * @Route("/category/{id}", requirements={"id" = "\d+"})
     * @Method({"PUT"})
     * 
     * Update an already existing category.
     * 
     */
    public function updateCategoryAction($id, Request $request, CategoryService $categoryService)
    {
        
        $category = $categoryService->getCategory($id);
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
        
        // Error flag
        $error = null;

	// If the form is being processed and if it is valid
        if ($form->isSubmitted() && $form->isValid()) {	
        	
                $error = $categoryService->updateCategory($category);
                
                if(!$error){
                    return $this->redirect('/category');
                }
        }
        
        // Displays the form, and the errors if there are any.
        return $this->render("AppBundle:Category:update_category.html.twig", array(
                "form" => $form->createView(), "error" => $error
        ));

    }

}
