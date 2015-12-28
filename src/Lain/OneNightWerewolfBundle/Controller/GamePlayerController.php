<?php

namespace Lain\OneNightWerewolfBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use FOS\RestBundle\Controller\Annotations\Put;
use JMS\Serializer\SerializationContext;
use Lain\OneNightWerewolfBundle\Controller\Traits\EntityGettable;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

/**
 * @RouteResource("Player")
 */
class GamePlayerController extends FOSRestController implements ClassResourceInterface
{
    use EntityGettable;

    /**
     * @ApiDoc(
     *  resource=true,
     *  description="Returns a Player object on a game specified by gameId and playerId",
     *  requirements={
     *      {"name"="gameId", "dataType"="integer", "requirement"="\d+", "description"="game id"},
     *      {"name"="playerId", "dataType"="integer", "requirement"="\d+", "description"="player id"}
     *  },
     * )
     */
    public function getAction($gameId, $playerId) {
        $gamePlayer = $this->getGamePlayer($gameId, $playerId);
        $view = $this->view($gamePlayer, 200);
        $groups = ['Default'];
        if (true) { // todo: トークンなどにより本人認証が出来る場合のみ'secret'を付加
            array_push($groups, 'private');
        }
        $view->setSerializationContext(
            SerializationContext::create()->setGroups($groups)
        );
        return $view;
    }

    /**
     * @Put("games/{gameId}/players/{playerId}/vote")
     *
     * @ApiDoc(
     *  description="Vote to an other player",
     *  requirements={
     *      {"name"="gameId", "dataType"="integer", "requirement"="\d+", "description"="game id"},
     *      {"name"="playerId", "dataType"="integer", "requirement"="\d+", "description"="player id"}
     *  },
     *  parameters={
     *      {"name"="target", "dataType"="integer", "requirement"="\d+", "description"="target player id"}
     *  },
     *  statusCodes={
     *      400="Returned when you try to vote to yourself."
     *  }
     * )
     */
    public function putVoteAction(Request $request, $gameId, $playerId) {
        return $this->affectOtherPlayer($request, $gameId, $playerId, 'vote');
    }

    /**
     * @Put("games/{gameId}/players/{playerId}/peep")
     *
     * @ApiDoc(
     *  description="Peep a role of an other player",
     *  requirements={
     *      {"name"="gameId", "dataType"="integer", "requirement"="\d+", "description"="game id"},
     *      {"name"="playerId", "dataType"="integer", "requirement"="\d+", "description"="player id"}
     *  },
     *  parameters={
     *      {"name"="target", "dataType"="integer", "requirement"="\d+", "description"="target player id"}
     *  },
     *  statusCodes={
     *      400="Returned when you try to peep yourself.",
     *      403="Returned when you don't have ability to peep."
     *  }
     * )
     */
    public function putPeepAction(Request $request, $gameId, $playerId) {
        return $this->affectOtherPlayer($request, $gameId, $playerId, 'peep', ['private']);
    }

    /**
     * @Put("games/{gameId}/players/{playerId}/swap")
     *
     * @ApiDoc(
     *  description="Swap roles with an other player",
     *  requirements={
     *      {"name"="gameId", "dataType"="integer", "requirement"="\d+", "description"="game id"},
     *      {"name"="playerId", "dataType"="integer", "requirement"="\d+", "description"="player id"}
     *  },
     *  parameters={
     *      {"name"="target", "dataType"="integer", "requirement"="\d+", "description"="target player id"}
     *  },
     *  statusCodes={
     *      400="Returned when you try to swap roles with yourself.",
     *      403="Returned when you don't have ability to swap."
     *  }
     * )
     */
    public function putSwapAction(Request $request, $gameId, $playerId) {
        return $this->affectOtherPlayer($request, $gameId, $playerId, 'swap', ['private']);
    }

    private function affectOtherPlayer(Request $request, $gameId, $playerId, $action, $extraSerializationGroups = []) {
        $canAction = 'can' . ucfirst($action);
        if (!$this->getGamePlayer($gameId, $playerId)->$canAction()) {
            throw new AccessDeniedHttpException("You don't have ability to $action");
        }

        $content = json_decode($request->getContent(), true);
        if ($playerId == $content['target']) {
            throw new BadRequestHttpException("You can't $action to yourself.");
        }

        $setDest = 'set' . ucfirst($action) . 'Destination';
        $addSrc  = 'add' . ucfirst($action) . 'Source';
        $player = $this->getGamePlayer($gameId, $playerId);
        $target = $this->getGamePlayer($gameId, $content['target']);
        $player->$setDest($target);
        $target->$addSrc($player);

        $objectManager = $this->getDoctrine()->getManager();
        $objectManager->persist($target);
        $objectManager->flush();

        $view = $this->view($target, 201);
        $view->setSerializationContext(
            SerializationContext::create()->setGroups(array_merge(['Default'], $extraSerializationGroups))
        );
        return $view;
    }
}
