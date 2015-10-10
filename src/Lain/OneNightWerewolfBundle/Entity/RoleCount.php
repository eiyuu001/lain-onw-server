<?php

namespace Lain\OneNightWerewolfBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as JMS;

/**
 * RoleCount
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class RoleCount
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @JMS\Exclude
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Regulation", inversedBy="role_counts")
     * @ORM\JoinColumn(name="regulation_id", referencedColumnName="id", nullable=FALSE)
     */
    private $regulation;

    /**
     * @ORM\ManyToOne(targetEntity="Role")
     * @ORM\JoinColumn(name="role_id", referencedColumnName="id", nullable=FALSE)
     */
    private $role;

    /**
     * @ORM\Column(type="integer", name="count")
     */
    private $count;

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
     * Set count
     *
     * @param integer $count
     *
     * @return RoleCount
     */
    public function setCount($count)
    {
        $this->count = $count;

        return $this;
    }

    /**
     * Get count
     *
     * @return integer
     */
    public function getCount()
    {
        return $this->count;
    }

    /**
     * Set regulation
     *
     * @param \Lain\OneNightWerewolfBundle\Entity\Regulation $regulation
     *
     * @return RoleCount
     */
    public function setRegulation(\Lain\OneNightWerewolfBundle\Entity\Regulation $regulation)
    {
        $this->regulation = $regulation;

        return $this;
    }

    /**
     * Get regulation
     *
     * @return \Lain\OneNightWerewolfBundle\Entity\Regulation
     */
    public function getRegulation()
    {
        return $this->regulation;
    }

    /**
     * Set role
     *
     * @param \Lain\OneNightWerewolfBundle\Entity\Role $role
     *
     * @return RoleCount
     */
    public function setRole(\Lain\OneNightWerewolfBundle\Entity\Role $role)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * Get role
     *
     * @return \Lain\OneNightWerewolfBundle\Entity\Role
     */
    public function getRole()
    {
        return $this->role;
    }
}
