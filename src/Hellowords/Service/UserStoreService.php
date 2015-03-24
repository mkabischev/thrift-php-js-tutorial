<?php

namespace Hellowords\Service;

use Doctrine\ORM\EntityManagerInterface;
use Hellowords\AccessViolationException;
use Hellowords\AuthResult;
use Hellowords\Component\Security;
use Hellowords\Component\SessionTrait;
use Hellowords\InvalidRequestException;
use Hellowords\Model\User;
use Hellowords\Model\UserSyncState;
use Hellowords\Repository\UserRepository;
use Hellowords\UserInfo;
use Hellowords\UserStoreIf;
use Psr\Log\LoggerInterface;

class UserStoreService implements UserStoreIf
{
    use SessionTrait;

    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var Security */
    private $security;

    /** @var LoggerInterface */
    private $logger;

    public function __construct(EntityManagerInterface $entityManager, LoggerInterface $logger)
    {
        $this->security = new Security();
        $this->entityManager = $entityManager;
        $this->logger = $logger;
    }

    /**
     * @return UserRepository
     */
    protected function getUserRepository()
    {
        return $this->entityManager->getRepository(User::class);
    }

    /**
     * @param string $username
     * @param string $password
     * @return AuthResult
     * @throws InvalidRequestException
     */
    public function authenticate($username, $password)
    {
        if (empty($username)) {
            throw new InvalidRequestException(['message' => 'Username is empty']);
        }

        if (empty($password)) {
            throw new InvalidRequestException(['message' => 'Password is empty']);
        }

        $existUser = $this->getUserRepository()->findOneByUsername($username);
        /* @var $existUser User */

        if ($existUser) {
            if ($this->security->serialize($password, $existUser->getSalt()) === $existUser->getPasswd()) {
                return $this->createSession($existUser->getUserInfo());
            }
            throw new AccessViolationException([
                'message' => sprintf('The pass is wrong for user "%s".', $username)
            ]);
        }

        $salt = $this->security->generateSalt();

        $user = new User();
        $user->setUsername($username);
        $user->setSalt($salt);
        $user->setPasswd($this->security->serialize($password, $salt));
        $this->entityManager->persist($user);

        $syncState = new UserSyncState();
        $syncState->setUser($user);
        $this->entityManager->persist($syncState);

        $this->entityManager->flush();

        return $this->createSession($user->getUserInfo());
    }

    /**
     * @param string $authToken
     * @return AuthResult
     */
    public function getSession($authToken)
    {
        $userInfo = $this->getSessionUser($authToken);
        return $this->createAuthResult($userInfo);
    }

    /**
     * @param UserInfo $userInfo
     * @return AuthResult
     */
    private function createAuthResult(UserInfo $userInfo)
    {
        $authToken = $this->security->generateSalt();

        $authResult = new AuthResult();
        $authResult->userInfo = $userInfo;
        $authResult->authToken = $authToken;

        $this->saveSession($authToken, $userInfo);

        return $authResult;
    }

    /**
     * @param UserInfo $userInfo
     * @return AuthResult
     */
    private function createSession(UserInfo $userInfo)
    {
        session_start();
        $this->logger->debug(sprintf('The user "%s" created a session.', $userInfo->username));
        return $this->createAuthResult($userInfo);
    }
}
