<?php

namespace Lain\OneNightWerewolfBundle\Utils\Entity\Id;

use Doctrine\ORM\Id\AbstractIdGenerator;
use Doctrine\ORM\EntityManager;

class BasicGenerator extends AbstractIdGenerator
{
    /**
     * {@inheritdoc}
     */
    public function generate(EntityManager $em, $entity)
    {
        do {
            $bytes = openssl_random_pseudo_bytes(4);
            $id = date('Ymd') . ':' . bin2hex($bytes);
        } while(!$this->isGoodId($em, $entity, $id));
        return $id;
    }

    private function isGoodId(EntityManager $em, $entity, $id) {
        return $em->getRepository(get_class($entity))->find($id) === null;
    }

}