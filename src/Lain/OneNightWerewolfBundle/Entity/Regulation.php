<?php

namespace Lain\OneNightWerewolfBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as JMS;

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
     * @ORM\ManyToOne(targetEntity="Room", inversedBy="Regulations")
     * @ORM\JoinColumn(name="room_id", referencedColumnName="id", nullable=FALSE)
     * @JMS\Exclude
     */
    private $room;

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

    /**
     * Set room
     *
     * @param \Lain\OneNightWerewolfBundle\Entity\Room $room
     *
     * @return Regulation
     */
    public function setRoom(\Lain\OneNightWerewolfBundle\Entity\Room $room)
    {
        $this->room = $room;

        return $this;
    }

    /**
     * Get room
     *
     * @return \Lain\OneNightWerewolfBundle\Entity\Room
     */
    public function getRoom()
    {
        return $this->room;
    }
}
