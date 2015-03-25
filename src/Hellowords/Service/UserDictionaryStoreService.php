<?php

namespace Hellowords\Service;

use Doctrine\ORM\EntityManagerInterface;
use Hellowords\Component\SessionTrait;
use Hellowords\Expression;
use Hellowords\InvalidRequestException;
use Hellowords\Language;
use Hellowords\Model\User;
use Hellowords\Model\UserExpression;
use Hellowords\Model\UserSyntrans;
use Hellowords\NotFoundException;
use Hellowords\Repository\UserExpressionRepository;
use Hellowords\Repository\UserRepository;
use Hellowords\Repository\UserSyntransRepository;
use Hellowords\Syntrans;
use Hellowords\UserDictionaryStoreIf;
use Hellowords\UserInfo;
use Psr\Log\LoggerInterface;

class UserDictionaryStoreService implements UserDictionaryStoreIf
{
    use SessionTrait;

    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var LoggerInterface */
    private $logger;

    public function __construct(EntityManagerInterface $entityManager, LoggerInterface $logger)
    {
        $this->entityManager = $entityManager;
        $this->logger = $logger;
    }

    /**
     * @return UserExpressionRepository
     */
    protected function getUserExpressionRepository()
    {
        return $this->entityManager->getRepository(UserExpression::class);
    }

    /**
     * @return UserSyntransRepository
     */
    protected function getUserSyntransRepository()
    {
        return $this->entityManager->getRepository(UserSyntrans::class);
    }

    /**
     * @return UserRepository
     */
    protected function getUserRepository()
    {
        return $this->entityManager->getRepository(User::class);
    }

    protected function incrementSequenceNumber(User $user)
    {
        $syncState = $user->getSyncState();
        $syncState->incrementSequenceNumber();
        $this->entityManager->persist($syncState);
        return $syncState->getUpdateSequenceNumber();
    }

    protected function findOrCreateExpression(User $user, Expression $expr)
    {
        if (empty($expr->chars)) {
            throw new InvalidRequestException([
                'message' => 'Empty expression'
            ]);
        }

        if (!isset(Language::$__names[$expr->lang])) {
            throw new InvalidRequestException([
                'message' => sprintf('Unknown language type %s', $expr->lang)
            ]);
        }

        $langName = Language::$__names[$expr->lang];

        $userExpr = $this->getUserExpressionRepository()->findOneBy([
            'chars' => $expr->chars,
            'lang' => $langName,
            'user' => $user
        ]);

        if (!$userExpr) {
            $userExpr = new UserExpression();
            $userExpr->setChars($expr->chars);
            $userExpr->setLang($langName);
            $userExpr->setUser($user);
            $this->entityManager->persist($userExpr);
        }

        return $userExpr;
    }

    protected function persistUserSyntrans(User $user, UserSyntrans $userSyntrans)
    {
        $userSyntrans->setUpdateSequenceNumber($this->incrementSequenceNumber($user));
        $this->entityManager->persist($userSyntrans);
        $this->entityManager->flush();
    }

    protected function addWord(UserInfo $userInfo, Expression $word, Expression $trans)
    {
        if ($word->lang === $trans->lang) {
            throw new InvalidRequestException([
                'message' => 'Translation language should be different'
            ]);
        }

        $user = $this->getUserRepository()->find($userInfo->id);
        /* @var $user User */

        $wordExpr = $this->findOrCreateExpression($user, $word);
        $transExpr = $this->findOrCreateExpression($user, $trans);

        if ($wordExpr->getId() && $transExpr->getId()) {
            $userSyntrans = $this->getUserSyntransRepository()->findOneBy([
                'word' => $wordExpr,
                'trans' => $transExpr,
                'user' => $user
            ]);
            /* @var $userSyntrans UserSyntrans */
            if ($userSyntrans) {
                if ($userSyntrans->restore()) {
                    $this->persistUserSyntrans($user, $userSyntrans);
                }
                return $userSyntrans->getSyntrans();
            }
        }

        $userSyntrans = new UserSyntrans();
        $userSyntrans->setWord($wordExpr);
        $userSyntrans->setTrans($transExpr);
        $userSyntrans->setUser($user);

        $this->persistUserSyntrans($user, $userSyntrans);

        return $userSyntrans->getSyntrans();
    }

    public function createSyntrans($authToken, Syntrans $syntrans)
    {
        return $this->addWord($this->getSessionUser($authToken), $syntrans->word, $syntrans->trans);
    }

    public function deleteSyntrans($authToken, $guid)
    {
        $userInfo = $this->getSessionUser($authToken);

        $user = $this->getUserRepository()->find($userInfo->id);
        /* @var $user User */

        $userSyntrans = $this->getUserSyntransRepository()->find($guid);
        /* @var $userSyntrans UserSyntrans */

        if (!$userSyntrans) {
            throw new NotFoundException([
                'message' => sprintf('The syntrans "%s" not found', $guid)
            ]);
        }

        $userSyntrans->setDeletedAt(new \DateTime());

        $this->persistUserSyntrans($user, $userSyntrans);

        return $userSyntrans->getUpdateSequenceNumber();
    }
}
