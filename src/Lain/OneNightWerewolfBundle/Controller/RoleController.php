<?php

namespace Lain\OneNightWerewolfBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;

class RoleController extends FOSRestController implements ClassResourceInterface
{
    public function cgetAction()
    {
        $roles = $this->getDoctrine()
            ->getRepository('LainOneNightWerewolfBundle:Role')
            ->findAll();
        $view = $this->view($roles, 200);
        return $this->handleView($view);
    }
}
