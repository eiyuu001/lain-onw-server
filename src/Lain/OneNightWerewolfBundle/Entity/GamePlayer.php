<?php

namespace Lain\OneNightWerewolfBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ginq\Ginq;
use Ginq\GroupingGinq;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as JMS;

/**
 * GamePlayer
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class GamePlayer
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
     * @ORM\ManyToOne(targetEntity="Game", inversedBy="gamePlayers")
     * @ORM\JoinColumn(name="game_id", referencedColumnName="id", nullable=FALSE)
     * @JMS\Exclude
     */
    private $game;

    /**
     * @ORM\ManyToOne(targetEntity="Player", inversedBy="gamePlayers")
     * @ORM\JoinColumn(name="player_id", referencedColumnName="id", nullable=FALSE)
     */
    private $player;

    /**
     * @ORM\ManyToOne(targetEntity="Role")
     * @ORM\JoinColumn(name="role_id", referencedColumnName="id", nullable=FALSE)
     * @JMS\Groups({"private", "finished"})
     */
    private $role;

    /**
     * @ORM\OneToMany(targetEntity="GamePlayer", mappedBy="voteDestination", cascade={"persist", "remove"})
     * @JMS\Exclude
     */
    private $voteSources;

    /**
     * @ORM\ManyToOne(targetEntity="GamePlayer", inversedBy="voteSources")
     * @ORM\JoinColumn(name="vote_destination_id", referencedColumnName="id")
     * @JMS\Exclude
     */
    private $voteDestination;

    /**
     * @ORM\OneToMany(targetEntity="GamePlayer", mappedBy="peepDestination", cascade={"persist", "remove"})
     * @JMS\Exclude
     */
    private $peepSources;

    /**
     * @ORM\ManyToOne(targetEntity="GamePlayer", inversedBy="peepSources")
     * @ORM\JoinColumn(name="peep_destination_id", referencedColumnName="id")
     * @JMS\Exclude
     */
    private $peepDestination;

    /**
     * @ORM\OneToMany(targetEntity="GamePlayer", mappedBy="swapDestination", cascade={"persist", "remove"})
     * @JMS\Exclude
     */
    private $swapSources;

    /**
     * @var GamePlayer
     *
     * @ORM\ManyToOne(targetEntity="GamePlayer", inversedBy="swapSources")
     * @ORM\JoinColumn(name="swap_destination_id", referencedColumnName="id")
     * @JMS\Exclude
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
     * @return GamePlayer
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
     * @return GamePlayer
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
     * @return GamePlayer
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
     * @JMS\Groups({"finished"})
     *
     * @return bool | null
     */
    public function isAlive()
    {
        if (!$this->game->hasFinished()) {
            return true;
        }
        $myVotedCount = $this->voteSources->count();
        $players = $this->game->getGamePlayers();
        $maxVotedCount = Ginq::from($players)->map(function(GamePlayer $player) {
            return $player->getVoteSources()->count();
        })->max();

        return $myVotedCount < $maxVotedCount;
    }

    /**
     * @JMS\VirtualProperty
     * @JMS\SerializedName("won")
     * @JMS\Groups({"finished"})
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
     * @JMS\Groups({"finished"})
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
        /** @var RoleConfig $roleConfig */
        $roleConfig = $this->game->getRoom()->getRoleConfigs()->filter(function(RoleConfig $roleConfig){
            return $roleConfig->getRole()->getId() == $this->getActualRole()->getId();
        })->first();
        return $this->isAlive() ? $roleConfig->getRewardForSurvivor() : $roleConfig->getRewardForDead();
    }

    /**
     * @return bool
     */
    public function canVote() {
        return true;
    }

    /**
     * @return bool
     */
    public function canPeep() {
        return $this->getRole()->getId() === 4; // �肢�t
    }

    /**
     * @return bool
     */
    public function canSwap() {
        return $this->getRole()->getId() === 5; // ����
    }

    /**
     * Constructor
     *
     * @param Game $game
     */
    public function __construct($game)
    {
        $this->setGame($game);
        $this->voteSources = new \Doctrine\Common\Collections\ArrayCollection();
        $this->peepSources = new \Doctrine\Common\Collections\ArrayCollection();
        $this->swapSources = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add voteSource
     *
     * @param \Lain\OneNightWerewolfBundle\Entity\GamePlayer $voteSource
     *
     * @return GamePlayer
     */
    public function addVoteSource(\Lain\OneNightWerewolfBundle\Entity\GamePlayer $voteSource)
    {
        $this->voteSources[] = $voteSource;

        return $this;
    }

    /**
     * Remove voteSource
     *
     * @param \Lain\OneNightWerewolfBundle\Entity\GamePlayer $voteSource
     */
    public function removeVoteSource(\Lain\OneNightWerewolfBundle\Entity\GamePlayer $voteSource)
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
     * @param \Lain\OneNightWerewolfBundle\Entity\GamePlayer $voteDestination
     *
     * @return GamePlayer
     */
    public function setVoteDestination(\Lain\OneNightWerewolfBundle\Entity\GamePlayer $voteDestination = null)
    {
        $this->voteDestination = $voteDestination;

        return $this;
    }

    /**
     * Get voteDestination
     *
     * @return \Lain\OneNightWerewolfBundle\Entity\GamePlayer
     */
    public function getVoteDestination()
    {
        return $this->voteDestination;
    }

    /**
     * Add peepSource
     *
     * @param \Lain\OneNightWerewolfBundle\Entity\GamePlayer $peepSource
     *
     * @return GamePlayer
     */
    public function addPeepSource(\Lain\OneNightWerewolfBundle\Entity\GamePlayer $peepSource)
    {
        $this->peepSources[] = $peepSource;

        return $this;
    }

    /**
     * Remove peepSource
     *
     * @param \Lain\OneNightWerewolfBundle\Entity\GamePlayer $peepSource
     */
    public function removePeepSource(\Lain\OneNightWerewolfBundle\Entity\GamePlayer $peepSource)
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
     * @param \Lain\OneNightWerewolfBundle\Entity\GamePlayer $peepDestination
     *
     * @return GamePlayer
     */
    public function setPeepDestination(\Lain\OneNightWerewolfBundle\Entity\GamePlayer $peepDestination = null)
    {
        $this->peepDestination = $peepDestination;

        return $this;
    }

    /**
     * Get peepDestination
     *
     * @return \Lain\OneNightWerewolfBundle\Entity\GamePlayer
     */
    public function getPeepDestination()
    {
        return $this->peepDestination;
    }

    /**
     * Add swapSource
     *
     * @param \Lain\OneNightWerewolfBundle\Entity\GamePlayer $swapSource
     *
     * @return GamePlayer
     */
    public function addSwapSource(\Lain\OneNightWerewolfBundle\Entity\GamePlayer $swapSource)
    {
        $this->swapSources[] = $swapSource;

        return $this;
    }

    /**
     * Remove swapSource
     *
     * @param \Lain\OneNightWerewolfBundle\Entity\GamePlayer $swapSource
     */
    public function removeSwapSource(\Lain\OneNightWerewolfBundle\Entity\GamePlayer $swapSource)
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
     * @param \Lain\OneNightWerewolfBundle\Entity\GamePlayer $swapDestination
     *
     * @return GamePlayer
     */
    public function setSwapDestination(\Lain\OneNightWerewolfBundle\Entity\GamePlayer $swapDestination = null)
    {
        $this->swapDestination = $swapDestination;

        return $this;
    }

    /**
     * Get swapDestination
     *
     * @return \Lain\OneNightWerewolfBundle\Entity\GamePlayer
     */
    public function getSwapDestination()
    {
        return $this->swapDestination;
    }

    /**
     * @JMS\VirtualProperty
     * @JMS\SerializedName("vote_destination")
     * @JMS\Groups({"finished"})
     *
     * @return array
     */
    public function getVoteDestinationsSummary() {
        return is_null($this->voteDestination) ? null : $this->voteDestination->getPlayer();
    }

    /**
     * @JMS\VirtualProperty
     * @JMS\SerializedName("peep_destination")
     * @JMS\Groups({"finished"})
     *
     * @return array
     */
    public function getPeepDestinationsSummary() {
        file_put_contents('/vagrant/log', gettype($this->peepDestination), FILE_APPEND);
        return is_null($this->peepDestination) ? null : $this->peepDestination->getPlayer();
    }

    /**
     * @JMS\VirtualProperty
     * @JMS\SerializedName("swap_destination")
     * @JMS\Groups({"finished"})
     *
     * @return array
     */
    public function getSwapDestinationsSummary() {
        return is_null($this->swapDestination) ? null : $this->swapDestination->getPlayer();
    }
}
