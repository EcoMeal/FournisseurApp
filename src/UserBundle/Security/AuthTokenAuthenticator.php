<?php

namespace UserBundle\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\PreAuthenticatedToken;
use Symfony\Component\Security\Http\Authentication\SimplePreAuthenticatorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\HttpUtils;

class AuthTokenAuthenticator implements SimplePreAuthenticatorInterface, AuthenticationFailureHandlerInterface {
	
	const TOKEN_VALIDITY_DURATION = 1800;
	
	protected $httpUtils;
	
	public function __construct(HttpUtils $httpUtils)
	{
		$this->httpUtils = $httpUtils;
		$this->exceptions = array(
				array("url" => "login_customer", "method" => "POST"),
				array("url" => "register_customer", "method" => "POST")
		);
	}
	
	public function noAuthRequired(Request $request) {
		$method = $request->getMethod();
		for($i = 0; $i < count($this->exceptions); $i++){
            $exception = $this->exceptions[$i];
            if($exception['method'] === $method && $this->httpUtils->checkRequestPath($request, '/api/'.$exception['url'])) {
            	return true;
            }
        }
        return false;
	}
	
	public function createToken(Request $request, $providerKey)
	{
	
		// Si la requête est une création de token ou une création de compte, aucune vérification n'est effectuée
		if (self::noAuthRequired($request)) {
			return;
		}
		
		$authTokenHeader = $request->headers->get('X-Auth-Token');
		
		if (!$authTokenHeader) {
			throw new BadCredentialsException('X-Auth-Token header is required');
		}
		
		return new PreAuthenticatedToken(
				'anon.',
				$authTokenHeader,
				$providerKey
		);
		
	}
	
	public function authenticateToken(TokenInterface $token, UserProviderInterface $userProvider, $providerKey) {
		
		if (!$userProvider instanceof AuthTokenUserProvider) {
			throw new \InvalidArgumentException(
				sprintf(
					'The user provider must be an instance of AuthTokenUserProvider (%s was given).',
					get_class($userProvider)
				)
			);
		}
		
		$tokenValue = $token->getCredentials();
		
		$authToken = $userProvider->getAuthToken($tokenValue);
		
		if(!$authToken || !$this->isTokenValid($authToken)) {
			throw new BadCredentialsException("Invalid authentication token");
		}
		
		$user = $authToken->getUser();
		$pre = new PreAuthenticatedToken(
			$user,
			$tokenValue,
			$providerKey,
			$user->getRoles()
		);
		
		$pre->setAuthenticated(true);
		
		return $pre;
		
	}
	
	public function supportsToken(TokenInterface $token, $providerKey)
	{
		return $token instanceof PreAuthenticatedToken && $token->getProviderKey() === $providerKey;
	}
	
	private function isTokenValid($authToken)
	{
		return (time() - $authToken->getCreationTime()->getTimestamp()) < self::TOKEN_VALIDITY_DURATION;
	}
	
	public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
	{
		// Si les données d'identification ne sont pas correctes, une exception est levée
		throw $exception;
	}
	
}