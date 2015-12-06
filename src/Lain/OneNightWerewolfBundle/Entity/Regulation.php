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
     * @ORM\OneToMany(targetEntity="RoleConfig", mappedBy="regulation", cascade={"persist", "remove"})
     */
    private $roleConfigs;

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
     * 
     * @param Room $room
     */
    public function __construct($room)
    {
        $this->setRoom($room);
        $this->roleConfigs = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add roleConfig
     *
     * @param \Lain\OneNightWerewolfBundle\Entity\RoleConfig $roleConfig
     *
     * @return Regulation
     */
    public function addRoleConfig(\Lain\OneNightWerewolfBundle\Entity\RoleConfig $roleConfig)
    {
        $this->roleConfigs[] = $roleConfig;

        return $this;
    }

    /**
     * Remove roleConfig
     *
     * @param \Lain\OneNightWerewolfBundle\Entity\RoleConfig $roleConfig
     */
    public function removeRoleConfig(\Lain\OneNightWerewolfBundle\Entity\RoleConfig $roleConfig)
    {
        $this->roleConfigs->removeElement($roleConfig);
    }

    /**
     * Get roleConfigs
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRoleConfigs()
    {
        return $this->roleConfigs;
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
