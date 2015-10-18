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
     * @ORM\OneToOne(targetEntity="Vote", cascade={"persist", "remove"})
     * @JMS\Groups({"secret"})
     */
    private $vote;
    

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
     * Set vote
     *
     * @param \Lain\OneNightWerewolfBundle\Entity\Vote $vote
     *
     * @return PlayerRole
     */
    public function setVote(\Lain\OneNightWerewolfBundle\Entity\Vote $vote = null)
    {
        $this->vote = $vote;

        return $this;
    }

    /**
     * Get vote
     *
     * @return \Lain\OneNightWerewolfBundle\Entity\Vote
     */
    public function getVote()
    {
        return $this->vote;
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
        $players = $this->game->getPlayerRoles();

        $destinations = Ginq::from($players)->map(function(PlayerRole $player) {
            return $player->getVote()->getDestination()->getPlayer()->getId();
        });

        $myObtainedCount = $destinations->filter(function($destination) {
            return $destination == $this->getPlayer()->getId();
        })->count();

        $maxObtainedCount = $destinations->groupBy(function($destination) {
            return $destination;
        })->map(function(GroupingGinq $g) {
            return $g->count();
        })->max();

        return $myObtainedCount < $maxObtainedCount;
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

}
