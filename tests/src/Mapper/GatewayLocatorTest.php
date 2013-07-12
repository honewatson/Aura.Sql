<?php
namespace Aura\Sql\Mapper;

class GatewayLocatorTest extends \PHPUnit_Framework_TestCase
{
    protected $gateways;

    protected function setUp()
    {
        parent::setUp();
        $registry = [
            'posts' => function() {
                $gateway = (object) ['type' => 'post'];
                return $gateway;
            },
            'comments' => function() {
                $gateway = (object) ['type' => 'comment'];
                return $gateway;
            },
            'authors' => function() {
                $gateway = (object) ['type' => 'author'];
                return $gateway;
            },
        ];
        
        $this->gateways = new GatewayLocator($registry);
    }

    protected function tearDown()
    {
        parent::tearDown();
    }

    public function testSetAndGet()
    {
        $this->gateways->set('tags', function () {
            $gateway = (object) ['type' => 'tag'];
            return $gateway;
        });
        
        $gateway = $this->gateways->get('tags');
        $this->assertTrue($gateway->type == 'tag');
    }

    public function testGet_noSuchGateway()
    {
        $this->setExpectedException('Aura\Sql\Mapper\Exception\NoSuchGateway');
        $gateway = $this->gateways->get('no-such-gateway');
    }
    
    public function test_iterator()
    {
        $expect = ['post', 'comment', 'author'];
        foreach ($this->gateways as $gateway) {
            $actual[] = $gateway->type;
        }
        $this->assertSame($expect, $actual);
    }
}
