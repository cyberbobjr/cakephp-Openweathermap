<?php
namespace Openweathermap\Test\TestCase\Controller\Component;

use Cake\Controller\ComponentRegistry;
use Cake\TestSuite\TestCase;
use Openweathermap\Controller\Component\OpenweathermapComponent;

/**
 * Openweathermap\Controller\Component\OpenweathermapComponent Test Case
 */
class OpenweathermapComponentTest extends TestCase
{

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $registry             = new ComponentRegistry();
        $this->Openweathermap = new OpenweathermapComponent($registry, ['key' => '1ac998025e1b44ea56a8af2ee5e965dd']);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Openweathermap);

        parent::tearDown();
    }

    /**
     * Test initial setup
     *
     * @return void
     */
    public function testInitialization()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    public function testGetWeatherByGeoloc()
    {
        $data = $this->Openweathermap->getWeatherByGeoloc(48.86189, 2.112527);
        $this->assertEquals(1, $data['success']);
    }
}
