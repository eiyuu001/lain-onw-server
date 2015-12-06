<?php

namespace Lain\OneNightWerewolfBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ginq\Ginq;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as JMS;

/**
 * Game
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Game
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
     * @ORM\ManyToOne(targetEntity="Room", inversedBy="games")
     * @ORM\JoinColumn(name="room_id", referencedColumnName="id", nullable=FALSE)
     * @JMS\Exclude
     */
    private $room;

    /**
     * @ORM\ManyToOne(targetEntity="Regulation")
     * @ORM\JoinColumn(name="regulation_id", referencedColumnName="id", nullable=FALSE)
     */
    private $regulation;

    /**
     * @ORM\OneToMany(targetEntity="GamePlayer", mappedBy="game", cascade={"persist", "remove"})
     */
    private $gamePlayers;

    /**
     * Constructor
     * 
     * @param Room
     */
    public function __construct($room)
    {
        $this->setRoom($room);
        $this->gamePlayers = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set room
     *
     * @param \Lain\OneNightWerewolfBundle\Entity\Room $room
     *
     * @return Game
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
     * Set regulation
     *
     * @param \Lain\OneNightWerewolfBundle\Entity\Regulation $regulation
     *
     * @return Game
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
     * Add gamePlayer
     *
     * @param \Lain\OneNightWerewolfBundle\Entity\GamePlayer $gamePlayer
     *
     * @return Game
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

    /**
     * @JMS\VirtualProperty
     * @JMS\SerializedName("finished")
     *
     * @return bool
     */
    public function hasFinished()
    {
        return Ginq::from($this->gamePlayers)->all(function(GamePlayer $gamePlayer) {
            return $gamePlayer->getVoteDestination() !== null;
        });
    }

    public function isPeopleWon() {
        if ($this->isHangedManWon()) {
            return false; // í›êlÇÃèüóòÇÕÇ∑Ç◊ÇƒÇ…óDêÊÇ∑ÇÈ
        }
        $oneOrMoreWerewolfWasDead = Ginq::from($this->gamePlayers)->filter(function(GamePlayer $gamePlayer) {
            return !$gamePlayer->isAlive();
        })->any(function(GamePlayer $deadGamePlayer) {
            return $deadGamePlayer->getActualRole()->getId() == 1; // êlòT
        });
        return $oneOrMoreWerewolfWasDead;
    }

    public function isWerewolfWon() {
        if ($this->isHangedManWon()) {
            return false; // í›êlÇÃèüóòÇÕÇ∑Ç◊ÇƒÇ…óDêÊÇ∑ÇÈ
        }
        $oneOrMoreWerewolfWasDead = Ginq::from($this->gamePlayers)->filter(function(GamePlayer $gamePlayer) {
            return !$gamePlayer->isAlive();
        })->any(function(GamePlayer $deadGamePlayer) {
            return $deadGamePlayer->getActualRole()->getId() == 1; // êlòT
        });
        return !$oneOrMoreWerewolfWasDead;
    }

    public function isHangedManWon() {
        $oneOrMoreHangedManWasDead = Ginq::from($this->gamePlayers)->filter(function(GamePlayer $gamePlayer) {
            return !$gamePlayer->isAlive();
        })->any(function(GamePlayer $deadGamePlayer) {
            return $deadGamePlayer->getActualRole()->getId() == 6; // í›êl
        });
        return $oneOrMoreHangedManWasDead;
    }


}
