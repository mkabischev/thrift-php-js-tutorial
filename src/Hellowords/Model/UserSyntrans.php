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
     * @ManyToOne(targetEntity="User", inversedBy="syntranses", fetch="LAZY", cascade={"persist"})
     * @var User
     */
    protected $user;

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

    public function getSyntrans()
    {
        return new Syntrans([
            'id' => $this->getId(),
            'word' => $this->getWord()->getExpression(),
            'trans' => $this->getTrans()->getExpression(),
            'updateSequenceNum' => $this->getUpdateSequenceNumber()
        ]);
    }
}
