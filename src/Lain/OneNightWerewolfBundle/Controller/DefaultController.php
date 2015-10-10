<?php

namespace Lain\OneNightWerewolfBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('LainOneNightWerewolfBundle:Default:index.html.twig', array('name' => $name));
    }
}
