<?php

namespace Lain\OneNightWerewolfBundle\Controller\Traits;

use Lain\OneNightWerewolfBundle\Entity\Game;
use Lain\OneNightWerewolfBundle\Entity\Player;
use Lain\OneNightWerewolfBundle\Entity\PlayerRole;
use Lain\OneNightWerewolfBundle\Entity\Regulation;
use Lain\OneNightWerewolfBundle\Entity\Role;
use Lain\OneNightWerewolfBundle\Entity\RoleCount;
use Lain\OneNightWerewolfBundle\Entity\Room;
use Lain\OneNightWerewolfBundle\Entity\Vote;

trait EntityGettable {

    private function getEntity($name, $id) {
        return $this->getDoctrine()
            ->getRepository('LainOneNightWerewolfBundle:' . $name)
            ->find($id);
    }

    /**
     * @param $id
     * @return Game
     */
    private function getGame($id) {
        return $this->getEntity('Game', $id);
    }

    /**
     * @param $id
     * @return Player
     */
    private function getPlayer($id) {
        return $this->getEntity('Player', $id);
    }

    /**
     * @param $id
     * @return PlayerRole
     */
    private function getPlayerRole($id) {
        return $this->getEntity('PlayerRole', $id);
    }

    /**
     * @param $id
     * @return Regulation
     */
    private function getRegulation($id) {
        return $this->getEntity('Regulation', $id);
    }

    /**
     * @param $id
     * @return Role
     */
    private function getRole($id) {
        return $this->getEntity('Role', $id);
    }

    /**
     * @param $id
     * @return RoleCount
     */
    private function getRoleCount($id) {
        return $this->getEntity('RoleCount', $id);
    }

    /**
     * @param $id
     * @return Room
     */
    private function getRoom($id) {
        return $this->getEntity('Room', $id);
    }

    /**
     * @param $id
     * @return Vote
     */
    private function getVote($id) {
        return $this->getEntity('Vote', $id);
    }

}