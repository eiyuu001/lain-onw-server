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

class RoomController extends FOSRestController implements ClassResourceInterface
{
    use EntityGettable;

    public function cgetAction() {
        return $this->getRooms();
    }

    public function getAction($roomId)
    {
        return $this->getRoom($roomId);
    }

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
        return $room;
    }

    public function postGameAction(Room $room) {
        $game = new Game($room);
        $game->castRoles();
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($game);
        $entityManager->flush();
        $entityManager->refresh($game);
        $view = $this->view($game, 200);
        $view->setSerializationContext(SerializationContext::create()->setGroups(['Default']));
        return $view;
    }

    public function postPlayerAction(Request $request, $roomId) {
        $content = json_decode($request->getContent(), true);
        $entityManager = $this->getDoctrine()->getManager();
        $player = new Player($this->getRoom($roomId));
        $player->setName($content['name']);
        $entityManager->persist($player);
        $entityManager->flush();
        return $player;
    }

}
