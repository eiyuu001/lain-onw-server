<?php

namespace Lain\OneNightWerewolfBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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
     * @JMS\Groups({"Default", "getRoom"})
     */
    private $id;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @ORM\Column(name="name", type="string", length=255)
     * @JMS\Groups({"Default", "postPlayer", "getRoom"})
     */
    private $name;

    /**
     * @var Room
     *
     * @ORM\ManyToOne(targetEntity="Room", inversedBy="Players")
     * @ORM\JoinColumn(name="room_id", referencedColumnName="id", nullable=FALSE)
     * @JMS\Exclude
     */
    private $room;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="GamePlayer", mappedBy="player", cascade={"persist", "remove"})
     * @JMS\Exclude
     */
    private $gamePlayers;

    /**
     * Constructor
     *
     * @param Room $room
     */
    public function __construct($room)
    {
        $this->setRoom($room);
        $this->gamePlayers = new ArrayCollection();
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
     * @param Room $room
     *
     * @return Player
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
     * @JMS\VirtualProperty
     * @JMS\SerializedName("score")
     * @JMS\Groups({"Default", "getRoom"})
     *
     * @return int
     */
    public function computeScore() {
        return Ginq::from($this->getGamePlayers())->sum(function(GamePlayer $gamePlayer) {
            return $gamePlayer->computeReward();
        });
    }

    /**
     * Add gamePlayer
     *
     * @param GamePlayer $gamePlayer
     *
     * @return Player
     */
    public function addGamePlayer(GamePlayer $gamePlayer)
    {
        $this->gamePlayers[] = $gamePlayer;

        return $this;
    }

    /**
     * Remove gamePlayer
     *
     * @param GamePlayer $gamePlayer
     */
    public function removeGamePlayer(GamePlayer $gamePlayer)
    {
        $this->gamePlayers->removeElement($gamePlayer);
    }

    /**
     * Get gamePlayers
     *
     * @return Collection
     */
    public function getGamePlayers()
    {
        return $this->gamePlayers;
    }
}
