<?php

namespace Lain\OneNightWerewolfBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;
use Ginq\Ginq;
use Lain\OneNightWerewolfBundle\Entity\Game;
use Lain\OneNightWerewolfBundle\Entity\PlayerRole;
use Lain\OneNightWerewolfBundle\Entity\Regulation;
use Lain\OneNightWerewolfBundle\Entity\Role;
use Lain\OneNightWerewolfBundle\Entity\RoleCount;
use Lain\OneNightWerewolfBundle\Entity\Room;
use Lain\OneNightWerewolfBundle\Entity\Player;
use Symfony\Component\HttpFoundation\Request;

class GameController extends FOSRestController implements ClassResourceInterface
{
    public function cgetAction($roomId) {
        /** @var Room $room */
        $room = $this->getDoctrine()->getRepository('LainOneNightWerewolfBundle:Room')->find($roomId);
        return $room->getPlayers();
    }

    public function getAction($roomId, $playerId) {
        /** @var Room $room */
        $room = $this->getDoctrine()->getRepository('LainOneNightWerewolfBundle:Room')->find($roomId);
        $player = $room->getPlayers()->filter(function(Player $player) use ($playerId){
            return $player->getId() === $playerId;
        })->first();
        return $player;
    }

    public function postAction(Request $request, $roomId) {
        $content = json_decode($request->getContent(), true);
        /** @var Regulation $regulation */
        $regulation = $this->getDoctrine()
            ->getRepository('LainOneNightWerewolfBundle:Regulation')
            ->find($content['regulationId']);
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
        /** @var Room $room */
        $room = $this->getDoctrine()
            ->getRepository('LainOneNightWerewolfBundle:Room')
            ->find($roomId);
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
            /** @var Player $player */
            $player = $this->getDoctrine()
                ->getRepository('LainOneNightWerewolfBundle:Player')
                ->find($playerId);
            $playerRole->setPlayer($player);
            $playerRole->setRole($role);
            return $playerRole;
        }, $roles, $playerIds);
        return $res;
    }

}
