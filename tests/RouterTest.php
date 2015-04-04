<?php

namespace LRouter\Tests;

use PHPUnit_Framework_TestCase,
    LRouter\Models\Router;

class RouterTest extends PHPUnit_Framework_TestCase {
    private $Router;

    public function setUp() {
        $this->Router = new Router('/foo-bar/', 'GET');
    }

    public function testConstruct() {
        $this->assertEquals($this->Router->getURI(), '/foo-bar/');
        $this->assertEquals($this->Router->getMethod(), 'GET');
    }

    public function testAddRoute() {
        $route = $this->getMockBuilder('LRouter\Models\Route')
            ->disableOriginalConstructor()
            ->setMethods(array('getMethod'))
            ->getMock();
        $route->expects($this->any())
            ->method('getMethod')
            ->will($this->returnValue('POST'));

        $this->Router->addRoute($route);

        $routes = $this->Router->getRoutes();
        $this->assertCount(1, $routes);
        $this->assertCount(1, $routes['POST']);
        $this->assertEquals($route, $routes['POST'][0]);
    }

    public function testRouteNoRouteForMethod() {
        $this->assertFalse($this->Router->route());
    }

    public function testRouteForMethod() {
        $route = $this->getMockBuilder('LRouter\Models\Route')
        ->disableOriginalConstructor()
        ->setMethods(array('getMethod', 'match', 'getCallable'))
        ->getMock();
        $route->expects($this->any())
            ->method('getMethod')
            ->will($this->returnValue('GET'));
        $route->expects($this->once())
            ->method('match')
            ->will($this->returnValue(array('params' => array())));
        $route->expects($this->once())
            ->method('getCallable')
            ->will($this->returnValue(function () { return 'foo bar'; }));

        $this->Router->addRoute($route);

        $this->assertEquals('foo bar', $this->Router->route());
    }
}
