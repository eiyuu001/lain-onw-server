<?php

namespace Lain\OneNightWerewolfBundle\Tests;

use Ginq\Ginq;
use Lain\OneNightWerewolfBundle\DataFixtures\ORM\LoadRoleData;
use Lain\OneNightWerewolfBundle\DataFixtures\ORM\LoadTestGameData;
use Symfony\Bundle\FrameworkBundle\Client;

class GameTest extends FixtureAwareTestCase
{
    /**
     * @var Client $client
     */
    private $client;

    public function setUp() {
        parent::setUp();
        self::addFixture(new LoadRoleData());
        self::addFixture(new LoadTestGameData());
        self::executeFixtures();
        $this->client = self::createClient();
    }

    public function testRoomCreated() {
        $rooms = $this->getResource('/rooms');
        $this->assertTrue(isset($rooms[0]->id));
        $roomId = $rooms[0]->id;
        $this->assertNotEmpty($roomId);
    }

    private function getResource($path) {
        $this->client->request('GET', $path);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        return json_decode($this->client->getResponse()->getContent());
    }

}