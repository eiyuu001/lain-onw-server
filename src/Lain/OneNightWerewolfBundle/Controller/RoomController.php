<?php

namespace Lain\OneNightWerewolfBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;
use Ginq\Ginq;
use Lain\OneNightWerewolfBundle\Controller\Traits\EntityGettable;
use Lain\OneNightWerewolfBundle\Entity\Game;
use Lain\OneNightWerewolfBundle\Entity\Player;
use Lain\OneNightWerewolfBundle\Entity\GamePlayer;
use Lain\OneNightWerewolfBundle\Entity\Role;
use Lain\OneNightWerewolfBundle\Entity\RoleConfig;
use Lain\OneNightWerewolfBundle\Entity\Room;
use JMS\Serializer\SerializationContext;
use Symfony\Component\HttpFoundation\Request;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

class RoomController extends FOSRestController implements ClassResourceInterface
{
    use EntityGettable;

    /**
     * @ApiDoc(
     *  resource=true,
     *  description="Returns a collection of Room"
     * )
     */
    public function cgetAction() {
        return $this->getRooms();
    }

    /**
     * @ApiDoc(
     *  resource=true,
     *  description="Returns a Room object specified by roomId",
     *  requirements={
     *      {"name"="roomId", "dataType"="integer", "requirement"="\d+", "description"="room id"}
     *  }
     * )
     */
    public function getAction($roomId)
    {
        return $this->getRoom($roomId);
    }

    /**
     * @ApiDoc(
     *  description="Create a new room",
     *  input={
     *    "class"="Lain\OneNightWerewolfBundle\Entity\Room",
     *    "groups"={"postRoom"}
     *  }
     * )
     */
    public function postAction(Request $request)
    {
        $content = json_decode($request->getContent(), true);
        $entityManager = $this->getDoctrine()->getManager();
        $room = new Room();
        foreach($content['roleConfigs'] as $roleConfigSrc) {
            $roleConfig = new RoleConfig($room);
            $role = $this->getRole($roleConfigSrc['id']);
            $roleConfig->setRole($role);
            $roleConfig->setCount($roleConfigSrc['count']);
            $roleConfig->setRewardForSurvivor($roleConfigSrc['rewardForSurvivor']);
            $roleConfig->setRewardForDead($roleConfigSrc['rewardForDead']);
            $entityManager->persist($roleConfig);
        }
        $entityManager->persist($room);
        $entityManager->flush();
        $entityManager->refresh($room);
        $view = $this->view($room, 201, [
            'Location' => $this->generateUrl('get_room', ['roomId' => $room->getId()])
        ]);
        return $view;
    }

    /**
     * @ApiDoc(
     *  description="Create a new game",
     *  requirements={
     *      {"name"="roomId", "dataType"="integer", "requirement"="\d+", "description"="room id"}
     *  }
     * )
     */
    public function postGameAction($roomId) {
        $game = new Game($this->getRoom($roomId));
        $game->castRoles();
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($game);
        $entityManager->flush();
        $entityManager->refresh($game);
        $view = $this->view($game, 201, [
            'Location' => $this->generateUrl('get_game', ['gameId' => $game->getId()])
        ]);
        $view->setSerializationContext(SerializationContext::create()->setGroups(['Default']));
        return $view;
    }

    /**
     * @ApiDoc(
     *  description="Create a new player",
     *  requirements={
     *      {"name"="roomId", "dataType"="integer", "requirement"="\d+", "description"="room id"}
     *  },
     *  input={
     *    "class"="Lain\OneNightWerewolfBundle\Entity\Player",
     *    "groups"={"postPlayer"}
     *  }
     * )
     */
    public function postPlayerAction(Request $request, $roomId) {
        $content = json_decode($request->getContent(), true);
        $entityManager = $this->getDoctrine()->getManager();
        $player = new Player($this->getRoom($roomId));
        $player->setName($content['name']);
        $entityManager->persist($player);
        $entityManager->flush();
        $view = $this->view($player, 201, [
            'Location' => $this->generateUrl('get_player', ['playerId' => $player->getId()])
        ]);
        return $view;
    }

}
