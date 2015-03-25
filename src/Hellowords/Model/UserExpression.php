<?php

namespace Hellowords\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Hellowords\Expression;

/**
 * @Entity(repositoryClass="Hellowords\Repository\UserExpressionRepository")
 * @Table(name="user_expression")
 */
class UserExpression
{
    /**
     * @Id
     * @Column(type="bigint")
     * @GeneratedValue(strategy="AUTO")
     * @var int
     */
    protected $id;

    /**
     * @Column(type="string", length=255)
     * @var string
     */
    protected $chars;

    /**
     * @Column(type="string", length=5)
     * @var string
     */
    protected $lang;

    /**
     * @ManyToOne(targetEntity="User", fetch="LAZY", cascade={"persist"})
     * @JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
     * @var User
     */
    protected $user;

    /**
     * @OneToMany(targetEntity="UserSyntrans", mappedBy="word")
     * @var ArrayCollection
     */
    protected $syntransAsWord;

    /**
     * @OneToMany(targetEntity="UserSyntrans", mappedBy="trans")
     * @var ArrayCollection
     */
    protected $syntransAsTrans;

    public function __construct()
    {
        $this->syntransAsWord = new ArrayCollection();
        $this->syntransAsTrans = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getChars()
    {
        return $this->chars;
    }

    public function setChars($chars)
    {
        $this->chars = $chars;
        return $this;
    }

    public function getLang()
    {
        return $this->lang;
    }

    public function setLang($lang)
    {
        $this->lang = $lang;
        return $this;
    }

    public function setUser(User $user)
    {
        $this->user = $user;
        return $this;
    }

    public function getExpression()
    {
        $expr = new Expression();
        $expr->id = $this->getId();
        $expr->chars = $this->getChars();
        $expr->lang = constant('Hellowords\Language::' . $this->getLang());
        return $expr;
    }
}
