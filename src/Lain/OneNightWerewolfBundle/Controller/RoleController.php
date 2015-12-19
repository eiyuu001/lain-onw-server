<?php

namespace Lain\OneNightWerewolfBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;
use Lain\OneNightWerewolfBundle\Controller\Traits\EntityGettable;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

class RoleController extends FOSRestController implements ClassResourceInterface
{
    use EntityGettable;

    /**
     * @ApiDoc(
     *  resource=true,
     *  description="Returns a collection of Role"
     * )
     */
    public function cgetAction() {
        return $this->getRoles();
    }

    /**
     * @ApiDoc(
     *  resource=true,
     *  description="Returns a Role object specified by roleId",
     *  requirements={
     *      {"name"="roleId", "dataType"="integer", "requirement"="\d+", "description"="role id"}
     *  }
     * )
     */
    public function getAction($roleId) {
        return $this->getRole($roleId);
    }
}
