<?php

namespace Acme\HelloBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Lain\OneNightWerewolfBundle\Entity\Regulation;
use Lain\OneNightWerewolfBundle\Entity\Role;
use Lain\OneNightWerewolfBundle\Entity\RoleCount;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;

class LoadRegulationData implements FixtureInterface, OrderedFixtureInterface, ContainerAwareInterface
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
        $records = [
            [
                'roles' => [
                    '人狼' => 2,
                    '村人' => 3,
                    '占い師' => 1,
                    '怪盗' => 1,
                    '吊人' => 1,
                ],
            ],
            [
                'roles' => [
                    '人狼' => 2,
                    '村人' => 3,
                    '占い師' => 1,
                    '怪盗' => 1,
                    '狂人' => 1,
                ],
            ],
        ];
        $roleRepository = $this->container->get('doctrine')->getRepository('LainOneNightWerewolfBundle:Role');
        foreach($records as $record) {
            $regulation = new Regulation();
            foreach($record['roles'] as $roleName => $count) {
                $roleCount = new RoleCount();
                $role = $roleRepository->findOneBy(['name' => $roleName]);
                $roleCount->setRole($role);
                $roleCount->setCount($count);
                $roleCount->setRegulation($regulation);
                $regulation->addRoleCount($roleCount);
            }
            $manager->persist($regulation);
        }
        $manager->flush();
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 2;
    }
}