<?php

namespace Lain\OneNightWerewolfBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;
use Lain\OneNightWerewolfBundle\Entity\Game;
use Lain\OneNightWerewolfBundle\Entity\PlayerRole;
use Lain\OneNightWerewolfBundle\Entity\Room;
use Lain\OneNightWerewolfBundle\Entity\Vote;

class VoteController extends FOSRestController implements ClassResourceInterface
{
    public function putAction($roomId, $gameId, $playerId, $destinationId) {
        /** @var Room $room */
        $room = $this->getDoctrine()->getRepository('LainOneNightWerewolfBundle:Room')->find($roomId);
        /** @var Game $game */
        $game = $room
            ->getGames()
            ->filter(function(Game $game) use ($gameId) {return $game->getId() == $gameId;})
            ->first();
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
