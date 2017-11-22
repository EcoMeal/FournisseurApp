<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\Category;
use AppBundle\Form\CategoryType;

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
    public function cleanAllCategoryAction()
    {
        $em = $this->getDoctrine()->getManager(); 
        $category_list = $em->getRepository("AppBundle:Category")->findAll();
        
        for($i = 0; $i < count($category_list); $i++){
             $em->remove($category_list[$i]);
        }
           
        
        $em->flush();
        return $this->redirect('/category');
    }


    /**
     * @Route("/category")
     */
    public function saveCategoryAction(Request $request)
    {
		
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        // Doctrine manager
        $doct = $this->getDoctrine()->getManager();

        // Error flag
        $error = null;

	// If the form is being processed and if it is valid
        if ($form->isSubmitted() && $form->isValid()) {
		
            // Checks if the category already exists
            $categoryWithSameName = $doct->getRepository("AppBundle:Category")->findOneByName($category->getName());

            if(!is_null($categoryWithSameName)) {
                $error = "La catégorie existe déjà";
            } else {
			
                // Save the category image if it exists
                if(!is_null($category->getImagePath())) {

                    // Get the file
                    $file = $category->getImagePath();

                    // Generate a unique name for the file before saving it
                    $fileName = md5(uniqid()).'.'.$file->guessExtension();

                    // Move the file to the directory where images are stored
                    $file->move(
                            $this->getParameter('image_directory'),
                            $fileName
                    );

                    // Update imagePath in the category entity
                    $category->setImagePath($fileName);

                }

                // Save the category in database
                $doct->persist($category);
                $doct->flush();

            }

        }

        // Retrieves all the categories from the database
        $categories = $doct->getRepository("AppBundle:Category")->findBy([], ['name' => 'ASC']);

        // Displays the form, the categories and the errors if there are any
        return $this->render('AppBundle:Category:add_category.html.twig', array(
                'categories' => $categories, "form" => $form->createView(), "error" => $error
        ));
		
    }

}
