<?php

namespace Lain\OneNightWerewolfBundle\Controller\Traits;

use Lain\OneNightWerewolfBundle\Entity\Game;
use Lain\OneNightWerewolfBundle\Entity\Player;
use Lain\OneNightWerewolfBundle\Entity\PlayerRole;
use Lain\OneNightWerewolfBundle\Entity\Regulation;
use Lain\OneNightWerewolfBundle\Entity\Role;
use Lain\OneNightWerewolfBundle\Entity\RoleCount;
use Lain\OneNightWerewolfBundle\Entity\RoleGroup;
use Lain\OneNightWerewolfBundle\Entity\Room;
use Lain\OneNightWerewolfBundle\Entity\Vote;

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
     * @return PlayerRole
     */
    protected function getPlayerRole($gameId, $playerId) {
        return $this->getDoctrine()
            ->getRepository('LainOneNightWerewolfBundle:PlayerRole')
            ->findOneBy([
                'game' => $gameId,
                'player' => $playerId,
            ]);
    }

    /**
     * @param $id
     * @return Regulation
     */
    protected function getRegulation($id) {
        return $this->getEntity('Regulation', $id);
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
     * @return RoleCount
     */
    protected function getRoleCount($id) {
        return $this->getEntity('RoleCount', $id);
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
     * @param $id
     * @return Vote
     */
    protected function getVote($id) {
        return $this->getEntity('Vote', $id);
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
        return $this->getEntities('Game');
    }

    /**
     * @return array
     */
    protected function getPlayerRoles() {
        return $this->getEntities('Game');
    }

    /**
     * @return array
     */
    protected function getRegulations() {
        return $this->getEntities('Game');
    }

    /**
     * @return array
     */
    protected function getRoles() {
        return $this->getEntities('Game');
    }

    /**
     * @return array
     */
    protected function getRoleCounts() {
        return $this->getEntities('Game');
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
        return $this->getEntities('Game');
    }

    /**
     * @return array
     */
    protected function getVotes() {
        return $this->getEntities('Game');
    }

}