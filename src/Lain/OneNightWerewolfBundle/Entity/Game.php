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
     * @var Room
     *
     * @ORM\ManyToOne(targetEntity="Room", inversedBy="games")
     * @ORM\JoinColumn(name="room_id", referencedColumnName="id", nullable=FALSE)
     * @JMS\Exclude
     */
    private $room;

    /**
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
            return false; // �ݐl�̏����͂��ׂĂɗD�悷��
        }
        $oneOrMoreWerewolfWasDead = Ginq::from($this->gamePlayers)->filter(function(GamePlayer $gamePlayer) {
            return !$gamePlayer->isAlive();
        })->any(function(GamePlayer $deadGamePlayer) {
            return $deadGamePlayer->getActualRole()->getId() == 1; // �l�T
        });
        return $oneOrMoreWerewolfWasDead;
    }

    public function isWerewolfWon() {
        if ($this->isHangedManWon()) {
            return false; // �ݐl�̏����͂��ׂĂɗD�悷��
        }
        $oneOrMoreWerewolfWasDead = Ginq::from($this->gamePlayers)->filter(function(GamePlayer $gamePlayer) {
            return !$gamePlayer->isAlive();
        })->any(function(GamePlayer $deadGamePlayer) {
            return $deadGamePlayer->getActualRole()->getId() == 1; // �l�T
        });
        return !$oneOrMoreWerewolfWasDead;
    }

    public function isHangedManWon() {
        $oneOrMoreHangedManWasDead = Ginq::from($this->gamePlayers)->filter(function(GamePlayer $gamePlayer) {
            return !$gamePlayer->isAlive();
        })->any(function(GamePlayer $deadGamePlayer) {
            return $deadGamePlayer->getActualRole()->getId() == 6; // �ݐl
        });
        return $oneOrMoreHangedManWasDead;
    }

    public function castRoles() {
        $roles = Ginq::from($this->shuffleRoles())->take($this->room->getPlayers()->count())->toList();
        array_map(function(Role $role, Player $player) {
            $gamePlayer = new GamePlayer($this, $player);
            $gamePlayer->setRole($role);
        }, $roles, $this->room->getPlayers()->getValues());
    }

    private function shuffleRoles() {
        $roles = Ginq::from($this->room->getRoleConfigs())->flatMap(function(RoleConfig $roleConfig){
            return Ginq::repeat($roleConfig->getRole(), $roleConfig->getCount());
        })->toList();
        shuffle($roles);
        return $roles;
    }
}
