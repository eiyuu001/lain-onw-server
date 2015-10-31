<?php

namespace Lain\OneNightWerewolfBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Lain\OneNightWerewolfBundle\Entity\Game;
use Lain\OneNightWerewolfBundle\Entity\Player;
use Lain\OneNightWerewolfBundle\Entity\PlayerRole;
use Lain\OneNightWerewolfBundle\Entity\Regulation;
use Lain\OneNightWerewolfBundle\Entity\Role;
use Lain\OneNightWerewolfBundle\Entity\RoleCount;
use Lain\OneNightWerewolfBundle\Entity\Room;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;

class LoadTestGameData implements FixtureInterface, OrderedFixtureInterface, ContainerAwareInterface
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
        $playerRecords = [
            [
                'name' => 'werewolf',
                'role' => '人狼'
            ],
            [
                'name' => 'madman',
                'role' => '狂人'
            ],
            [
                'name' => 'villager',
                'role' => '村人'
            ],
            [
                'name' => 'fortune teller',
                'role' => '占い師'
            ],
            [
                'name' => 'phantom thief',
                'role' => '怪盗'
            ],
            [
                'name' => 'hanged man',
                'role' => '吊人'
            ],
        ];

        $room = new Room();
        $manager->persist($room);

        $regulations = $this->loadRegulation($manager, $room);
        $players = $this->loadPlayer($manager, $room, $playerRecords);
        $games = $this->loadGame($manager, $room, $regulations[0], $playerRecords);

        $manager->flush();
    }

    private function loadRegulation(ObjectManager $manager, Room $room) {
        $records = [
            [
                'roles' => [
                    '人狼' => [
                        'count' => 1,
                        'rewardAmount' => 3,
                        'deathDecrease' => 3,
                    ],
                    '村人' => [
                        'count' => 1,
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
                    '吊人' => [
                        'count' => 1,
                        'rewardAmount' => 3,
                        'deathDecrease' => 0,
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
        $manager->flush();
        return $manager->getRepository('LainOneNightWerewolfBundle:Regulation')->findAll();
    }

    private function loadPlayer(ObjectManager $manager, Room $room, $playerRecords) {
        foreach ($playerRecords as $record) {
            $player = new Player();
            $player->setName($record['name']);
            $player->setRoom($room);
            $room->addPlayer($player);
            $manager->persist($room);
        }
        $manager->flush();
        return $manager->getRepository('LainOneNightWerewolfBundle:Player')->findAll();
    }

    private function loadGame(ObjectManager $manager, Room $room, Regulation $regulation, $playerRecords) {
        $game = new Game();
        $game->setRegulation($regulation);
        foreach ($playerRecords as $record) {
            $playerRole = new PlayerRole();
            $playerRole->setGame($game);
            $game->addPlayerRole($playerRole);

            /** @var Role $role */
            $role = $manager->getRepository('LainOneNightWerewolfBundle:Role')
                ->findOneBy(['name' => $record['role']]);
            $playerRole->setRole($role);

            /** @var Player $player */
            $player = $manager->getRepository('LainOneNightWerewolfBundle:Player')
                ->findOneBy(['name' => $record['name']]);
            $playerRole->setPlayer($player);
            $player->addPlayerRole($playerRole);
            $manager->persist($player);
        }
        $game->setRoom($room);
        $room->addGame($game);
        $manager->persist($room);
        $manager->flush();
        return $manager->getRepository('LainOneNightWerewolfBundle:Game')->findAll();
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 2;
    }
}