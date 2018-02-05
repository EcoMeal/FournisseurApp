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
					"message" => "Fournisseur supprimé avec succès"
			);
		}
		
		return array(
				"type" => "ERROR",
				"message" => "Erreur lors de la suppression du fournisseur. Aucun fournisseur correspondant à cet identifiant"
		);
		
	}
	
}