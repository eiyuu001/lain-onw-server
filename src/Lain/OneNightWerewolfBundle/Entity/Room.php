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
     * @JMS\Exclude
     */
    private $players;

    /**
     * @ORM\OneToMany(targetEntity="Regulation", mappedBy="room", cascade={"persist", "remove"})
     * @JMS\Exclude
     */
    private $regulations;

    /**
     * @ORM\OneToMany(targetEntity="Game", mappedBy="room", cascade={"persist", "remove"})
     * @JMS\Exclude
     */
    private $games;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->players = new \Doctrine\Common\Collections\ArrayCollection();
        $this->regulations = new \Doctrine\Common\Collections\ArrayCollection();
        $this->games = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Add regulation
     *
     * @param \Lain\OneNightWerewolfBundle\Entity\Regulation $regulation
     *
     * @return Room
     */
    public function addRegulation(\Lain\OneNightWerewolfBundle\Entity\Regulation $regulation)
    {
        $this->regulations[] = $regulation;

        return $this;
    }

    /**
     * Remove regulation
     *
     * @param \Lain\OneNightWerewolfBundle\Entity\Regulation $regulation
     */
    public function removeRegulation(\Lain\OneNightWerewolfBundle\Entity\Regulation $regulation)
    {
        $this->regulations->removeElement($regulation);
    }

    /**
     * Get regulations
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRegulations()
    {
        return $this->regulations;
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
}
