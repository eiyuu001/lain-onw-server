<?php

namespace Lain\OneNightWerewolfBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ginq\Ginq;
use Ginq\GroupingGinq;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as JMS;

/**
 * PlayerRole
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class PlayerRole
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
     * @var Game
     *
     * @ORM\ManyToOne(targetEntity="Game", inversedBy="playerRoles")
     * @ORM\JoinColumn(name="game_id", referencedColumnName="id", nullable=FALSE)
     * @JMS\Exclude
     */
    private $game;

    /**
     * @ORM\ManyToOne(targetEntity="Player")
     * @ORM\JoinColumn(name="player_id", referencedColumnName="id", nullable=FALSE)
     */
    private $player;

    /**
     * @ORM\ManyToOne(targetEntity="Role")
     * @ORM\JoinColumn(name="role_id", referencedColumnName="id", nullable=FALSE)
     * @JMS\Groups({"secret"})
     */
    private $role;

    /**
     * @ORM\OneToMany(targetEntity="PlayerRole", mappedBy="voteTo", cascade={"persist", "remove"})
     * @JMS\Exclude
     */
    private $votesFrom;

    /**
     * @ORM\ManyToOne(targetEntity="PlayerRole", inversedBy="votesFrom")
     * @ORM\JoinColumn(name="vote_player_id", referencedColumnName="id")
     * @JMS\Groups({"secret"})
     */
    private $voteTo;


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
     * Set game
     *
     * @param \Lain\OneNightWerewolfBundle\Entity\Game $game
     *
     * @return PlayerRole
     */
    public function setGame(\Lain\OneNightWerewolfBundle\Entity\Game $game)
    {
        $this->game = $game;

        return $this;
    }

    /**
     * Get game
     *
     * @return \Lain\OneNightWerewolfBundle\Entity\Game
     */
    public function getGame()
    {
        return $this->game;
    }

    /**
     * Set player
     *
     * @param \Lain\OneNightWerewolfBundle\Entity\Player $player
     *
     * @return PlayerRole
     */
    public function setPlayer(\Lain\OneNightWerewolfBundle\Entity\Player $player)
    {
        $this->player = $player;

        return $this;
    }

    /**
     * Get player
     *
     * @return \Lain\OneNightWerewolfBundle\Entity\Player
     */
    public function getPlayer()
    {
        return $this->player;
    }

    /**
     * Set role
     *
     * @param \Lain\OneNightWerewolfBundle\Entity\Role $role
     *
     * @return PlayerRole
     */
    public function setRole(\Lain\OneNightWerewolfBundle\Entity\Role $role)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * Get role
     *
     * @return \Lain\OneNightWerewolfBundle\Entity\Role
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * @JMS\VirtualProperty
     * @JMS\SerializedName("alive")
     * @JMS\Groups({"secret"})
     *
     * @return bool | null
     */
    public function isAlive()
    {
        if (!$this->game->hasFinished()) {
            return true;
        }
        $myVotedCount = $this->votesFrom->count();
        $players = $this->game->getPlayerRoles();
        $maxVotedCount = Ginq::from($players)->map(function(PlayerRole $player) {
            return $player->getVotesFrom()->count();
        })->max();

        return $myVotedCount < $maxVotedCount;
    }

    /**
     * @JMS\VirtualProperty
     * @JMS\SerializedName("won")
     * @JMS\Groups({"secret"})
     *
     * @return bool | null
     */
    public function isWon()
    {
        if (!$this->game->hasFinished()) {
            return null;
        }
        switch ($this->getRole()->getRoleGroup()->getId()) {
            case 1:
                return $this->getGame()->isWerewolfWon();
            case 2:
                return $this->getGame()->isPeopleWon();
            case 3:
                return $this->getGame()->isHangedManWon();
            default:
                return null;
        }
    }

    /**
     * @JMS\VirtualProperty
     * @JMS\SerializedName("reward")
     * @JMS\Groups({"secret"})
     *
     * @return int | null
     */
    public function computeReward()
    {
        if (!$this->game->hasFinished()) {
            return null;
        }
        if (!$this->isWon()) {
            return 0;
        }
        /** @var RoleCount $roleCount */
        $roleCount = $this->game->getRegulation()->getRoleCounts()->filter(function(RoleCount $roleCount){
            return $roleCount->getRole()->getId() == $this->getRole()->getId();
        })->first();
        $reward = $roleCount->getRewardAmount();
        if (!$this->isAlive()) {
            $reward -= $roleCount->getDeathDecrease();
        }
        return $reward;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->votesFrom = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add votesFrom
     *
     * @param \Lain\OneNightWerewolfBundle\Entity\PlayerRole $votesFrom
     *
     * @return PlayerRole
     */
    public function addVotesFrom(\Lain\OneNightWerewolfBundle\Entity\PlayerRole $votesFrom)
    {
        $this->votesFrom[] = $votesFrom;

        return $this;
    }

    /**
     * Remove votesFrom
     *
     * @param \Lain\OneNightWerewolfBundle\Entity\PlayerRole $votesFrom
     */
    public function removeVotesFrom(\Lain\OneNightWerewolfBundle\Entity\PlayerRole $votesFrom)
    {
        $this->votesFrom->removeElement($votesFrom);
    }

    /**
     * Get votesFrom
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getVotesFrom()
    {
        return $this->votesFrom;
    }

    /**
     * Set voteTo
     *
     * @param \Lain\OneNightWerewolfBundle\Entity\PlayerRole $voteTo
     *
     * @return PlayerRole
     */
    public function setVoteTo(\Lain\OneNightWerewolfBundle\Entity\PlayerRole $voteTo = null)
    {
        $this->voteTo = $voteTo;

        return $this;
    }

    /**
     * Get voteTo
     *
     * @return \Lain\OneNightWerewolfBundle\Entity\PlayerRole
     */
    public function getVoteTo()
    {
        return $this->voteTo;
    }
}
