<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

use AppBundle\Entity\BasketCategory;
use AppBundle\Form\BasketCategoryType;

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
     * @Route("/basket_category/delete/{id}", requirements={"id" = "\d+"})
     * 
     * Deletes the basket category with the given id from the database.  
     */
    public function deleteBasketCategoryAction($id)
    {
        $em = $this->getDoctrine()->getManager(); 

        $basket_category = $em->getRepository("AppBundle:BasketCategory")->findOneById($id);    
        $em->remove($basket_category);          
        $em->flush();
        return $this->redirect('/basket_category');
    }
    
      
    /**
     * @Route("/basket_category")
     */
    public function saveBasketCategoryAction(Request $request)
    {
		
        $basketCategory = new BasketCategory();
        $form = $this->createForm(BasketCategoryType::class, $basketCategory);
        $form->handleRequest($request);

        // Doctrine manager
        $doct = $this->getDoctrine()->getManager();

        // Error flag
        $error = null;

	// If the form is being processed and if it is valid
        if ($form->isSubmitted() && $form->isValid()) {
		
            // Checks if the category already exists
            $basketCategoryWithSameName = $doct->getRepository("AppBundle:BasketCategory")->findOneByName($basketCategory->getName());

            if(!is_null($basketCategoryWithSameName)) {
                $error = "La catégorie existe déjà";
            } else {
			
                // Save the category image if it exists
                if(!is_null($basketCategory->getImagePath())) {

                    // Get the file
                    $file = $basketCategory->getImagePath();

                    // Generate a unique name for the file before saving it
                    $fileName = md5(uniqid()).'.'.$file->guessExtension();

                    // Move the file to the directory where images are stored
                    $file->move(
                            $this->getParameter('image_directory'),
                            $fileName
                    );

                    // Update imagePath in the category entity
                    $basketCategory->setImagePath($fileName);

                }

                // Save the category in database
                $doct->persist($basketCategory);
                $doct->flush();

            }

        }

        // Retrieves all the categories from the database
        $categories = $doct->getRepository("AppBundle:BasketCategory")->findBy([], ['name' => 'ASC']);
        
        // Displays the form, the categories and the errors if there are any
        return $this->render('AppBundle:BasketCategory:add_basket_category.html.twig', array(
                'categories' => $categories, "form" => $form->createView(), "error" => $error
        ));
		
    }
    
}
