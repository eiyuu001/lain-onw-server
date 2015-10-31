<?php

namespace Lain\OneNightWerewolfBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ginq\Ginq;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as JMS;

/**
 * Player
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Player
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
     * @ORM\ManyToOne(targetEntity="Room", inversedBy="Players")
     * @ORM\JoinColumn(name="room_id", referencedColumnName="id", nullable=FALSE)
     * @JMS\Exclude
     */
    private $room;

    /**
     * @ORM\OneToMany(targetEntity="PlayerRole", mappedBy="player", cascade={"persist", "remove"})
     */
    private $playerRoles;

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
     * @return Player
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
     * Set room
     *
     * @param \Lain\OneNightWerewolfBundle\Entity\Room $room
     *
     * @return Player
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

    /**
     * @JMS\VirtualProperty
     * @JMS\SerializedName("score")
     *
     * @return int
     */
    public function computeScore() {
        return Ginq::from($this->getPlayerRoles())->sum(function(PlayerRole $playerRole) {
            return $playerRole->computeReward();
        });
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->playerRoles = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add playerRole
     *
     * @param \Lain\OneNightWerewolfBundle\Entity\PlayerRole $playerRole
     *
     * @return Player
     */
    public function addPlayerRole(\Lain\OneNightWerewolfBundle\Entity\PlayerRole $playerRole)
    {
        $this->playerRoles[] = $playerRole;

        return $this;
    }

    /**
     * Remove playerRole
     *
     * @param \Lain\OneNightWerewolfBundle\Entity\PlayerRole $playerRole
     */
    public function removePlayerRole(\Lain\OneNightWerewolfBundle\Entity\PlayerRole $playerRole)
    {
        $this->playerRoles->removeElement($playerRole);
    }

    /**
     * Get playerRoles
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPlayerRoles()
    {
        return $this->playerRoles;
    }
}
