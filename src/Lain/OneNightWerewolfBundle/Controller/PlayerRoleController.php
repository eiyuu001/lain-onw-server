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
        $content = json_decode($request->getContent(), true);
        $voteFrom = $this->getPlayerRole($gameId, $playerId);
        $voteTo = $this->getPlayerRole($gameId, $content['destinationId']);
        $voteFrom->setVoteTo($voteTo);
        $voteTo->addVotesFrom($voteFrom);

        $objectManager = $this->getDoctrine()->getManager();
        $objectManager->persist($voteTo);
        $objectManager->flush();

        return $voteTo;
    }
}
