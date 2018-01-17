<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class LoginController extends Controller
{
	
	/**
	 * @Route("/login")
	 * @Method({"GET"})
	 *
	 * Returns the login form.
	 *
	 */
	public function getLoginForm() {
		return $this->render('AppBundle:Login:login_form.html.twig', array());
	}
	
	public function authenticate(Request $request) {
		
	}
	
}
