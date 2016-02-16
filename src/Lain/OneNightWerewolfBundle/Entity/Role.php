<?php

namespace Lain\OneNightWerewolfBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as JMS;

/**
 * Role
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Role
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var bool
     *
     * @Assert\NotBlank()
     * @ORM\Column(name="votable", type="boolean", length=255)
     */
    private $votable;

    /**
     * @var bool
     *
     * @Assert\NotBlank()
     * @ORM\Column(name="peepable", type="boolean", length=255)
     */
    private $peepable;

    /**
     * @var bool
     *
     * @Assert\NotBlank()
     * @ORM\Column(name="swappable", type="boolean", length=255)
     */
    private $swappable;

    /**
     * @var RoleGroup
     *
     * @ORM\ManyToOne(targetEntity="RoleGroup", inversedBy="roles")
     * @ORM\JoinColumn(name="role_group_id", referencedColumnName="id", nullable=FALSE)
     * @JMS\SerializedName("roleGroup")
     */
    private $roleGroup;


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Role
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set roleGroup
     *
     * @param RoleGroup $roleGroup
     *
     * @return Role
     */
    public function setRoleGroup(RoleGroup $roleGroup)
    {
        $this->roleGroup = $roleGroup;

        return $this;
    }

    /**
     * Get roleGroup
     *
     * @return RoleGroup
     */
    public function getRoleGroup()
    {
        return $this->roleGroup;
    }

    /**
     * Set votable
     *
     * @param boolean $votable
     *
     * @return Role
     */
    public function setVotable($votable)
    {
        $this->votable = $votable;

        return $this;
    }

    /**
     * Get votable
     *
     * @return boolean
     */
    public function getVotable()
    {
        return $this->votable;
    }

    /**
     * Set peepable
     *
     * @param boolean $peepable
     *
     * @return Role
     */
    public function setPeepable($peepable)
    {
        $this->peepable = $peepable;

        return $this;
    }

    /**
     * Get peepable
     *
     * @return boolean
     */
    public function getPeepable()
    {
        return $this->peepable;
    }

    /**
     * Set swappable
     *
     * @param boolean $swappable
     *
     * @return Role
     */
    public function setSwappable($swappable)
    {
        $this->swappable = $swappable;

        return $this;
    }

    /**
     * Get swappable
     *
     * @return boolean
     */
    public function getSwappable()
    {
        return $this->swappable;
    }
}
