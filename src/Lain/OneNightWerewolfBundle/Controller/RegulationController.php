<?php

namespace Lain\OneNightWerewolfBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;
use Lain\OneNightWerewolfBundle\Entity\Regulation;
use Lain\OneNightWerewolfBundle\Entity\RoleCount;
use Symfony\Component\HttpFoundation\Request;

class RegulationController extends FOSRestController implements ClassResourceInterface
{
    public function cgetAction()
    {
        return $this->getDoctrine()
            ->getRepository('LainOneNightWerewolfBundle:Regulation')
            ->findAll();
    }

    public function getAction($regulationId)
    {
        return $this->getDoctrine()
            ->getRepository('LainOneNightWerewolfBundle:Regulation')
            ->find($regulationId);
    }

    public function postAction(Request $request) {
        $content = json_decode($request->getContent(), true);
        $regulation = new Regulation();
        $roleRepository = $this->getDoctrine()->getRepository('LainOneNightWerewolfBundle:Role');
        foreach($content['role_counts'] as $roleInfo) {
            $roleCount = new RoleCount();
            $role = $roleRepository->find($roleInfo['id']);
            $roleCount->setRole($role);
            $roleCount->setCount($roleInfo['count']);
            $roleCount->setRegulation($regulation);
            $regulation->addRoleCount($roleCount);
        }
        $regulation->setPlayers($content['players']);
        $objectManager = $this->getDoctrine()->getManager();
        $objectManager->persist($regulation);
        $objectManager->flush();
        return $regulation;
    }
}
