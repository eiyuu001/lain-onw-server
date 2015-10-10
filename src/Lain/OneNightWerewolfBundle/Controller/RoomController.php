<?php

namespace Lain\OneNightWerewolfBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;

class RoomController extends FOSRestController implements ClassResourceInterface
{
    public function cgetAction()
    {
        $view = $this->view(['foon' => 'asso'], 200);
        return $this->handleView($view);
    }
}
