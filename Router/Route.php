<?php

namespace App\Router;

class Route {

    private $_path; 
    private $_toCallFunction;
    private $_matches = [];
    
    
    public function __construct($path, $toCallFunction) {
        $this->_path = trim($path, '/');
        $this->_toCallFunction = $toCallFunction;
    }

    public function match($url) {
        $url = trim($url, '/');
        $path = preg_replace('#:([\w]+)#', '([^/]+)', $this->_path);
        $regex = "#^$path$#i";
        if(!preg_match($regex, $url, $matches)) {
            return false;
        }
        array_shift($matches);
        $this->_matches = $matches;
        return true;
    } 

    public function call(){
        return call_user_func_array($this->_toCallFunction, $this->_matches);
    }

}