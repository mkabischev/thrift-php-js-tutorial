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
        session_id($authToken);
        session_start();

        if (empty($_SESSION['userInfo'])) {
            throw new AccessViolationException([
                'message' => 'AuthToken is corrupted'
            ]);
        }

        return $_SESSION['userInfo'];
    }
}
