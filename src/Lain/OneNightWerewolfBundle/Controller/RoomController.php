<?php

namespace Lain\OneNightWerewolfBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;
use Ginq\Ginq;
use Lain\OneNightWerewolfBundle\Controller\Traits\EntityGettable;
use Lain\OneNightWerewolfBundle\Entity\Game;
use Lain\OneNightWerewolfBundle\Entity\Player;
use Lain\OneNightWerewolfBundle\Entity\PlayerRole;
use Lain\OneNightWerewolfBundle\Entity\Regulation;
use Lain\OneNightWerewolfBundle\Entity\Role;
use Lain\OneNightWerewolfBundle\Entity\RoleCount;
use Lain\OneNightWerewolfBundle\Entity\Room;
use Symfony\Component\HttpFoundation\Request;

class RoomController extends FOSRestController implements ClassResourceInterface
{
    use EntityGettable;

    public function getAction($roomId)
    {
        $room = $this->getDoctrine()->getRepository('LainOneNightWerewolfBundle:Room')->find($roomId);
        return $room;
    }

    public function postAction()
    {
        $room = new Room();
        $objectManager = $this->getDoctrine()->getManager();
        $objectManager->persist($room);
        $objectManager->flush();
        return $room;
    }

    public function postGameAction(Request $request, $roomId) {
        $content = json_decode($request->getContent(), true);
        $regulation = $this->getRegulation($content['regulationId']);
        $game = new Game();
        $game->setRegulation($regulation);
        $playerRoles = $this->createPlayerRoles(
            $this->shuffleRoles($regulation),
            $content['playerIds']
        );
        $objectManager = $this->getDoctrine()->getManager();
        /** @var PlayerRole $playerRole */
        foreach ($playerRoles as $playerRole) {
            $playerRole->setGame($game);
            $game->addPlayerRole($playerRole);
            $objectManager->persist($game);
        }
        $room = $this->getRoom($roomId);
        $game->setRoom($room);
        $room->addGame($game);
        $objectManager->persist($room);
        $objectManager->flush();
        return $game;
    }

    private function shuffleRoles(Regulation $regulation) {
        $roles = Ginq::from($regulation->getRoleCounts())->flatMap(function(RoleCount $roleCount){
            return Ginq::repeat($roleCount->getRole(), $roleCount->getCount());
        })->toList();
        shuffle($roles);
        return $roles;
    }

    private function createPlayerRoles($roles, $playerIds) {
        $roles = Ginq::from($roles)->take(count($playerIds))->toList();
        $res = array_map(function(Role $role, $playerId) {
            $playerRole = new PlayerRole();
            $player = $this->getPlayer($playerId);
            $playerRole->setPlayer($player);
            $playerRole->setRole($role);
            return $playerRole;
        }, $roles, $playerIds);
        return $res;
    }

    public function postPlayerAction(Request $request, $roomId) {
        $content = json_decode($request->getContent(), true);
        $objectManager = $this->getDoctrine()->getManager();
        $room = $this->getRoom($roomId);
        $player = new Player();
        $player->setName($content['name']);
        $player->setRoom($room);
        $room->addPlayer($player);
        $objectManager->persist($room);
        $objectManager->flush();
        return $player;
    }

    public function postRegulationAction(Request $request, $roomId) {
        $content = json_decode($request->getContent(), true);
        $regulation = new Regulation();
        foreach($content['role_counts'] as $roleInfo) {
            $roleCount = new RoleCount();
            $role = $this->getRole($roleInfo['id']);
            $roleCount->setRole($role);
            $roleCount->setCount($roleInfo['count']);
            $roleCount->setRegulation($regulation);
            $regulation->addRoleCount($roleCount);
        }
        $room = $this->getRoom($roomId);
        $regulation->setRoom($room);
        $room->addRegulation($regulation);
        $objectManager = $this->getDoctrine()->getManager();
        $objectManager->persist($room);
        $objectManager->flush();
        return $regulation;
    }

}
