<?php

namespace Lain\OneNightWerewolfBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\Util\Codes;
use Lain\OneNightWerewolfBundle\Controller\Traits\EntityGettable;
use Lain\OneNightWerewolfBundle\Controller\Traits\EntityPersistable;
use Lain\OneNightWerewolfBundle\Controller\Traits\EntityRefreshable;
use Lain\OneNightWerewolfBundle\Entity\Game;
use Lain\OneNightWerewolfBundle\Entity\Player;
use Lain\OneNightWerewolfBundle\Entity\RoleConfig;
use Lain\OneNightWerewolfBundle\Entity\Room;
use JMS\Serializer\SerializationContext;
use Symfony\Component\HttpFoundation\Request;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

class RoomController extends FOSRestController implements ClassResourceInterface
{
    use EntityGettable;
    use EntityPersistable;
    use EntityRefreshable;

    /**
     * @ApiDoc(
     *  resource=true,
     *  description="Returns a collection of Room"
     * )
     */
    public function cgetAction() {
        return $this->getRooms();
    }

    /**
     * @ApiDoc(
     *  resource=true,
     *  description="Returns a Room object specified by roomId",
     *  requirements={
     *      {"name"="roomId", "dataType"="integer", "requirement"="\d+", "description"="room id"}
     *  }
     * )
     */
    public function getAction($roomId)
    {
        return $this->getRoom($roomId);
    }

    /**
     * @ApiDoc(
     *  description="Create a new room",
     *  input={
     *    "class"="Lain\OneNightWerewolfBundle\Entity\Room",
     *    "groups"={"postRoom"}
     *  }
     * )
     */
    public function postAction(Request $request)
    {
        $content = json_decode($request->getContent(), true);
        $room = new Room();
        foreach($content['roleConfigs'] as $roleId => $roleConfigSrc) {
            $roleConfig = new RoleConfig($room);
            $role = $this->getRole($roleId);
            $roleConfig->setRole($role);
            $roleConfig->setCount($roleConfigSrc['count']);
            $roleConfig->setRewardForSurvivor($roleConfigSrc['rewardForSurvivor']);
            $roleConfig->setRewardForDead($roleConfigSrc['rewardForDead']);
            $this->persist($roleConfig, false);
        }
        $this->persist($room);
        $this->refresh($room);
        return $this->view($room, Codes::HTTP_CREATED)
            ->setRoute('get_room')
            ->setRouteParameters(['roomId' => $room->getId()]);
    }

    /**
     * @ApiDoc(
     *  description="Create a new game",
     *  requirements={
     *      {"name"="roomId", "dataType"="integer", "requirement"="\d+", "description"="room id"}
     *  }
     * )
     */
    public function postGameAction($roomId) {
        $game = new Game($this->getRoom($roomId));
        $game->castRoles();
        $this->persist($game);
        $this->refresh($game);
        return $this->view($game, Codes::HTTP_CREATED)
            ->setRoute('get_game')
            ->setRouteParameters(['gameId' => $game->getId()])
            ->setSerializationContext(SerializationContext::create()->setGroups(['Default']));
    }

    /**
     * @ApiDoc(
     *  description="Create a new player",
     *  requirements={
     *      {"name"="roomId", "dataType"="integer", "requirement"="\d+", "description"="room id"}
     *  },
     *  input={
     *    "class"="Lain\OneNightWerewolfBundle\Entity\Player",
     *    "groups"={"postPlayer"}
     *  }
     * )
     */
    public function postPlayerAction(Request $request, $roomId) {
        $content = json_decode($request->getContent(), true);
        $player = new Player($this->getRoom($roomId));
        $player->setName($content['name']);
        $this->persist($player);
        return $this->view($player, Codes::HTTP_CREATED)
            ->setRoute('get_player')
            ->setRouteParameters(['playerId' => $player->getId()]);
    }

}
