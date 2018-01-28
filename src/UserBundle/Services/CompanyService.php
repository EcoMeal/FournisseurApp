<?php

namespace UserBundle\Services;

use Doctrine\ORM\EntityManager;

class CompanyService {
	
	private $em;
	
	public function __construct(EntityManager $entityManager) {
		$this->em = $entityManager;
	}
	
	public function deleteCompany($id) {
		
		$company = $this->em->getRepository("UserBundle:Company")->findOneById($id);    
		
		if($company) {
			$user = $company->getUser();
			$this->em->remove($user);
			$this->em->remove($company);
			$this->em->flush();
			return array(
					"type" => "SUCCESS",
					"message" => "Catégorie supprimée"
			);
		}
		
		return array(
				"type" => "ERROR",
				"message" => "La companie n'existe pas"
		);
		
	}
	
}