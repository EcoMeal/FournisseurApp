<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use AppBundle\Entity\Category;
use AppBundle\Form\CategoryType;

class CategoryController extends Controller
{
	



    /**
     * @Route("/category/clean")
     */
    public function cleanAllCategoryAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager(); 
        $connection = $em->getConnection();
        $platform   = $connection->getDatabasePlatform();
        $connection->executeUpdate($platform->getTruncateTableSQL('category', true /* whether to ccascade */));
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
		
		//Doctrine manager
		$doct = $this->getDoctrine()->getManager();
		
		//Error
		$error = null;

		//En cas de formulaire valide
        if ($form->isValid()) {
		
			//On vérifie que la catégorie n'existe pas déjà
			$categoryWithSameName = $doct->getRepository("AppBundle:Category")->findOneByName($category->getName());
			
			if(!is_null($categoryWithSameName)) {
				$error = "La catégorie existe déjà";
			} else {
				
                if(!is_null($category->getImagePath())) {
                
                    // On enregistre le fichier
				    $file = $category->getImagePath();
				
				    // Generate a unique name for the file before saving it
				    $fileName = md5(uniqid()).'.'.$file->guessExtension();

				    // Move the file to the directory where brochures are stored
				    $file->move(
					    $this->getParameter('image_directory'),
					    $fileName
				    );
				
				    $category->setImagePath($fileName);

                }
				
				// On enregistre la catégorie
				$doct->persist($category);
				$doct->flush();
				
			}
			
		}
		
		$categories = $doct->getRepository("AppBundle:Category")->findBy([], ['name' => 'ASC']);
			
		return $this->render('AppBundle:Category:add_category.html.twig', array(
			'categories' => $categories, "form" => $form->createView(), "error" => $error
		));
		
    }

}
