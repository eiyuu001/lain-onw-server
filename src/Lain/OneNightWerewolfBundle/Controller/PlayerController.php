<?php

namespace Lain\OneNightWerewolfBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;
use Lain\OneNightWerewolfBundle\Entity\Room;
use Lain\OneNightWerewolfBundle\Entity\Player;
use Symfony\Component\HttpFoundation\Request;

class PlayerController extends FOSRestController implements ClassResourceInterface
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
        /** @var Room $room */
        $objectManager = $this->getDoctrine()->getManager();
        $room = $objectManager->getRepository('LainOneNightWerewolfBundle:Room')->find($roomId);
        $player = new Player();
        $player->setName($content['name']);
        $player->setToken(bin2hex(openssl_random_pseudo_bytes(4)));
        $player->setRoom($room);
        $room->addPlayer($player);
        $objectManager->persist($room);
        $objectManager->flush();
        return $player;
    }
}
