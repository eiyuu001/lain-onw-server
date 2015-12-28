<?php

namespace Lain\OneNightWerewolfBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;
use Lain\OneNightWerewolfBundle\Controller\Traits\EntityGettable;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

class PlayerController extends FOSRestController implements ClassResourceInterface
{
    use EntityGettable;

    /**
     * @ApiDoc(
     *  resource=true,
     *  description="Returns a Player object specified by playerId",
     *  requirements={
     *      {"name"="playerId", "dataType"="integer", "requirement"="\d+", "description"="player id"}
     *  }
     * )
     */
    public function getAction($playerId) {
        return $this->getPlayer($playerId);
    }

}
