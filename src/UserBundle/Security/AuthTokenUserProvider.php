<?php

namespace UserBundle\Security;

use Symfony\Component\Security\Core\User\UserProviderInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use FOS\UserBundle\Doctrine\UserManager;

class AuthTokenUserProvider implements UserProviderInterface {
	
	protected $authTokenRepository;
	
	protected $userRepository;
	
	public function __construct(EntityRepository $authTokenRepository, UserManager $userRepository) {
		$this->authTokenRepository = $authTokenRepository;
		$this->userRepository = $userRepository;
	}
	
	public function getAuthToken($token) {
		return $this->authTokenRepository->findOneByToken($token);
	}
	
	public function loadUserByUsername($username) {
		return $this->userRepository->findUserByUsername($username);
	}
	
	public function refreshUser(UserInterface $user) {
		throw new UnsupportedUserException();
	}
	
	public function supportsClass($class) {
		return "UserBundle\Entity\User" === $class;
	}
	
}