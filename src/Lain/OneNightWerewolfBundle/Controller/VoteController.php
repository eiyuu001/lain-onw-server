<?php

namespace Lain\OneNightWerewolfBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\Controller\Annotations\Put;
use Lain\OneNightWerewolfBundle\Controller\Traits\EntityGettable;
use Lain\OneNightWerewolfBundle\Entity\Vote;
use Symfony\Component\HttpFoundation\Request;

class VoteController extends FOSRestController implements ClassResourceInterface
{
    use EntityGettable;

    /**
     * @Put("games/{gameId}/players/{playerId}/vote")
     */
    public function putAction(Request $request, $gameId, $playerId) {
        $content = json_decode($request->getContent(), true);
        $destination = $this->getPlayerRole($gameId, $content['destinationId']);
        $vote = new Vote();
        $vote->setDestination($destination);
        $playerRole = $this->getPlayerRole($gameId, $playerId);
        $playerRole->setVote($vote);
        $objectManager = $this->getDoctrine()->getManager();
        $objectManager->persist($playerRole);
        $objectManager->flush();
        return $vote;
    }
}
