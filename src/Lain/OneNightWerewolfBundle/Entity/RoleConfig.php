<?php

namespace Lain\OneNightWerewolfBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as JMS;

/**
 * RoleConfig
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class RoleConfig
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
     * @var Room
     *
     * @ORM\ManyToOne(targetEntity="Room", inversedBy="roleConfigs")
     * @ORM\JoinColumn(name="room_id", referencedColumnName="id", nullable=FALSE)
     * @JMS\Exclude
     */
    private $room;

    /**
     * @var Role
     *
     * @ORM\ManyToOne(targetEntity="Role")
     * @ORM\JoinColumn(name="role_id", referencedColumnName="id", nullable=FALSE)
     * @JMS\Inline()
     */
    private $role;

    /**
     * @var integer
     *
     * @Assert\NotBlank()
     * @ORM\Column(name="count", type="integer")
     * @JMS\Groups({"Default", "postRoom"})
     */
    private $count;

    /**
     * @var integer
     *
     * @Assert\NotBlank()
     * @ORM\Column(name="reward_for_survivor", type="integer")
     * @JMS\SerializedName("rewardForSurvivor")
     * @JMS\Groups({"Default", "postRoom"})
     */
    private $rewardForSurvivor;

    /**
     * @var integer
     *
     * @Assert\NotBlank()
     * @ORM\Column(name="reward_for_dead", type="integer")
     * @JMS\SerializedName("rewardForDead")
     * @JMS\Groups({"Default", "postRoom"})
     */
    private $rewardForDead;

    /**
     * Constructor
     *
     * @param Room $room
     * @param Role $role
     */
    public function __construct(Room $room, Role $role)
    {
        $this->setRoom($room);
        $room->addRoleConfig($this);

        $this->setRole($role);
    }

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
     * @return RoleConfig
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
     * Set room
     *
     * @param Room $room
     *
     * @return RoleConfig
     */
    public function setRoom(Room $room)
    {
        $this->room = $room;

        return $this;
    }

    /**
     * Get room
     *
     * @return Room
     */
    public function getRoom()
    {
        return $this->room;
    }

    /**
     * Set role
     *
     * @param Role $role
     *
     * @return RoleConfig
     */
    public function setRole(Role $role)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * Get role
     *
     * @return Role
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * Set rewardForSurvivor
     *
     * @param integer $rewardForSurvivor
     *
     * @return RoleConfig
     */
    public function setRewardForSurvivor($rewardForSurvivor)
    {
        $this->rewardForSurvivor = $rewardForSurvivor;

        return $this;
    }

    /**
     * Get rewardForSurvivor
     *
     * @return integer
     */
    public function getRewardForSurvivor()
    {
        return $this->rewardForSurvivor;
    }

    /**
     * Set rewardForDead
     *
     * @param integer $rewardForDead
     *
     * @return RoleConfig
     */
    public function setRewardForDead($rewardForDead)
    {
        $this->rewardForDead = $rewardForDead;

        return $this;
    }

    /**
     * Get rewardForDead
     *
     * @return integer
     */
    public function getRewardForDead()
    {
        return $this->rewardForDead;
    }
}
