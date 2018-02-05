<?php

namespace UserBundle\Controller;

use FOS\UserBundle\Controller\RegistrationController as BaseController;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use UserBundle\Entity\Company;
use UserBundle\Form\CompanyType;
use Symfony\Component\HttpFoundation\JsonResponse;

class RegistrationController extends BaseController
{
	
	public function registerAction(Request $request) {
		
        $formFactory = $this->get('fos_user.registration.form.factory');
        $userManager = $this->get('fos_user.user_manager');
        $dispatcher = $this->get('event_dispatcher');

        $user = $userManager->createUser();
        $user->setEnabled(true);

        $company = new Company();
        $companyForm = $this->createForm(CompanyType::class, $company);
        
        /*$event = new GetResponseUserEvent($user, $request);
        $dispatcher->dispatch(FOSUserEvents::REGISTRATION_INITIALIZE, $event);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }*/
        
        $companyForm->handleRequest($request);
        
 		$success = NULL;
 		$error = NULL;
        
        if ($companyForm->isSubmitted()) {
            if ($companyForm->isValid()) {
            	
            	
            	$companyValid = true;
            	
            	//Vérifier que les données "uniques" ne sont pas déjà utilisées (username, mail, siret, companyname)
            	$em = $this->getDoctrine()->getManager();
            	$companies = $em->getRepository("UserBundle:Company")->findAll();
            	$currentCompany;
		        for($i = 0; $i < count($companies); $i++){
		        	$currentCompany = $companies[$i];
		        	if($currentCompany->getCompanyname() == $company->getCompanyname()) {
		        		$error = "Nom de compagnie déjà utilisé";
		        		$companyValid = false;
		        		break;
		        	} else if($currentCompany->getSiret() == $company->getSiret()) {
		        		$error = "Siret déjà utilisé";
		        		$companyValid = false;
		        		break;
		        	} else if($currentCompany->getUser()->getUsername() == $company->getUser()->getUsername()) {
		        		$error = "Nom d'utilisateur déjà utilisé";
		        		$companyValid = false;
		        		break;
		        	} else if($currentCompany->getUser()->getEmail() == $company->getUser()->getEmail()) {
		        		$error = "Email déjà utilisé";
		        		$companyValid = false;
		        		break;
		        	}
		        }
		        
		        if($companyValid) {
		        	$user = $company->getUser();
		        	$user->setEnabled(true);
		        	$user->addRole("ROLE_FOURNISSEUR");
		        	$userManager->updateUser($user);
		        	
		        	$em = $this->getDoctrine()->getManager();
		        	$em->persist($company);
		        	$em->flush();
		        	
		        	$company = new Company();
		        	$companyForm = $this->createForm(CompanyType::class, $company);
		        	$success = "Fournisseur créé avec succès";
		        }
                
            } else {
            	$error = "Erreur lors de l'enregistrement : certaines données sont invalides";
            }

        }
                
        $params = array('companyForm' => $companyForm->createView());
        
        if(!is_null($success)) {
        	$params['success'] = $success;
        }
        
        if(!is_null($error)) {
        	$params['error'] = $error;
        }
        
		return $this->render('@FOSUser/Registration/register.html.twig', $params);
		
    }
    
    /**
     * @Route("/api/register_customer")
     * @Method({"POST"})
     */
    public function registerCustomerAction(Request $request) {
    	
   		$content = json_decode($request->getContent());
    	$login = $content->login;
    	$mail = $content->mail;
    	$password = $content->password;
    	
    	//TODO check that the fields are valid
    	
    	$userManager = $this->get('fos_user.user_manager');
        $user = $userManager->createUser();
    	
        $user->setUsername($login);
        $user->setPlainPassword($password);
        $user->setEmail($mail);
        $user->addRole("ROLE_CUSTOMER");
        $user->setEnabled(true);
        
        $existingUser = $userManager->findUserBy(array("username" => $login));
        if(!$existingUser) {
	        $userManager->updateUser($user);
        } else {
        	return new JsonResponse(array("success" => false, "error" => "username already taken"), 400);
        }
        
    	return new JsonResponse(array("success" => true, "error" => ""), 200);
    	
    }
	
}