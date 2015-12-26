<?php

namespace Lain\OneNightWerewolfBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;
use JMS\Serializer\SerializationContext;
use Lain\OneNightWerewolfBundle\Controller\Traits\EntityGettable;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

class GameController extends FOSRestController implements ClassResourceInterface
{
    use EntityGettable;

    public function cgetAction() {
        return $this->getGames();
    }

    public function getAction($gameId) {
        $game = $this->getGame($gameId);
        $view = $this->view($game, 200);
        return $view;
    }

    public function getResultAction($gameId) {
        $game = $this->getGame($gameId);
        if (!$game->hasFinished()) {
            throw new ResourceNotFoundException();
        }
        $view = $this->view($game, 200);
        $groups = ['Default', 'finished'];
        $view->setSerializationContext(
            SerializationContext::create()->setGroups($groups)
        );
        return $view;
    }
}
