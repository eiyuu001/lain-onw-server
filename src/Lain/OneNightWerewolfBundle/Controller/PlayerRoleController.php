<?php

namespace Lain\OneNightWerewolfBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use JMS\Serializer\SerializationContext;
use Lain\OneNightWerewolfBundle\Controller\Traits\EntityGettable;

/**
 * @RouteResource("Player")
 */
class PlayerRoleController extends FOSRestController implements ClassResourceInterface
{
    use EntityGettable;

    public function getAction($gameId, $playerId) {
        $playerRole = $this->getPlayerRole($gameId, $playerId);
        $view = $this->view($playerRole, 200);
        $groups = ['Default'];
        if (true) { // todo: �g�[�N���Ȃǂɂ��{�l�F�؂��o����ꍇ�̂�'secret'��t��
            array_push($groups, 'secret');
        }
        $view->setSerializationContext(
            SerializationContext::create()->setGroups($groups)
        );
        return $view;
    }
}
