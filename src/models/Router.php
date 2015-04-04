<?php

namespace LRouter\Models;

class Router {
    protected $URI;
    protected $Routes = array();
    protected $Method;

    public function __construct($uri = null, $method = null) {
        $this->URI = $uri ?: $_SERVER['REQUEST_URI'];
        $this->Method = $method ?: $_SERVER['REQUEST_METHOD'];
    }

    public function getURI() {
        return $this->URI;
    }

    public function getMethod() {
        return $this->Method;
    }

    public function getRoutes() {
        return $this->Routes;
    }

    public function addRoute(Route $route) {
        $this->Routes[$route->getMethod()][] = $route;
    }

    public function route() {
        if (!isset($this->Routes[$this->Method])) {
            return false;
        }

        foreach ($this->Routes[$this->Method] as $route) {
            if ($match = $route->match($this->URI)) {
                return call_user_func_array($route->getCallable(), $match['params']);
            }
        }

        return false;
    }
}