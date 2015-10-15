<?php

namespace Lain\OneNightWerewolfBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use Lain\OneNightWerewolfBundle\Controller\Traits\EntityGettable;
use Lain\OneNightWerewolfBundle\Entity\PlayerRole;

/**
 * @RouteResource("Player")
 */
class PlayerRoleController extends FOSRestController implements ClassResourceInterface
{
    use EntityGettable;

    public function getAction($gameId, $playerId) {
        return $this->getGame($gameId)
            ->getPlayerRoles()
            ->filter(function(PlayerRole $playerRole) use ($playerId) {
                return $playerRole->getPlayer()->getId() == $playerId;
            })
            ->first();
    }
}
