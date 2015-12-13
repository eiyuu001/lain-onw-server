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
        return $this->getRoom($roomId);
    }

    public function postAction()
    {
        $room = new Room();
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($room);
        $entityManager->flush();
        return $room;
    }

    public function postGameAction(Request $request, $roomId) {
        $content = json_decode($request->getContent(), true);
        $game = new Game($this->getRoom($roomId));
        $regulation = $this->getRegulation($content['regulation']);
        $game->setRegulation($regulation);
        $roles = Ginq::from($this->shuffleRoles($regulation))->take(count($content['players']))->toList();
        $entityManager = $this->getDoctrine()->getManager();
        array_map(function(Role $role, $playerId) use ($game, $entityManager) {
            $gamePlayer = new GamePlayer($game);
            $gamePlayer->setPlayer($this->getPlayer($playerId));
            $gamePlayer->setRole($role);
            $entityManager->persist($gamePlayer);
        }, $roles, $content['players']);
        $entityManager->persist($game);
        $entityManager->flush();
        $entityManager->refresh($game);
        return $game;
    }

    private function shuffleRoles(Regulation $regulation) {
        $roles = Ginq::from($regulation->getRoleConfigs())->flatMap(function(RoleConfig $roleConfig){
            return Ginq::repeat($roleConfig->getRole(), $roleConfig->getCount());
        })->toList();
        shuffle($roles);
        return $roles;
    }

    public function getPlayersAction($roomId) {
        $room = $this->getRoom($roomId);
        return $room->getPlayers();
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

    public function postRegulationAction(Request $request, $roomId) {
        $content = json_decode($request->getContent(), true);
        $entityManager = $this->getDoctrine()->getManager();
        $regulation = new Regulation($this->getRoom($roomId));
        foreach($content['roleConfigs'] as $roleConfigSrc) {
            $roleConfig = new RoleConfig($regulation);
            $role = $this->getRole($roleConfigSrc['id']);
            $roleConfig->setRole($role);
            $roleConfig->setCount($roleConfigSrc['count']);
            $roleConfig->setRewardForSurvivor($roleConfigSrc['rewardForSurvivor']);
            $roleConfig->setRewardForDead($roleConfigSrc['rewardForDead']);
            $entityManager->persist($roleConfig);
        }
        $entityManager->persist($regulation);
        $entityManager->flush();
        $entityManager->refresh($regulation);
        return $regulation;
    }

}
