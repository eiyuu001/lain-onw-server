<?php

namespace Lain\OneNightWerewolfBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;

class RoleController extends FOSRestController implements ClassResourceInterface
{
    public function cgetAction()
    {
        return $this->getDoctrine()
            ->getRepository('LainOneNightWerewolfBundle:Role')
            ->findAll();
    }
}
