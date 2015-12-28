<?php

namespace Lain\OneNightWerewolfBundle\Controller\Traits;

trait EntityRefreshable {
    protected function refresh($entity) {
        $manager = $this->getDoctrine()->getManager();
        $manager->refresh($entity);
    }
}