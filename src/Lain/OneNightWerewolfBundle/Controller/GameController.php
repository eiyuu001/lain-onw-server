<?php

namespace Lain\OneNightWerewolfBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\Util\Codes;
use JMS\Serializer\SerializationContext;
use Lain\OneNightWerewolfBundle\Controller\Traits\EntityGettable;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

class GameController extends FOSRestController implements ClassResourceInterface
{
    use EntityGettable;

    /**
     * @ApiDoc(
     *  resource=true,
     *  description="Returns a collection of Game"
     * )
     */
    public function cgetAction() {
        return $this->getGames();
    }

    /**
     * @ApiDoc(
     *  resource=true,
     *  description="Returns a Game object specified by gameId",
     *  requirements={
     *      {"name"="gameId", "dataType"="integer", "requirement"="\d+", "description"="game id"}
     *  }
     * )
     */
    public function getAction($gameId) {
        $game = $this->getGame($gameId);
        return $this->view($game, Codes::HTTP_OK)
            ->setSerializationContext(SerializationContext::create()->setGroups(['Default']));
    }

    /**
     * @ApiDoc(
     *  resource=true,
     *  description="Returns results of a game specified by gameId",
     *  requirements={
     *      {"name"="gameId", "dataType"="integer", "requirement"="\d+", "description"="game id"}
     *  },
     *  statusCodes={
     *      404="Returned when the game has not been finished."
     *  }
     * )
     */
    public function getResultAction($gameId) {
        $game = $this->getGame($gameId);
        if (!$game->hasFinished()) {
            throw new ResourceNotFoundException('The game has not been finished.');
        }
        return $view = $this->view($game, Codes::HTTP_OK)
            ->setSerializationContext(SerializationContext::create()->setGroups(['Default', 'finished']));
    }
}
