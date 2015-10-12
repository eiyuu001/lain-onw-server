<?php

namespace Lain\OneNightWerewolfBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;
use Lain\OneNightWerewolfBundle\Entity\Regulation;
use Lain\OneNightWerewolfBundle\Entity\RoleCount;
use Lain\OneNightWerewolfBundle\Entity\Room;
use Symfony\Component\HttpFoundation\Request;

class RegulationController extends FOSRestController implements ClassResourceInterface
{
    public function cgetAction($roomId) {
        /** @var Room $room */
        $room = $this->getDoctrine()->getRepository('LainOneNightWerewolfBundle:Room')->find($roomId);
        return $room->getRegulations();
    }

    public function getAction($roomId, $regulationId) {
        /** @var Room $room */
        $room = $this->getDoctrine()->getRepository('LainOneNightWerewolfBundle:Room')->find($roomId);
        $regulation = $room->getPlayers()->filter(function(Regulation $regulation) use ($regulationId){
            return $regulation->getId() === $regulationId;
        })->first();
        return $regulation;
    }

    public function postAction(Request $request, $roomId) {
        $content = json_decode($request->getContent(), true);
        $regulation = new Regulation();
        $roleRepository = $this->getDoctrine()->getRepository('LainOneNightWerewolfBundle:Role');
        foreach($content['role_counts'] as $roleInfo) {
            $roleCount = new RoleCount();
            $role = $roleRepository->find($roleInfo['id']);
            $roleCount->setRole($role);
            $roleCount->setCount($roleInfo['count']);
            $roleCount->setRegulation($regulation);
            $regulation->addRoleCount($roleCount);
        }
        $regulation->setPlayers($content['players']);
        /** @var Room $room */
        $room = $this->getDoctrine()->getRepository('LainOneNightWerewolfBundle:Room')->find($roomId);
        $regulation->setRoom($room);
        $room->addRegulation($regulation);
        $objectManager = $this->getDoctrine()->getManager();
        $objectManager->persist($room);
        $objectManager->flush();
        return $regulation;
    }
}
