<?php

namespace Lain\OneNightWerewolfBundle\Controller\Traits;

trait EntityPersistable {
    protected function persist($entity, $flush = true) {
        $manager = $this->getDoctrine()->getManager();
        $manager->persist($entity);
        if ($flush) {
            $manager->flush();
        }
    }
}