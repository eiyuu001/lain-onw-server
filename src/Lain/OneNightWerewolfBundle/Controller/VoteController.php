<?php

namespace Lain\OneNightWerewolfBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;
use Lain\OneNightWerewolfBundle\Controller\Traits\EntityGettable;
use Lain\OneNightWerewolfBundle\Entity\PlayerRole;
use Lain\OneNightWerewolfBundle\Entity\Vote;

class VoteController extends FOSRestController implements ClassResourceInterface
{
    use EntityGettable;

    public function putAction($gameId, $playerId, $destinationId) {
        $playerRole = $this->getPlayerRole($gameId, $playerId);
        $destination = $this->getPlayerRole($gameId, $destinationId);
        $vote = new Vote();
        $vote->setSource($playerRole);
        $vote->setDestination($destination);
        $objectManager = $this->getDoctrine()->getManager();
        $objectManager->persist($vote);
        $objectManager->flush();
        return $vote;
    }
}
