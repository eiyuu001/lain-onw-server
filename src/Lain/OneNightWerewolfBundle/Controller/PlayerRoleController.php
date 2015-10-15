<?php

namespace Lain\OneNightWerewolfBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use Lain\OneNightWerewolfBundle\Controller\Traits\EntityGettable;

/**
 * @RouteResource("Player")
 */
class PlayerRoleController extends FOSRestController implements ClassResourceInterface
{
    use EntityGettable;

    public function getAction($gameId, $playerId) {
        return $this->getPlayerRole($gameId, $playerId);
    }
}
