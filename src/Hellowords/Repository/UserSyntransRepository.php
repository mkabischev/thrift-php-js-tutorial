<?php

namespace Hellowords\Repository;

use Doctrine\ORM\EntityRepository;
use Hellowords\Model\UserSyntrans;

class UserSyntransRepository extends EntityRepository
{
    public function findByUserAfterUSN($userId, $updateSequenceNumber, $maxEntities)
    {
        $qb = $this->_em->createQueryBuilder();

        $query = $qb
            ->select(['s', 'w', 't'])
            ->from(UserSyntrans::class, 's')
            ->join('s.word', 'w')
            ->join('s.trans', 't')
            ->where(
                $qb->expr()->eq('s.user', $userId),
                $qb->expr()->gt('s.updateSequenceNumber', $updateSequenceNumber)
            )
            ->orderBy('s.updateSequenceNumber', 'ASC')
            ->setMaxResults($maxEntities)
            ->getQuery();

        return $query->getResult();
    }
}
