<?php

namespace Lain\OneNightWerewolfBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;
use Lain\OneNightWerewolfBundle\Controller\Traits\EntityGettable;

class PlayerController extends FOSRestController implements ClassResourceInterface
{
    use EntityGettable;

    public function cgetAction() {
        return $this->getPlayers();
    }

    public function getAction($playerId) {
        return $this->getPlayer($playerId);
    }

}
