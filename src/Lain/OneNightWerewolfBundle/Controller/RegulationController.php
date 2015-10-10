<?php

namespace Lain\OneNightWerewolfBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;

class RegulationController extends FOSRestController implements ClassResourceInterface
{
    public function cgetAction()
    {
        return $this->getDoctrine()
            ->getRepository('LainOneNightWerewolfBundle:Regulation')
            ->findAll();
    }

    public function getAction($regulationId)
    {
        return $this->getDoctrine()
            ->getRepository('LainOneNightWerewolfBundle:Regulation')
            ->find($regulationId);
    }
}
