<?php

namespace Hellowords\Model;

/**
 * @Entity(repositoryClass="Hellowords\Repository\UserSyncStateRepository")
 * @Table(name="user_sync_state")
 * @HasLifecycleCallbacks
 */
class UserSyncState
{
    /**
     * @Id
     * @Column(type="bigint")
     * @GeneratedValue(strategy="AUTO")
     * @var int
     */
    protected $id;

    /**
     * @OneToOne(targetEntity="User", fetch="LAZY", inversedBy="syncState", cascade={"persist"})
     * @var User
     */
    protected $user;

    /**
     * @Column(type="bigint", name="update_sequence_number", options={"default":0})
     * @var int
     */
    protected $updateSequenceNumber;

    /**
     * @Column(type="datetime", name="full_sync_before")
     * @var \DateTime
     */
    protected $fullSyncBefore;

    public function __construct()
    {
        $this->updateSequenceNumber = 0;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function setUser(User $user)
    {
        $this->user = $user;
        return $this;
    }

    public function incrementSequenceNumber()
    {
        $this->updateSequenceNumber++;
        return $this;
    }

    public function getUpdateSequenceNumber()
    {
        return $this->updateSequenceNumber;
    }

    public function getFullSyncBefore()
    {
        return $this->fullSyncBefore;
    }

    public function setFullSyncBefore(\DateTime $datetime)
    {
        $this->fullSyncBefore = $datetime;
        return $this;
    }

    /**
     * @PrePersist
     */
    public function updateFullSyncBefore()
    {
        if ($this->getFullSyncBefore() === null) {
            $this->setFullSyncBefore(new \DateTime());
        }
    }
}
