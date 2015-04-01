<?php

namespace Hellowords\Service;

use Doctrine\ORM\EntityManagerInterface;
use Hellowords\Component\SessionTrait;
use Hellowords\Model\UserSyncState;
use Hellowords\Model\UserSyntrans;
use Hellowords\Repository\UserSyncStateRepository;
use Hellowords\Repository\UserSyntransRepository;
use Hellowords\SyncChunk;
use Hellowords\SynchronizerIf;
use Hellowords\SyncState;
use Psr\Log\LoggerInterface;

class Synchronizer implements SynchronizerIf
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
     * @return UserSyncStateRepository
     */
    protected function getUserSyncStateRepository()
    {
        return $this->entityManager->getRepository(UserSyncState::class);
    }

    /**
     * @return UserSyntransRepository
     */
    protected function getUserSyntransRepository()
    {
        return $this->entityManager->getRepository(UserSyntrans::class);
    }

    protected function loadSyntranses(SyncChunk $chunk, $userId, $afterUSN, $maxEntities)
    {
        $userSyntranses = $this->getUserSyntransRepository()->findByUserAfterUSN($userId, $afterUSN, $maxEntities);

        foreach ($userSyntranses as $userSyntrans) {
            /* @var $userSyntrans UserSyntrans */
            $syntrans = $userSyntrans->getSyntrans();
            $chunk->chunkHighUSN = max($chunk->chunkHighUSN, $syntrans->updateSequenceNum);
            $chunk->syntransList[] = $syntrans;
        }
    }

    public function getSyncChunk($authToken, $afterUSN, $maxEntities)
    {
        $userInfo = $this->getSessionUser($authToken);

        $syncChunk = new SyncChunk();
        $syncChunk->time = time();

        $this->loadSyntranses($syncChunk, $userInfo->id, $afterUSN, $maxEntities);

        $userSyncState = $this->getUserSyncStateRepository()->findOneByUser($userInfo->id);
        /* @var $userSyncState UserSyncState */

        $syncChunk->updateCount = $userSyncState->getUpdateSequenceNumber();

        $this->logger->debug(sprintf('The user "%s" synced after USN "%s".', $userInfo->id, $afterUSN));

        return $syncChunk;
    }

    public function getSyncState($authToken)
    {
        $userInfo = $this->getSessionUser($authToken);

        $userSyncState = $this->getUserSyncStateRepository()->findOneByUser($userInfo->id);
        /* @var $userSyncState UserSyncState */

        $syncState = new SyncState();
        $syncState->fullSyncBefore = $userSyncState->getFullSyncBefore()->getTimestamp();
        $syncState->updateCount = $userSyncState->getUpdateSequenceNumber();
        $syncState->time = time();

        return $syncState;
    }
}
