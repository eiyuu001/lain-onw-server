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
     * @JMS\Groups({"Default", "postPlayer"})
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity="Room", inversedBy="Players")
     * @ORM\JoinColumn(name="room_id", referencedColumnName="id", nullable=FALSE)
     * @JMS\Exclude
     */
    private $room;

    /**
     * @ORM\OneToMany(targetEntity="GamePlayer", mappedBy="player", cascade={"persist", "remove"})
     * @JMS\Exclude
     */
    private $gamePlayers;

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
        return Ginq::from($this->getGamePlayers())->sum(function(GamePlayer $gamePlayer) {
            return $gamePlayer->computeReward();
        });
    }

    /**
     * Constructor
     * 
     * @param Room $room
     */
    public function __construct($room)
    {
        $this->setRoom($room);
        $this->gamePlayers = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add gamePlayer
     *
     * @param \Lain\OneNightWerewolfBundle\Entity\GamePlayer $gamePlayer
     *
     * @return Player
     */
    public function addGamePlayer(\Lain\OneNightWerewolfBundle\Entity\GamePlayer $gamePlayer)
    {
        $this->gamePlayers[] = $gamePlayer;

        return $this;
    }

    /**
     * Remove gamePlayer
     *
     * @param \Lain\OneNightWerewolfBundle\Entity\GamePlayer $gamePlayer
     */
    public function removeGamePlayer(\Lain\OneNightWerewolfBundle\Entity\GamePlayer $gamePlayer)
    {
        $this->gamePlayers->removeElement($gamePlayer);
    }

    /**
     * Get gamePlayers
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getGamePlayers()
    {
        return $this->gamePlayers;
    }
}
