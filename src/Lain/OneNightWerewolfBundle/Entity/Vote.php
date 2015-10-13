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
     * @ORM\OneToOne(targetEntity="PlayerRole", inversedBy="vote")
     * @ORM\JoinColumn(name="src_player_role_id", referencedColumnName="id", nullable=FALSE)
     */
    private $source;

    /**
     * @ORM\ManyToOne(targetEntity="PlayerRole")
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
     * Set source
     *
     * @param \Lain\OneNightWerewolfBundle\Entity\PlayerRole $source
     *
     * @return Vote
     */
    public function setSource(\Lain\OneNightWerewolfBundle\Entity\PlayerRole $source)
    {
        $this->source = $source;

        return $this;
    }

    /**
     * Get source
     *
     * @return \Lain\OneNightWerewolfBundle\Entity\PlayerRole
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * Set destination
     *
     * @param \Lain\OneNightWerewolfBundle\Entity\PlayerRole $destination
     *
     * @return Vote
     */
    public function setDestination(\Lain\OneNightWerewolfBundle\Entity\PlayerRole $destination)
    {
        $this->destination = $destination;

        return $this;
    }

    /**
     * Get destination
     *
     * @return \Lain\OneNightWerewolfBundle\Entity\PlayerRole
     */
    public function getDestination()
    {
        return $this->destination;
    }
}
