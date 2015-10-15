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
        $game = $this->getGame($gameId);
        /** @var PlayerRole $playerRole */
        $playerRole = $game
            ->getPlayerRoles()
            ->filter(function(PlayerRole $playerRole) use ($playerId) {return $playerRole->getPlayer()->getId() == $playerId;})
            ->first();
        /** @var PlayerRole $destination */
        $destination = $game
            ->getPlayerRoles()
            ->filter(function(PlayerRole $playerRole) use ($destinationId) {return $playerRole->getPlayer()->getId() == $destinationId;})
            ->first();
        $vote = new Vote();
        $vote->setSource($playerRole);
        $vote->setDestination($destination);
        $objectManager = $this->getDoctrine()->getManager();
        $objectManager->persist($vote);
        $objectManager->flush();
        return $vote;
    }
}
