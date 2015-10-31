<?php

namespace Lain\OneNightWerewolfBundle\Tests;

use Lain\OneNightWerewolfBundle\DataFixtures\ORM\LoadRoleData;
use Lain\OneNightWerewolfBundle\DataFixtures\ORM\LoadTestGameData;

class GameTest extends FixtureAwareTestCase
{
    public function setUp() {
        parent::setUp();
        self::addFixture(new LoadRoleData());
        self::addFixture(new LoadTestGameData());
        self::executeFixtures();
    }

    public function test() {

    }
}