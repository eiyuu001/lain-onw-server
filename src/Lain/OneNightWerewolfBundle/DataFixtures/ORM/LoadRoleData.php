<?php

namespace Lain\OneNightWerewolfBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Lain\OneNightWerewolfBundle\Entity\Role;
use Lain\OneNightWerewolfBundle\Entity\RoleGroup;

class LoadRoleData implements FixtureInterface, OrderedFixtureInterface
{
    /**
    * {@inheritDoc}
    */
    public function load(ObjectManager $manager)
    {
        $records = [
            [
                'name' => '人狼チーム',
                'roles' => [
                    [
                        'name' => '人狼',
                    ],
                    [
                        'name' => '狂人',
                    ],
                ]
            ],
            [
                'name' => '村人チーム',
                'roles' => [
                    [
                        'name' => '村人',
                    ],
                    [
                        'name' => '占い師',
                    ],
                    [
                        'name' => '怪盗',
                    ],
                ]
            ],
            [
                'name' => '吊人チーム',
                'roles' => [
                    [
                        'name' => '吊人',
                    ],
                ],
            ]
        ];
        foreach($records as $record) {
            $roleGroup = new RoleGroup();
            $roleGroup->setName($record['name']);
            foreach($record['roles'] as $roleRecord) {
                $role = new Role();
                $role->setName($roleRecord['name']);
                $role->setRoleGroup($roleGroup);
                $roleGroup->addRole($role);
                $manager->persist($roleGroup);
            }
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