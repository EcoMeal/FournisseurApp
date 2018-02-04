<?php

namespace UserBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use UserBundle\Services\CompanyService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class CompanyController extends Controller
{
	
	/**
	 * @Route("/company/{id}", requirements={"id" = "\d+"})
	 * @Method({"DELETE"})
	 * 
	 * Deletes a company account from the database.
	 *
	 */
	public function deleteCompany($id, CompanyService $companyService) {
		$retour = $companyService->deleteCompany($id);
		$response = null;
		if($retour['type'] == "ERROR") {
			$response = array("error" => $retour['message']);
		} else {
			$response = array("success" => $retour['message']);
		}
		return $this->json($response);
	}
	
	/**
	 * @Route("/company")
	 * @Method({"GET"})
	 * 
	 * Lists all the company accounts in the database.
	 * 
	 */
	public function listCompanies() {
		$em = $this->getDoctrine()->getManager();
		$companies = $em->getRepository("UserBundle:Company")->findAll();
		return $this->render('UserBundle:Company:list_companies.html.twig', array(
				'companies' => $companies
		));
	}
	
	/**
	 * @Route("/company/categories")
	 * @Method({"GET"})
	 * 
	 * Returns the form that allows the user to link product categories to a company
	 * 
	 */
	public function displayCompanyCategoriesAction() {
		
		$em = $this->getDoctrine()->getManager();
		$companies = $em->getRepository("UserBundle:Company")->findAll();
		$categories = $em->getRepository("AppBundle:Category")->findAll();
		
		return $this->render("UserBundle:Company:add_company_categories.html.twig", array(
				'companies' => $companies,
				'categories' => $categories
		));
		
	}
	
	/**
	 * @Route("/company/{id}/categories", requirements={"id" = "\d+"})
	 * @Method({"GET"})
	 */
	public function getCompanyCategoriesAction($id) {
		$em = $this->getDoctrine()->getManager();
		$company = $em->getRepository("UserBundle:Company")->find($id);
		if(!is_null($company)) {
			$categories = $company->getCategories();
			$arrayJson = array();
			for($i = 0; $i < count($categories); $i++){
            	array_push($arrayJson, $categories[$i]->getId());
        	}
        	return $this->json($arrayJson);
		} else {
			//TODO message d'erreur id invalide
		}
	}
	
	/**
	 * @Route("/company/{id}/categories", requirements={"id" = "\d+"})
	 * @Method({"POST"})
	 */
	public function setCompanyCategoriesAction($id, Request $request) {
			
		var_dump("content = ".$request->getContent());
		$selectedCategories = json_decode($request->getContent());
		$em = $this->getDoctrine()->getManager();
		$allCategories = $em->getRepository("AppBundle:Category")->findAll();
		$company = $em->getRepository("UserBundle:Company")->find($id);
		$companyCategories = array();
		
		for($i = 0; $i < count($allCategories); $i++){
			if(in_array($allCategories[$i]->getId(), $selectedCategories)) {
				array_push($companyCategories, $allCategories[$i]);
			}
		}
		
		$company->setCategories($companyCategories);
		$em->persist($company);
        $em->flush();
        
        return new JsonResponse(null, 200);
		
	}
	
}
