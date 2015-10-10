<?php

namespace Lain\OneNightWerewolfBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Regulation
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Regulation
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
     * @ORM\OneToMany(targetEntity="RoleCount", mappedBy="regulation", cascade={"persist", "remove"})
     */
    private $roleCounts;

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
     * Constructor
     */
    public function __construct()
    {
        $this->roleCounts = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add roleCount
     *
     * @param \Lain\OneNightWerewolfBundle\Entity\RoleCount $roleCount
     *
     * @return Regulation
     */
    public function addRoleCount(\Lain\OneNightWerewolfBundle\Entity\RoleCount $roleCount)
    {
        $this->roleCounts[] = $roleCount;

        return $this;
    }

    /**
     * Remove roleCount
     *
     * @param \Lain\OneNightWerewolfBundle\Entity\RoleCount $roleCount
     */
    public function removeRoleCount(\Lain\OneNightWerewolfBundle\Entity\RoleCount $roleCount)
    {
        $this->roleCounts->removeElement($roleCount);
    }

    /**
     * Get roleCounts
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRoleCounts()
    {
        return $this->roleCounts;
    }
}
