<?php

namespace Lain\OneNightWerewolfBundle\Entity;

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
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\OneToMany(targetEntity="Player", mappedBy="room", cascade={"persist", "remove"})
     */
    private $players;

    /**
     * @ORM\OneToMany(targetEntity="Game", mappedBy="room", cascade={"persist", "remove"})
     * @JMS\Exclude
     */
    private $games;
    
    
    /**
     * @ORM\OneToMany(targetEntity="RoleConfig", mappedBy="room", cascade={"persist", "remove"})
     */
    private $roleConfigs;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->players = new \Doctrine\Common\Collections\ArrayCollection();
        $this->games = new \Doctrine\Common\Collections\ArrayCollection();
        $this->roleConfigs = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @param \Lain\OneNightWerewolfBundle\Entity\Player $player
     *
     * @return Room
     */
    public function addPlayer(\Lain\OneNightWerewolfBundle\Entity\Player $player)
    {
        $this->players[] = $player;

        return $this;
    }

    /**
     * Remove player
     *
     * @param \Lain\OneNightWerewolfBundle\Entity\Player $player
     */
    public function removePlayer(\Lain\OneNightWerewolfBundle\Entity\Player $player)
    {
        $this->players->removeElement($player);
    }

    /**
     * Get players
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPlayers()
    {
        return $this->players;
    }

    /**
     * Add game
     *
     * @param \Lain\OneNightWerewolfBundle\Entity\Game $game
     *
     * @return Room
     */
    public function addGame(\Lain\OneNightWerewolfBundle\Entity\Game $game)
    {
        $this->games[] = $game;

        return $this;
    }

    /**
     * Remove game
     *
     * @param \Lain\OneNightWerewolfBundle\Entity\Game $game
     */
    public function removeGame(\Lain\OneNightWerewolfBundle\Entity\Game $game)
    {
        $this->games->removeElement($game);
    }

    /**
     * Get games
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getGames()
    {
        return $this->games;
    }
    
    /**
     * Add roleConfig
     *
     * @param \Lain\OneNightWerewolfBundle\Entity\RoleConfig $roleConfig
     *
     * @return Room
     */
    public function addRoleConfig(\Lain\OneNightWerewolfBundle\Entity\RoleConfig $roleConfig)
    {
        $this->roleConfigs[] = $roleConfig;

        return $this;
    }

    /**
     * Remove roleConfig
     *
     * @param \Lain\OneNightWerewolfBundle\Entity\RoleConfig $roleConfig
     */
    public function removeRoleConfig(\Lain\OneNightWerewolfBundle\Entity\RoleConfig $roleConfig)
    {
        $this->roleConfigs->removeElement($roleConfig);
    }

    /**
     * Get roleConfigs
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRoleConfigs()
    {
        return $this->roleConfigs;
    }


}
