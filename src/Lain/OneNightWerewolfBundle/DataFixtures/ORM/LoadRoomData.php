<?php

namespace Lain\OneNightWerewolfBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Lain\OneNightWerewolfBundle\Entity\Player;
use Lain\OneNightWerewolfBundle\Entity\Regulation;
use Lain\OneNightWerewolfBundle\Entity\RoleCount;
use Lain\OneNightWerewolfBundle\Entity\Room;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;

class LoadRoomData implements FixtureInterface, OrderedFixtureInterface, ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * {@inheritDoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
    * {@inheritDoc}
    */
    public function load(ObjectManager $manager)
    {
        $room = new Room();
        $manager->persist($room);

        $this->loadPlayer($manager, $room);
        $this->loadRegulation($manager, $room);

        $manager->flush();
    }

    private function loadPlayer(ObjectManager $manager, Room $room) {
        $records = [
            [
                'name' => 'eiyuu',
            ],
            [
                'name' => 'longeman',
            ],
        ];

        foreach ($records as $record) {
            $player = new Player();
            $player->setName($record['name']);
            $player->setRoom($room);
            $room->addPlayer($player);
            $manager->persist($room);
        }
    }

    private function loadRegulation(ObjectManager $manager, Room $room) {
        $records = [
            [
                'roles' => [
                    '人狼' => [
                        'count' => 2,
                        'rewardAmount' => 3,
                        'deathDecrease' => 3,
                    ],
                    '村人' => [
                        'count' => 3,
                        'rewardAmount' => 2,
                        'deathDecrease' => 1,
                    ],
                    '占い師' => [
                        'count' => 1,
                        'rewardAmount' => 2,
                        'deathDecrease' => 1,
                    ],
                    '怪盗' => [
                        'count' => 1,
                        'rewardAmount' => 2,
                        'deathDecrease' => 1,
                    ],
                    '狂人' => [
                        'count' => 1,
                        'rewardAmount' => 3,
                        'deathDecrease' => 2,
                    ],
                ],
            ],
        ];

        $roleRepository = $this->container->get('doctrine')->getRepository('LainOneNightWerewolfBundle:Role');
        foreach($records as $record) {
            $regulation = new Regulation();
            foreach($record['roles'] as $roleName => $roleInfo) {
                $roleCount = new RoleCount();
                $role = $roleRepository->findOneBy(['name' => $roleName]);
                $roleCount->setRole($role);
                $roleCount->setCount($roleInfo['count']);
                $roleCount->setRewardAmount($roleInfo['rewardAmount']);
                $roleCount->setDeathDecrease($roleInfo['deathDecrease']);
                $roleCount->setRegulation($regulation);
                $regulation->addRoleCount($roleCount);
            }
            $regulation->setRoom($room);
            $room->addRegulation($regulation);
            $manager->persist($regulation);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 2;
    }
}