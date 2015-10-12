<?php

namespace Lain\OneNightWerewolfBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;
use Lain\OneNightWerewolfBundle\Entity\Room;

class RoomController extends FOSRestController implements ClassResourceInterface
{
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
}
