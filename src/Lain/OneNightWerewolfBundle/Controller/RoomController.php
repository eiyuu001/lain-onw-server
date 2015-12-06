<?php

namespace Lain\OneNightWerewolfBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;
use Ginq\Ginq;
use Lain\OneNightWerewolfBundle\Controller\Traits\EntityGettable;
use Lain\OneNightWerewolfBundle\Entity\Game;
use Lain\OneNightWerewolfBundle\Entity\Player;
use Lain\OneNightWerewolfBundle\Entity\GamePlayer;
use Lain\OneNightWerewolfBundle\Entity\Regulation;
use Lain\OneNightWerewolfBundle\Entity\Role;
use Lain\OneNightWerewolfBundle\Entity\RoleConfig;
use Lain\OneNightWerewolfBundle\Entity\Room;
use Symfony\Component\HttpFoundation\Request;

class RoomController extends FOSRestController implements ClassResourceInterface
{
    use EntityGettable;

    public function cgetAction() {
        return $this->getRooms();
    }

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
        $gamePlayers = $this->createGamePlayers(
            $this->shuffleRoles($regulation),
            $content['playerIds']
        );
        $objectManager = $this->getDoctrine()->getManager();
        /** @var GamePlayer $gamePlayer */
        foreach ($gamePlayers as $gamePlayer) {
            $gamePlayer->setGame($game);
            $objectManager->persist($gamePlayer->getPlayer());
            $game->addGamePlayer($gamePlayer);
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
        $roles = Ginq::from($regulation->getRoleConfigs())->flatMap(function(RoleConfig $roleConfig){
            return Ginq::repeat($roleConfig->getRole(), $roleConfig->getCount());
        })->toList();
        shuffle($roles);
        return $roles;
    }

    private function createGamePlayers($roles, $playerIds) {
        $roles = Ginq::from($roles)->take(count($playerIds))->toList();
        $res = array_map(function(Role $role, $playerId) {
            $gamePlayer = new GamePlayer();
            $player = $this->getPlayer($playerId);
            $gamePlayer->setPlayer($player);
            $gamePlayer->setRole($role);
            $player->addGamePlayer($gamePlayer);
            return $gamePlayer;
        }, $roles, $playerIds);
        return $res;
    }
	
	public function getPlayersAction($roomId) {
		$room = $this->getRoom($roomId);
		return $room->getPlayers();
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
        foreach($content['roleConfigs'] as $roleInfo) {
            $roleConfig = new RoleConfig();
            $role = $this->getRole($roleInfo['id']);
            $roleConfig->setRole($role);
            $roleConfig->setCount($roleInfo['count']);
			$roleConfig->setRewardForSurvivor($roleInfo['rewardForSurvivor']);
			$roleConfig->setRewardForDead($roleInfo['rewardForDead']);
            $roleConfig->setRegulation($regulation);
            $regulation->addRoleConfig($roleConfig);
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
