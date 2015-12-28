<?php

namespace Lain\OneNightWerewolfBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as JMS;

/**
 * Room
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Room
{
    /**
     * @var integer
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Player", mappedBy="room", cascade={"persist", "remove"})
     */
    private $players;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Game", mappedBy="room", cascade={"persist", "remove"})
     * @JMS\Exclude
     */
    private $games;
    
    
    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="RoleConfig", mappedBy="room", cascade={"persist", "remove"})
     * @JMS\Groups({"Default", "postRoom"})
     * @JMS\SerializedName("roleConfigs")
     */
    private $roleConfigs;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->players = new ArrayCollection();
        $this->games = new ArrayCollection();
        $this->roleConfigs = new ArrayCollection();
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
     * Add player
     *
     * @param Player $player
     *
     * @return Room
     */
    public function addPlayer(Player $player)
    {
        $this->players[] = $player;

        return $this;
    }

    /**
     * Remove player
     *
     * @param Player $player
     */
    public function removePlayer(Player $player)
    {
        $this->players->removeElement($player);
    }

    /**
     * Get players
     *
     * @return Collection
     */
    public function getPlayers()
    {
        return $this->players;
    }

    /**
     * Add game
     *
     * @param Game $game
     *
     * @return Room
     */
    public function addGame(Game $game)
    {
        $this->games[] = $game;

        return $this;
    }

    /**
     * Remove game
     *
     * @param Game $game
     */
    public function removeGame(Game $game)
    {
        $this->games->removeElement($game);
    }

    /**
     * Get games
     *
     * @return Collection
     */
    public function getGames()
    {
        return $this->games;
    }
    
    /**
     * Add roleConfig
     *
     * @param RoleConfig $roleConfig
     *
     * @return Room
     */
    public function addRoleConfig(RoleConfig $roleConfig)
    {
        $this->roleConfigs[] = $roleConfig;

        return $this;
    }

    /**
     * Remove roleConfig
     *
     * @param RoleConfig $roleConfig
     */
    public function removeRoleConfig(RoleConfig $roleConfig)
    {
        $this->roleConfigs->removeElement($roleConfig);
    }

    /**
     * Get roleConfigs
     *
     * @return Collection
     */
    public function getRoleConfigs()
    {
        return $this->roleConfigs;
    }


}
