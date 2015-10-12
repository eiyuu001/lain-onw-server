<?php

namespace Lain\OneNightWerewolfBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use Lain\OneNightWerewolfBundle\Entity\Game;
use Lain\OneNightWerewolfBundle\Entity\PlayerRole;
use Lain\OneNightWerewolfBundle\Entity\Room;

/**
 * @RouteResource("Player")
 */
class PlayerRoleController extends FOSRestController implements ClassResourceInterface
{
    public function getAction($roomId, $gameId, $playerId) {
        /** @var Room $room */
        $room = $this->getDoctrine()->getRepository('LainOneNightWerewolfBundle:Room')->find($roomId);
        /** @var Game $game */
        $game = $room
            ->getGames()
            ->filter(function(Game $game) use ($gameId) {return $game->getId() == $gameId;})
            ->first();
        $playerRole = $game
            ->getPlayerRoles()
            ->filter(function(PlayerRole $playerRole) use ($playerId) {return $playerRole->getPlayer()->getId() == $playerId;})
            ->first();
        return $playerRole;
    }
}
