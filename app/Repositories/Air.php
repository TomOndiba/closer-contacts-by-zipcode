<?php

namespace App\Repositories;

class Air {
 
    private $elements = array();
    
    public static function getInstance()
    {
        static $instance = null;
        if (null === $instance) {
            $instance = new static();
        }

        return $instance;
    }
    
    protected function __construct()
    {
    }
    
    private function __clone()
    {
    }
    
    private function __wakeup()
    {
    }
    
    protected function _get($key, $default = null) {
    
        $answer = null;
    
        if( ISSET($this->elements[$key]) ) {
            $answer = $this->elements[$key];
        } else {
            $answer = $default;
            if( is_callable( $answer ) )
            {
                $answer = $answer();
            }
            $this->_put($key, $answer);
            
        }
        return $answer;
        
    }
    
    protected function _put($key, $value) {
    
        $this->elements[$key] = $value;
    
    }
    
    protected function _forget($key) {
    
        UNSET($this->elements[$key]);
    
    }
    
    public static function get($key, $default = null) {
        
        $air = Air::getInstance();
        return $air->_get($key, $default);        
        
    }
    
    public static function put($key, $value) {
    
        $air = Air::getInstance();
        $air->_put($key, $value);
    
    }
    
    public static function forget($key) {
    
        $air = Air::getInstance();
        $air->_forget($key, $value);
    
    }
    
}