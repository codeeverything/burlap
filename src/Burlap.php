<?php

/**
 * A simple Dependency Injection Container inspired by Fabien Potencier's Twitee 
 * and his series on dependency injection.
 * 
 * https://github.com/fabpot/twittee
 * http://fabien.potencier.org/what-is-dependency-injection.html
 * 
 * @version 0.1
 * @author Mike Timms
 */
 
namespace Burlap;

class Burlap {
    /**
     * Contains the configuration of each service
     * 
     * @var array
     */
    public $container = [];
    
    /**
     * Contains the instance of any service that has been set as shared
     * 
     * @var array
     */
    public static $shared = [];
    
    /**
     * Share the service $name with data $data
     * When a shared service is accessed for the first time its result is stored
     * and when accessed again the same result returned without re-running the
     * service's logic
     * 
     * @param string $name - The name of the service
     * @param mixed $data - The result of the service to store
     */
    public function share($name, $data) {
        if (!isset(static::$shared[$name])) {
            static::$shared[$name] = $data;
        }
        
        return static::$shared[$name];
    }
    
    /**
     * Catch calls to undefined functions an if arguments are given store these 
     * in the $this->container array to define a service.
     * If no arguments are given then try to retrieve and run a service, or instance thereof
     * 
     * @param string $name - The name of the service to register or run
     * @param array $args - An array of arguments to be used when defining a service
     */
    public function __call($name, $args) {
        // TODO: Add validaton of args
        
        // set 
        if (count($args) > 0) {
            $this->container[$name] = $args[0];
            return;
        } 
        
        // else, get
        
        // If the service has been shared, then return the stored instance of the result
        if (isset(static::$shared[$name])) {
            return static::$shared[$name];
        }
        
        /**
         * Otherwise read in the service's configuation: this follows the same convention as AngularJS
         * taking a single array argument, the last item of which is expected to be a function. All other
         * items are strings which reference other dependencies that are registered in the container.
         */
        $service = isset($this->container[$name]) ? $this->container[$name] : [];
        
        $callable = array_pop($service);
        
        // load dependencies
        $dependencies = [$this];
        foreach ($service as $dependency) {
            // TOOD: Handle missing dependencies
            $dependencies[] = $this->{$dependency}();
        }
        
        return call_user_func_array($callable, $dependencies);
    }
}