<?php

namespace Lain\OneNightWerewolfBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;
use Lain\OneNightWerewolfBundle\Controller\Traits\EntityGettable;

class RoleController extends FOSRestController implements ClassResourceInterface
{
    use EntityGettable;

    public function cgetAction() {
        return $this->getRoles();
    }

    public function getAction($roleId) {
        return $this->getRole($roleId);
    }
}
