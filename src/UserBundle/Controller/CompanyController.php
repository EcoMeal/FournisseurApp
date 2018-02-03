<?php

namespace UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use UserBundle\Services\CompanyService;

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
	
}
