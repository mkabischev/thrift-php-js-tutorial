<?php

namespace Hellowords\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Hellowords\UserInfo;

/**
 * @Entity(repositoryClass="Hellowords\Repository\UserRepository")
 * @Table(name="user")
 */
class User
{
    /**
     * @Id
     * @Column(type="bigint")
     * @GeneratedValue(strategy="AUTO")
     * @var int
     */
    protected $id;

    /**
     * @Column(type="string", length=50)
     * @var string
     */
    protected $username;

    /**
     * @Column(type="string", name="passwd_hash", length=32, options={"fixed":true})
     * @var string
     */
    protected $passwd;

    /**
     * @Column(type="string", length=32, options={"fixed":true})
     * @var string
     */
    protected $salt;

    /**
     * @OneToMany(targetEntity="UserSyntrans", mappedBy="user", cascade={"persist"})
     * @var ArrayCollection
     */
    protected $syntranses;

    /**
     * @OneToOne(targetEntity="UserSyncState", fetch="LAZY", mappedBy="user", cascade={"persist"})
     * @var UserSyncState
     */
    protected $syncState;

    public function __construct()
    {
        $this->syntranses = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function setUsername($username)
    {
        $this->username = $username;
        return $this;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function setPasswd($passwd)
    {
        $this->passwd = $passwd;
        return $this;
    }

    public function getPasswd()
    {
        return $this->passwd;
    }

    public function setSalt($salt)
    {
        $this->salt = $salt;
        return $this;
    }

    public function getSalt()
    {
        return $this->salt;
    }

    public function getSyncState()
    {
        return $this->syncState;
    }

    public function getSyntranses()
    {
        return $this->syntranses;
    }

    public function getUserInfo()
    {
        return new UserInfo([
            'id' => $this->getId(),
            'username' => $this->getUsername()
        ]);
    }
}
