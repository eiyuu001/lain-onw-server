<?php

namespace Acme\HelloBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Lain\OneNightWerewolfBundle\Entity\Role;

class LoadRoleData implements FixtureInterface, OrderedFixtureInterface
{
    /**
    * {@inheritDoc}
    */
    public function load(ObjectManager $manager)
    {
        $records = [
            [
                'name' => '人狼',
            ],
            [
                'name' => '村人',
            ],
            [
                'name' => '吊人',
            ],
            [
                'name' => '占い師',
            ],
            [
                'name' => '怪盗',
            ],
            [
                'name' => '狂人',
            ],
        ];
        foreach($records as $record) {
            $role = new Role();
            $role->setName($record['name']);
            $manager->persist($role);
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