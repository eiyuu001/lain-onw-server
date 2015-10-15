<?php

namespace Lain\OneNightWerewolfBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;
use Lain\OneNightWerewolfBundle\Controller\Traits\EntityGettable;

class GameController extends FOSRestController implements ClassResourceInterface
{
    use EntityGettable;

    public function cgetAction() {
        return $this->getGames();
    }

    public function getAction($gameId) {
        return $this->getGame($gameId);
    }

}
