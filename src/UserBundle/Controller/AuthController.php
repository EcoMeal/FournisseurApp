<?php

namespace UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use UserBundle\Entity\AuthToken;

class AuthController extends Controller
{
	
	/**
	 * @Route("/api/login_customer")
     * @Method({"POST"})
	 */
	public function loginAsCustomer(Request $request) {
		
		$content = json_decode($request->getContent());
		
		$login = $content->login;
		$password = $content->password;
		
		$userManager = $this->get('fos_user.user_manager');
		
		$user = $userManager->findUserBy(array('username' => $login));
		
		if(is_null($user) || !$user->hasRole("ROLE_CUSTOMER")) {
			return new JsonResponse("bad credentials", 400);
		}
		
		$encoder = $this->get('security.password_encoder');
		$isPasswordValid = $encoder->isPasswordValid($user, $password);
		
		// Le mot de passe n'est pas correct
		if (!$isPasswordValid) { 
			return new JsonResponse("bad credentials", 400);
		}
		
		$authToken = new AuthToken();	
		$authToken->setToken(base64_encode(random_bytes(64)));
		$authToken->setCreationTime(new \DateTime('now'));
		$authToken->setUser($user);
		
		$em = $this->getDoctrine()->getManager();
		
		$oldTokens = $em->getRepository("UserBundle:AuthToken")->findBy(array("user" => $user));
		
		if(!is_null($oldTokens)) {
			for($i = 0; $i < count($oldTokens); $i++){
				$em->remove($oldTokens[$i]);
			}
			$em->flush();
		}
		
		$em->persist($authToken);
		$em->flush();
		
		return new JsonResponse(array('X-Auth_Token' => $authToken->getToken(), "mail" => $user->getEmail()), 200);
		
	}
	
}
