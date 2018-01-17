<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;

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
