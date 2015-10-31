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
     * @ORM\OneToMany(targetEntity="PlayerRole", mappedBy="voteDestination", cascade={"persist", "remove"})
     * @JMS\Exclude
     */
    private $voteSources;

    /**
     * @ORM\ManyToOne(targetEntity="PlayerRole", inversedBy="voteSources")
     * @ORM\JoinColumn(name="vote_destination_id", referencedColumnName="id")
     * @JMS\Groups({"secret"})
     */
    private $voteDestination;

    /**
     * @ORM\OneToMany(targetEntity="PlayerRole", mappedBy="peepDestination", cascade={"persist", "remove"})
     * @JMS\Exclude
     */
    private $peepSources;

    /**
     * @ORM\ManyToOne(targetEntity="PlayerRole", inversedBy="peepSources")
     * @ORM\JoinColumn(name="peep_destination_id", referencedColumnName="id")
     * @JMS\Groups({"secret"})
     */
    private $peepDestination;

    /**
     * @ORM\OneToMany(targetEntity="PlayerRole", mappedBy="swapDestination", cascade={"persist", "remove"})
     * @JMS\Exclude
     */
    private $swapSources;

    /**
     * @var PlayerRole
     *
     * @ORM\ManyToOne(targetEntity="PlayerRole", inversedBy="swapSources")
     * @ORM\JoinColumn(name="swap_destination_id", referencedColumnName="id")
     * @JMS\Groups({"secret"})
     */
    private $swapDestination;

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
     * @JMS\SerializedName("actualRole")
     * @JMS\Groups({"finished"})
     *
     * @return Role | null
     */
    public function getActualRole()
    {
        if (!$this->game->hasFinished()) {
            return null;
        }
        if ($this->swapDestination !== null) {
            return $this->swapDestination->getRole();
        }
        if (!$this->swapSources->isEmpty()) {
            return $this->swapSources->first()->getRole();
        }
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
        switch ($this->getActualRole()->getRoleGroup()->getId()) {
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
        $this->voteSources = new \Doctrine\Common\Collections\ArrayCollection();
        $this->peepSources = new \Doctrine\Common\Collections\ArrayCollection();
        $this->swapSources = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add voteSource
     *
     * @param \Lain\OneNightWerewolfBundle\Entity\PlayerRole $voteSource
     *
     * @return PlayerRole
     */
    public function addVoteSource(\Lain\OneNightWerewolfBundle\Entity\PlayerRole $voteSource)
    {
        $this->voteSources[] = $voteSource;

        return $this;
    }

    /**
     * Remove voteSource
     *
     * @param \Lain\OneNightWerewolfBundle\Entity\PlayerRole $voteSource
     */
    public function removeVoteSource(\Lain\OneNightWerewolfBundle\Entity\PlayerRole $voteSource)
    {
        $this->voteSources->removeElement($voteSource);
    }

    /**
     * Get voteSources
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getVoteSources()
    {
        return $this->voteSources;
    }

    /**
     * Set voteDestination
     *
     * @param \Lain\OneNightWerewolfBundle\Entity\PlayerRole $voteDestination
     *
     * @return PlayerRole
     */
    public function setVoteDestination(\Lain\OneNightWerewolfBundle\Entity\PlayerRole $voteDestination = null)
    {
        $this->voteDestination = $voteDestination;

        return $this;
    }

    /**
     * Get voteDestination
     *
     * @return \Lain\OneNightWerewolfBundle\Entity\PlayerRole
     */
    public function getVoteDestination()
    {
        return $this->voteDestination;
    }

    /**
     * Add peepSource
     *
     * @param \Lain\OneNightWerewolfBundle\Entity\PlayerRole $peepSource
     *
     * @return PlayerRole
     */
    public function addPeepSource(\Lain\OneNightWerewolfBundle\Entity\PlayerRole $peepSource)
    {
        $this->peepSources[] = $peepSource;

        return $this;
    }

    /**
     * Remove peepSource
     *
     * @param \Lain\OneNightWerewolfBundle\Entity\PlayerRole $peepSource
     */
    public function removePeepSource(\Lain\OneNightWerewolfBundle\Entity\PlayerRole $peepSource)
    {
        $this->peepSources->removeElement($peepSource);
    }

    /**
     * Get peepSources
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPeepSources()
    {
        return $this->peepSources;
    }

    /**
     * Set peepDestination
     *
     * @param \Lain\OneNightWerewolfBundle\Entity\PlayerRole $peepDestination
     *
     * @return PlayerRole
     */
    public function setPeepDestination(\Lain\OneNightWerewolfBundle\Entity\PlayerRole $peepDestination = null)
    {
        $this->peepDestination = $peepDestination;

        return $this;
    }

    /**
     * Get peepDestination
     *
     * @return \Lain\OneNightWerewolfBundle\Entity\PlayerRole
     */
    public function getPeepDestination()
    {
        return $this->peepDestination;
    }

    /**
     * Add swapSource
     *
     * @param \Lain\OneNightWerewolfBundle\Entity\PlayerRole $swapSource
     *
     * @return PlayerRole
     */
    public function addSwapSource(\Lain\OneNightWerewolfBundle\Entity\PlayerRole $swapSource)
    {
        $this->swapSources[] = $swapSource;

        return $this;
    }

    /**
     * Remove swapSource
     *
     * @param \Lain\OneNightWerewolfBundle\Entity\PlayerRole $swapSource
     */
    public function removeSwapSource(\Lain\OneNightWerewolfBundle\Entity\PlayerRole $swapSource)
    {
        $this->swapSources->removeElement($swapSource);
    }

    /**
     * Get swapSources
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSwapSources()
    {
        return $this->swapSources;
    }

    /**
     * Set swapDestination
     *
     * @param \Lain\OneNightWerewolfBundle\Entity\PlayerRole $swapDestination
     *
     * @return PlayerRole
     */
    public function setSwapDestination(\Lain\OneNightWerewolfBundle\Entity\PlayerRole $swapDestination = null)
    {
        $this->swapDestination = $swapDestination;

        return $this;
    }

    /**
     * Get swapDestination
     *
     * @return \Lain\OneNightWerewolfBundle\Entity\PlayerRole
     */
    public function getSwapDestination()
    {
        return $this->swapDestination;
    }
}
