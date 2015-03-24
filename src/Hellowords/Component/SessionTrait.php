<?php

namespace Hellowords\Component;

use Hellowords\AccessViolationException;
use Hellowords\UserInfo;

trait SessionTrait
{
    /**
     * @param string $authToken
     * @return UserInfo
     * @throws AccessViolationException
     */
    public function getSessionUser($authToken)
    {
        session_start();

        if (empty($_SESSION['authToken']) || $_SESSION['authToken'] !== $authToken) {
            throw new AccessViolationException([
                'message' => 'AuthToken is corrupted'
            ]);
        }

        return $_SESSION['userInfo'];
    }

    public function saveSession($authToken, UserInfo $userInfo)
    {
        $_SESSION['authToken'] = $authToken;
        $_SESSION['userInfo'] = $userInfo;
    }
}
