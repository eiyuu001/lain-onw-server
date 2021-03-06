<?php

namespace Lain\OneNightWerewolfBundle\Controller\Traits;

use Lain\OneNightWerewolfBundle\Entity\Game;
use Lain\OneNightWerewolfBundle\Entity\Player;
use Lain\OneNightWerewolfBundle\Entity\GamePlayer;
use Lain\OneNightWerewolfBundle\Entity\Role;
use Lain\OneNightWerewolfBundle\Entity\RoleConfig;
use Lain\OneNightWerewolfBundle\Entity\RoleGroup;
use Lain\OneNightWerewolfBundle\Entity\Room;

trait EntityGettable {

    protected function getEntity($name, $id) {
        return $this->getDoctrine()
            ->getRepository('LainOneNightWerewolfBundle:' . $name)
            ->find($id);
    }

    /**
     * @param $id
     * @return Game
     */
    protected function getGame($id) {
        return $this->getEntity('Game', $id);
    }

    /**
     * @param $id
     * @return Player
     */
    protected function getPlayer($id) {
        return $this->getEntity('Player', $id);
    }

    /**
     * @param $gameId
     * @param $playerId
     * @return GamePlayer
     */
    protected function getGamePlayer($gameId, $playerId) {
        return $this->getDoctrine()
            ->getRepository('LainOneNightWerewolfBundle:GamePlayer')
            ->findOneBy([
                'game' => $gameId,
                'player' => $playerId,
            ]);
    }

    /**
     * @param $id
     * @return Role
     */
    protected function getRole($id) {
        return $this->getEntity('Role', $id);
    }

    /**
     * @param $id
     * @return RoleConfig
     */
    protected function getRoleConfig($id) {
        return $this->getEntity('RoleConfig', $id);
    }

    /**
     * @param $id
     * @return RoleGroup
     */
    protected function getRoleGroup($id) {
        return $this->getEntity('RoleGroup', $id);
    }

    /**
     * @param $id
     * @return Room
     */
    protected function getRoom($id) {
        return $this->getEntity('Room', $id);
    }

    /**
     * @param $name
     * @return array
     */
    protected function getEntities($name) {
        return $this->getDoctrine()
            ->getRepository('LainOneNightWerewolfBundle:' . $name)
            ->findAll();
    }

    /**
     * @return array
     */
    protected function getGames() {
        return $this->getEntities('Game');
    }

    /**
     * @return array
     */
    protected function getPlayers() {
        return $this->getEntities('Player');
    }

    /**
     * @return array
     */
    protected function getGamePlayers() {
        return $this->getEntities('GamePlayer');
    }

    /**
     * @return array
     */
    protected function getRoles() {
        return $this->getEntities('Role');
    }

    /**
     * @return array
     */
    protected function getRoleConfigs() {
        return $this->getEntities('RoleConfig');
    }

    /**
     * @return array
     */
    protected function getRoleGroups() {
        return $this->getEntities('RoleGroup');
    }

    /**
     * @return array
     */
    protected function getRooms() {
        return $this->getEntities('Room');
    }

}