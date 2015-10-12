<?php

namespace Lain\OneNightWerewolfBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Lain\OneNightWerewolfBundle\Entity\Player;
use Lain\OneNightWerewolfBundle\Entity\Room;

class LoadRoomData implements FixtureInterface, OrderedFixtureInterface
{
    /**
    * {@inheritDoc}
    */
    public function load(ObjectManager $manager)
    {
        $room = new Room();
        $manager->persist($room);

        $records = [
            [
                'name' => 'eiyuu',
                'token' => '_eiyuu',
            ],
            [
                'name' => 'longeman',
                'token' => '_longeman',
            ],
        ];

        foreach ($records as $record) {
            $player = new Player();
            $player->setName($record['name']);
            $player->setToken($record['token']);
            $player->setRoom($room);
            $room->addPlayer($player);
            $manager->persist($room);
        }

        $manager->flush();
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 1;
    }
}