<?php

namespace Lain\OneNightWerewolfBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use FOS\RestBundle\Controller\Annotations\Put;
use JMS\Serializer\SerializationContext;
use Lain\OneNightWerewolfBundle\Controller\Traits\EntityGettable;
use Symfony\Component\HttpFoundation\Request;

/**
 * @RouteResource("Player")
 */
class PlayerRoleController extends FOSRestController implements ClassResourceInterface
{
    use EntityGettable;

    public function getAction($gameId, $playerId) {
        $playerRole = $this->getPlayerRole($gameId, $playerId);
        $view = $this->view($playerRole, 200);
        $groups = ['Default'];
        if (true) { // todo: トークンなどにより本人認証が出来る場合のみ'secret'を付加
            array_push($groups, 'secret');
        }
        $view->setSerializationContext(
            SerializationContext::create()->setGroups($groups)
        );
        return $view;
    }

    /**
     * @Put("games/{gameId}/players/{playerId}/vote")
     */
    public function putVoteAction(Request $request, $gameId, $playerId) {
        return $this->affectOtherPlayer($request, $gameId, $playerId, 'vote');
    }

    /**
     * @Put("games/{gameId}/players/{playerId}/peep")
     */
    public function putPeepAction(Request $request, $gameId, $playerId) {
        return $this->affectOtherPlayer($request, $gameId, $playerId, 'peep', ['secret']);
    }

    /**
     * @Put("games/{gameId}/players/{playerId}/swap")
     */
    public function putSwapAction(Request $request, $gameId, $playerId) {
        return $this->affectOtherPlayer($request, $gameId, $playerId, 'swap', ['secret']);
    }

    private function affectOtherPlayer(Request $request, $gameId, $playerId, $action, $extraSerializationGroups = []) {
        $content = json_decode($request->getContent(), true);
        $setDest = 'set' . ucfirst($action) . 'Destination';
        $addSrc  = 'add' . ucfirst($action) . 'Source';
        $player = $this->getPlayerRole($gameId, $playerId);
        $target = $this->getPlayerRole($gameId, $content['target']);
        $player->$setDest($target);
        $target->$addSrc($player);

        $objectManager = $this->getDoctrine()->getManager();
        $objectManager->persist($target);
        $objectManager->flush();

        $view = $this->view($target, 200);
        $view->setSerializationContext(
            SerializationContext::create()->setGroups(array_merge(['Default'], $extraSerializationGroups))
        );
        return $view;
    }
}
