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
     * @Assert\NotBlank()
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
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
}
