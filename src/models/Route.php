<?php

namespace LRouter\Models;

use Closure;

class Route {
    protected $BasePath;
    protected $Path;
    protected $Method;
    protected $Callable;
    protected $Regex;

    public function __construct($path = null, $method = null, Closure $callable = null) {
        if ($path) {
            $this->setPath($path);
        }
        $this->Method = $method;
        $this->Callable = $callable;
    }

    public function setBasePath($basePath) {
        $this->BasePath = $basePath;
        return $this;
    }

    public function getBasePath() {
        return $this->BasePath;
    }

    public function setPath($path) {
        $this->Path = $path;
        $this->generateRegex();
        return $this;
    }

    public function getPath() {
        return $this->Path;
    }

    public function setMethod($method) {
        $this->Method = $method;
        return $this;
    }

    public function getMethod() {
        return $this->Method;
    }

    public function setCallable(Closure $callable) {
        $this->Callable = $callable;
        return $this;
    }

    public function getCallable() {
        return $this->Callable;
    }

    public function setName($name) {
        $this->Name = $name;
        return $this;
    }

    public function getName() {
        return $this->Name;
    }

    protected function generateRegex() {
        $this->Regex = preg_replace_callback('#(:\w+)#i', function ($match) {
            /*
                (?P<id> - creates a group with name 'id'
                    [^/]+ - any non-slash character (so a URL segment)
                )
            */
            return '(?P<' . str_replace(':', '', $match[0]) . '>[^/?]+)';
        }, $this->Path);
    }

    protected function formatMatches(Array $matches) {
        if (empty($matches)) {
            return false;
        }

        $return = array(
            'path' => $matches[0],
            'params' => array()
        );

        foreach ($matches as $key => $value) {
            if (is_numeric($key)) {
                continue;
            }

            $return['params'][$key] = $value;
        }

        return $return;
    }


    public function match($uri) {
        /*
            (?:\/) - allows optional trailing slash
            (?!.+\/) - fails match it finds another URL segment after the last match
            (?:\?) - allows optional query string params
        */
        preg_match('#' . $this->BasePath . $this->Regex . '(?:\/?)(?!.+\/)#i', $uri, $matches);

        return $this->formatMatches($matches);
    }
}