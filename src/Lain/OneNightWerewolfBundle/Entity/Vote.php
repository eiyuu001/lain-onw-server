<?php

namespace Lain\OneNightWerewolfBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as JMS;

/**
 * Vote
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Vote
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
     * @ORM\ManyToOne(targetEntity="GamePlayer")
     * @ORM\JoinColumn(name="dst_player_role_id", referencedColumnName="id", nullable=FALSE)
     */
    private $destination;
    

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
     * Set destination
     *
     * @param \Lain\OneNightWerewolfBundle\Entity\GamePlayer $destination
     *
     * @return Vote
     */
    public function setDestination(\Lain\OneNightWerewolfBundle\Entity\GamePlayer $destination)
    {
        $this->destination = $destination;

        return $this;
    }

    /**
     * Get destination
     *
     * @return \Lain\OneNightWerewolfBundle\Entity\GamePlayer
     */
    public function getDestination()
    {
        return $this->destination;
    }
}
