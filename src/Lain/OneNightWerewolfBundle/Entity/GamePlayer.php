<?php

namespace Lain\OneNightWerewolfBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ginq\Ginq;
use Lain\OneNightWerewolfBundle\Util\Roles;
use Lain\OneNightWerewolfBundle\Util\RoleGroups;
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
     * @var Player
     *
     * @ORM\ManyToOne(targetEntity="Player", inversedBy="gamePlayers")
     * @ORM\JoinColumn(name="player_id", referencedColumnName="id", nullable=FALSE)
     * @JMS\Inline()
     */
    private $player;

    /**
     * @var Role
     *
     * @ORM\ManyToOne(targetEntity="Role")
     * @ORM\JoinColumn(name="role_id", referencedColumnName="id", nullable=FALSE)
     * @JMS\Groups({"private", "finished"})
     */
    private $role;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="GamePlayer", mappedBy="voteDestination", cascade={"persist", "remove"})
     * @JMS\Exclude
     */
    private $voteSources;

    /**
     * @var GamePlayer
     *
     * @ORM\ManyToOne(targetEntity="GamePlayer", inversedBy="voteSources")
     * @ORM\JoinColumn(name="vote_destination_id", referencedColumnName="id")
     * @JMS\Exclude
     */
    private $voteDestination;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="GamePlayer", mappedBy="peepDestination", cascade={"persist", "remove"})
     * @JMS\Exclude
     */
    private $peepSources;

    /**
     * @var GamePlayer
     *
     * @ORM\ManyToOne(targetEntity="GamePlayer", inversedBy="peepSources")
     * @ORM\JoinColumn(name="peep_destination_id", referencedColumnName="id")
     * @JMS\Exclude
     */
    private $peepDestination;

    /**
     * @var ArrayCollection
     *
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
     * Constructor
     *
     * @param Game $game
     * @param Player $player
     */
    public function __construct(Game $game, Player $player)
    {
        $this->setGame($game);
        $game->addGamePlayer($this);

        $this->setPlayer($player);
        $player->addGamePlayer($this);

        $this->voteSources = new ArrayCollection();
        $this->peepSources = new ArrayCollection();
        $this->swapSources = new ArrayCollection();
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
     * Set game
     *
     * @param Game $game
     *
     * @return GamePlayer
     */
    public function setGame(Game $game)
    {
        $this->game = $game;

        return $this;
    }

    /**
     * Get game
     *
     * @return Game
     */
    public function getGame()
    {
        return $this->game;
    }

    /**
     * Set player
     *
     * @param Player $player
     *
     * @return GamePlayer
     */
    public function setPlayer(Player $player)
    {
        $this->player = $player;

        return $this;
    }

    /**
     * Get player
     *
     * @return Player
     */
    public function getPlayer()
    {
        return $this->player;
    }

    /**
     * Set role
     *
     * @param Role $role
     *
     * @return GamePlayer
     */
    public function setRole(Role $role)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * Get role
     *
     * @return Role
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
            case RoleGroups::WEREWOLF:
                return $this->getGame()->isWerewolfGroupWon();
            case RoleGroups::VILLAGER:
                return $this->getGame()->isVillagerGroupWon();
            case RoleGroups::HANGED_MAN:
                return $this->getGame()->isHangedManGroupWon();
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
        return $this->getRole()->getId() == Roles::FORTUNE_TELLER;
    }

    /**
     * @return bool
     */
    public function canSwap() {
        return $this->getRole()->getId() == Roles::PHANTOM_THIEF;
    }

    /**
     * @return bool
     */
    public function hasVoted() {
        return !empty($this->voteDestination);
    }

    /**
     * @return bool
     */
    public function hasPeeped() {
        return !empty($this->peepDestination);
    }

    /**
     * @return bool
     */
    public function hasSwapped() {
        return !empty($this->swapDestination);
    }

    public function action($actionName, GamePlayer $target) {
        $setDest = 'set' . ucfirst($actionName) . 'Destination';
        $addSrc  = 'add' . ucfirst($actionName) . 'Source';
        $this->$setDest($target);
        $target->$addSrc($this);
    }

    /**
     * Add voteSource
     *
     * @param GamePlayer $voteSource
     *
     * @return GamePlayer
     */
    public function addVoteSource(GamePlayer $voteSource)
    {
        $this->voteSources[] = $voteSource;

        return $this;
    }

    /**
     * Remove voteSource
     *
     * @param GamePlayer $voteSource
     */
    public function removeVoteSource(GamePlayer $voteSource)
    {
        $this->voteSources->removeElement($voteSource);
    }

    /**
     * Get voteSources
     *
     * @return Collection
     */
    public function getVoteSources()
    {
        return $this->voteSources;
    }

    /**
     * Set voteDestination
     *
     * @param GamePlayer $voteDestination
     *
     * @return GamePlayer
     */
    public function setVoteDestination(GamePlayer $voteDestination = null)
    {
        $this->voteDestination = $voteDestination;

        return $this;
    }

    /**
     * Get voteDestination
     *
     * @return GamePlayer
     */
    public function getVoteDestination()
    {
        return $this->voteDestination;
    }

    /**
     * Add peepSource
     *
     * @param GamePlayer $peepSource
     *
     * @return GamePlayer
     */
    public function addPeepSource(GamePlayer $peepSource)
    {
        $this->peepSources[] = $peepSource;

        return $this;
    }

    /**
     * Remove peepSource
     *
     * @param GamePlayer $peepSource
     */
    public function removePeepSource(GamePlayer $peepSource)
    {
        $this->peepSources->removeElement($peepSource);
    }

    /**
     * Get peepSources
     *
     * @return Collection
     */
    public function getPeepSources()
    {
        return $this->peepSources;
    }

    /**
     * Set peepDestination
     *
     * @param GamePlayer $peepDestination
     *
     * @return GamePlayer
     */
    public function setPeepDestination(GamePlayer $peepDestination = null)
    {
        $this->peepDestination = $peepDestination;

        return $this;
    }

    /**
     * Get peepDestination
     *
     * @return GamePlayer
     */
    public function getPeepDestination()
    {
        return $this->peepDestination;
    }

    /**
     * Add swapSource
     *
     * @param GamePlayer $swapSource
     *
     * @return GamePlayer
     */
    public function addSwapSource(GamePlayer $swapSource)
    {
        $this->swapSources[] = $swapSource;

        return $this;
    }

    /**
     * Remove swapSource
     *
     * @param GamePlayer $swapSource
     */
    public function removeSwapSource(GamePlayer $swapSource)
    {
        $this->swapSources->removeElement($swapSource);
    }

    /**
     * Get swapSources
     *
     * @return Collection
     */
    public function getSwapSources()
    {
        return $this->swapSources;
    }

    /**
     * Set swapDestination
     *
     * @param GamePlayer $swapDestination
     *
     * @return GamePlayer
     */
    public function setSwapDestination(GamePlayer $swapDestination = null)
    {
        $this->swapDestination = $swapDestination;

        return $this;
    }

    /**
     * Get swapDestination
     *
     * @return GamePlayer
     */
    public function getSwapDestination()
    {
        return $this->swapDestination;
    }

    /**
     * @JMS\VirtualProperty
     * @JMS\SerializedName("voteDestination")
     * @JMS\Groups({"finished"})
     *
     * @return array
     */
    public function getVoteDestinationsSummary() {
        return is_null($this->voteDestination) ? null : $this->voteDestination->getPlayer();
    }

    /**
     * @JMS\VirtualProperty
     * @JMS\SerializedName("peepDestination")
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
     * @JMS\SerializedName("swapDestination")
     * @JMS\Groups({"finished"})
     *
     * @return array
     */
    public function getSwapDestinationsSummary() {
        return is_null($this->swapDestination) ? null : $this->swapDestination->getPlayer();
    }
}
