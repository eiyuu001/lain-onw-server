<?php

namespace Lain\OneNightWerewolfBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;
use Lain\OneNightWerewolfBundle\Controller\Traits\EntityGettable;

class RegulationController extends FOSRestController implements ClassResourceInterface
{
    use EntityGettable;

    public function cgetAction() {
        return $this->getRegulations();
    }

    public function getAction($regulationId) {
        return $this->getRegulation($regulationId);
    }
}
