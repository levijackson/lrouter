<?php

namespace LRouter\Tests;

use PHPUnit_Framework_TestCase,
    LRouter\Models\Route,
    ReflectionClass;

class RouteTest extends PHPUnit_Framework_TestCase {
    public function testConstruct() {
        $route = new \LRouter\Models\Route('/blog/post/:category/:id', 'GET', function () {
            return 'foo';
        });

        $this->assertEquals('/blog/post/:category/:id', $route->getPath());
        $this->assertEquals('GET', $route->getMethod());
        $this->assertEquals('foo', call_user_func($route->getCallable()));
    }

    public function testSetBasePath() {
        $base = '/app/';
        $route = new Route();
        $route->setBasePath($base);
        $this->assertEquals($base, $route->getBasePath());
    }

    public function testSetPath() {
        $path = '/blog/post/:category/:id';
        $route = new Route();
        $route->setPath($path);
        $this->assertEquals($path, $route->getPath());
    }

    public function testSetMethod() {
        $method = 'GET';
        $route = new Route();
        $route->setMethod($method);
        $this->assertEquals($method, $route->getMethod());
    }

    public function testSetName() {
        $name = 'Foo Bar';
        $route = new Route();
        $route->setName($name);
        $this->assertEquals($name, $route->getName());
    }

    public function testSetCallableAnonomyousFunction() {
        $route = new Route();
        $route->setCallable((function () {
            return 'foo bar';
        }));
        $this->assertEquals('foo bar', call_user_func($route->getCallable()));
    }

    public function testGenerateRegex() {
        $route = new Route();
        $route->setPath('/blog/post/:category/:id');
        $reflection = new ReflectionClass($route);
        $property = $reflection->getProperty('Regex');
        $property->setAccessible(true);
        $this->assertEquals('/blog/post/(?P<category>[^/?]+)/(?P<id>[^/?]+)', $property->getValue($route));
    }

    public function testFormatMatches() {
        $route = new Route();
        $reflection = new ReflectionClass($route);
        $method = $reflection->getMethod('formatMatches');
        $method->setAccessible(true);
        $param = array(
            '/foo/path/',
            'foo',
            'baz' => 'bax',
            'bar'
        );
        $expected = array(
            'path' => '/foo/path/',
            'params' => array('baz' => 'bax')
        );
        $this->assertEquals($expected, $method->invokeArgs($route, array($param)));
    }

    public function testMatch() {
        $route = new Route();
        $route->setPath('/blog/post/:category/:id');
        $expected = array(
            'path' => '/blog/post/web/12/',
            'params' => array(
                'category' => 'web',
                'id' => '12'
            )
        );
        $this->assertEquals($expected, $route->match('/blog/post/web/12/'));
        $this->assertEquals($expected, $route->match('/blog/post/web/12/?foo=bar'));
        $this->assertEquals($expected, $route->match('/blog/post/web/12/#identifier'));
        $this->assertEquals($expected, $route->match('/blog/post/web/12/?foo=bar#identifer'));
        
        $expected['path'] = '/blog/post/web/12';
        $this->assertEquals($expected, $route->match('/blog/post/web/12'));
        $this->assertFalse($route->match('/blog/post/web/12/foo/bar'));
        $this->assertFalse($route->match('/blog/post/web/12/foo/bar?foo=bar'));
    }
}
