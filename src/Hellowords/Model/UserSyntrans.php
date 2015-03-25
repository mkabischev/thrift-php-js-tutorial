<?php

namespace Hellowords\Model;

use Hellowords\Syntrans;

/**
 * @Entity(repositoryClass="Hellowords\Repository\UserSyntransRepository")
 * @Table(name="user_syntrans")
 */
class UserSyntrans
{
    /**
     * @Id
     * @Column(type="bigint")
     * @GeneratedValue(strategy="AUTO")
     * @var int
     */
    protected $id;

    /**
     * @ManyToOne(targetEntity="UserExpression", cascade={"persist"}, fetch="EAGER")
     * @JoinColumn(name="word_id", referencedColumnName="id")
     * @var UserExpression
     */
    protected $word;

    /**
     * @ManyToOne(targetEntity="UserExpression", cascade={"persist"}, fetch="EAGER")
     * @JoinColumn(name="trans_id", referencedColumnName="id")
     * @var UserExpression
     */
    protected $trans;

    /**
     * @Column(type="bigint", name="update_sequence_number")
     * @var int
     */
    protected $updateSequenceNumber;

    /**
     * @Column(type="datetime", name="created_at")
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * @Column(type="datetime", name="deleted_at", nullable=true)
     * @var \DateTime
     */
    protected $deletedAt;

    /**
     * @ManyToOne(targetEntity="User", inversedBy="syntranses", fetch="LAZY", cascade={"persist"})
     * @var User
     */
    protected $user;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
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

    public function setWord(UserExpression $word)
    {
        $this->word = $word;
        return $this;
    }

    public function getWord()
    {
        return $this->word;
    }

    public function setTrans(UserExpression $trans)
    {
        $this->trans = $trans;
        return $this;
    }

    public function getTrans()
    {
        return $this->trans;
    }

    public function setUpdateSequenceNumber($updateSequenceNumber)
    {
        $this->updateSequenceNumber = $updateSequenceNumber;
        return $this;
    }

    public function getUpdateSequenceNumber()
    {
        return $this->updateSequenceNumber;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function setDeletedAt(\DateTime $datetime)
    {
        $this->deletedAt = $datetime;
        return $this;
    }

    public function getDeletedAt()
    {
        return $this->deletedAt;
    }

    public function restore()
    {
        if ($this->deletedAt) {
            $this->deletedAt = null;
            return true;
        }
        return false;
    }

    public function getSyntrans()
    {
        $syntrans = new Syntrans();
        $syntrans->id = $this->getId();
        $syntrans->word = $this->getWord()->getExpression();
        $syntrans->trans = $this->getTrans()->getExpression();
        $syntrans->updateSequenceNum = $this->getUpdateSequenceNumber();
        $syntrans->createdAt = $this->getCreatedAt()->getTimestamp();

        if (($deletedAt = $this->getDeletedAt())) {
            $syntrans->deletedAt = $deletedAt->getTimestamp();
        }

        return $syntrans;
    }
}
