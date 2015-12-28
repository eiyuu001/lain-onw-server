<?php

namespace Lain\OneNightWerewolfBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ginq\Ginq;
use Lain\OneNightWerewolfBundle\Util\Roles;
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
     * @var Room
     *
     * @ORM\ManyToOne(targetEntity="Room", inversedBy="games")
     * @ORM\JoinColumn(name="room_id", referencedColumnName="id", nullable=FALSE)
     * @JMS\Exclude
     */
    private $room;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="GamePlayer", mappedBy="game", cascade={"persist", "remove"})
     * @JMS\SerializedName("players")
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
     * Set room
     *
     * @param Room $room
     *
     * @return Game
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
     * Add gamePlayer
     *
     * @param GamePlayer $gamePlayer
     *
     * @return Game
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

    public function isVillagerGroupWon() {
        if ($this->isHangedManGroupWon()) {
            return false;
        }
        $oneOrMoreWerewolfWasDead = Ginq::from($this->gamePlayers)->filter(function(GamePlayer $gamePlayer) {
            return !$gamePlayer->isAlive();
        })->any(function(GamePlayer $deadGamePlayer) {
            return $deadGamePlayer->getActualRole()->getId() == Roles::WEREWOLF;
        });
        return $oneOrMoreWerewolfWasDead;
    }

    public function isWerewolfGroupWon() {
        if ($this->isHangedManGroupWon()) {
            return false;
        }
        $oneOrMoreWerewolfWasDead = Ginq::from($this->gamePlayers)->filter(function(GamePlayer $gamePlayer) {
            return !$gamePlayer->isAlive();
        })->any(function(GamePlayer $deadGamePlayer) {
            return $deadGamePlayer->getActualRole()->getId() == Roles::WEREWOLF;
        });
        return !$oneOrMoreWerewolfWasDead;
    }

    public function isHangedManGroupWon() {
        $oneOrMoreHangedManWasDead = Ginq::from($this->gamePlayers)->filter(function(GamePlayer $gamePlayer) {
            return !$gamePlayer->isAlive();
        })->any(function(GamePlayer $deadGamePlayer) {
            return $deadGamePlayer->getActualRole()->getId() == Roles::HANGED_MAN;
        });
        return $oneOrMoreHangedManWasDead;
    }

    public function castRoles() {
        $roles = Ginq::from($this->shuffleRoles())
            ->take($this->room->getPlayers()->count())
            ->toList();
        $pairs = array_map(null, $roles, $this->room->getPlayers()->getValues());
        foreach($pairs as list($role, $player)) {
            $gamePlayer = new GamePlayer($this, $player);
            $gamePlayer->setRole($role);
        }
    }

    private function shuffleRoles() {
        $roles = Ginq::from($this->room->getRoleConfigs())->flatMap(function(RoleConfig $roleConfig){
            return Ginq::repeat($roleConfig->getRole(), $roleConfig->getCount());
        })->toList();
        shuffle($roles);
        return $roles;
    }
}
