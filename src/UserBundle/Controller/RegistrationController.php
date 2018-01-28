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
		
		/** @var $formFactory FactoryInterface */
        $formFactory = $this->get('fos_user.registration.form.factory');
        /** @var $userManager UserManagerInterface */
        $userManager = $this->get('fos_user.user_manager');
        /** @var $dispatcher EventDispatcherInterface */
        $dispatcher = $this->get('event_dispatcher');

        $user = $userManager->createUser();
        $user->setEnabled(true);

        $company = new Company();
        $companyForm = $this->createForm(CompanyType::class, $company);
        
        $event = new GetResponseUserEvent($user, $request);
        $dispatcher->dispatch(FOSUserEvents::REGISTRATION_INITIALIZE, $event);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        /*$form = $formFactory->createForm();
        $form->setData($user);*/

        //$form->handleRequest($request);
        $companyForm->handleRequest($request);

        if ($companyForm->isSubmitted()) {
            if ($companyForm->isValid()) {
                $event = new FormEvent($companyForm, $request);
                $dispatcher->dispatch(FOSUserEvents::REGISTRATION_SUCCESS, $event);

                $user = $company->getUser();
                $user->setEnabled(true);
                $user->addRole("ROLE_FOURNISSEUR");
                $userManager->updateUser($user);
                
                $em = $this->getDoctrine()->getManager();
                $em->persist($company);
                $em->flush();
                
                if (null === $response = $event->getResponse()) {
                    $url = $this->generateUrl('fos_user_registration_confirmed');
                    $response = new RedirectResponse($url);
                }

                $dispatcher->dispatch(FOSUserEvents::REGISTRATION_COMPLETED, new FilterUserResponseEvent($user, $request, $response));

                return $response;
            }

            $event = new FormEvent($form, $request);
            $dispatcher->dispatch(FOSUserEvents::REGISTRATION_FAILURE, $event);

            if (null !== $response = $event->getResponse()) {
                return $response;
            }
        }
                
		return $this->render('@FOSUser/Registration/register.html.twig', array(
            'companyForm' => $companyForm->createView()
        ));
		
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