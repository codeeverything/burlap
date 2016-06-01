<?php

/**
 * A simple Dependency Injection Container inspired by Fabien Potencier's Twittee 
 * and his series on dependency injection.
 * 
 * Also implements the ContainerInterface portion of the Container Interopability standard proposed here: 
 * https://github.com/container-interop/container-interop
 * Support for delegate containers is being considered :)
 * 
 * https://github.com/fabpot/twittee
 * http://fabien.potencier.org/what-is-dependency-injection.html
 * 
 * @version 0.2
 * @author Mike Timms
 */
 
namespace Burlap;

use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use Interop\Container\Exception\NotFoundException;

class Burlap implements ContainerInterface {
    /**
     * Contains the configuration of each service
     * 
     * @var array
     */
    public $container = [];
    
    /**
     * Delegate Container
     * A delegate container, from which dependencies should be resolved
     * 
     * @var ContainerInterface
     */
    public $delegate;
    
    /**
     * Contains the instance of any service that has been set as shared
     * 
     * @var array
     */
    public static $shared = [];
    
    /**
     * Constructor for our Burlap sack
     * 
     * @param ContainerInterface|null $delegate - The delegate container object, or NULL. 
     * See https://github.com/container-interop/container-interop/blob/master/docs/Delegate-lookup.md
     * 
     * @return void
     */
    public function __construct(ContainerInterface $delegate = null) {
        $this->delegate = $delegate;
    }
    
    /**
     * Share the service $name with data $data
     * When a shared service is accessed for the first time its result is stored
     * and when accessed again the same result returned without re-running the
     * service's logic
     * 
     * @param string $name - The name of the service
     * @param mixed $data - The result of the service to store
     * @return callable
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
     * @return mixed
     */
    public function __call($name, $args) {
        // set 
        if (count($args) > 0) {
            // TODO: Add validaton of args
            if (!is_array($args[0])) {
                throw new ContainerException('Container takes one argument on definition, and this must be an array');
            }
            
            if (!is_callable(end($args[0]))) {
                throw new ContainerException('Container expects last entry in argument array to be a function');
            }
            
            $this->container[$name] = $args[0];
            return;
        } 
        
        // else, get - backwards compatibility: it's quicker to call $this->get(service)
        
        return $this->get($name);
    }
    
    /**
     * {@inheritdoc}
     */
    public function get($serviceID) {
        if (!$this->has($serviceID)) {
            throw new NotFoundException('Container could not find definition or instance of service "' . $serviceID . '"');
        }
        
        // If the service has been shared, then return the stored instance of the result
        if (isset(static::$shared[$serviceID])) {
            return static::$shared[$serviceID];
        }
        
        /**
         * Otherwise read in the service's configuation: this follows the same convention as AngularJS
         * taking a single array argument, the last item of which is expected to be a function. All other
         * items are strings which reference other dependencies that are registered in the container.
         */
        $service = isset($this->container[$serviceID]) ? $this->container[$serviceID] : [];
        
        $callable = array_pop($service);
        
        if (!is_callable($callable)) {
            throw new ContainerException('Container expects callable in service definition to be callable. For service "' . $name . '" it is not.');
        }
        
        // load dependencies
        $dependencies = [$this];
        
        // get dependencies from the delegate container if there is one, otherwise try within Burlap
        // this is an all or nothing deal, you can't mix and match
        $handler = $this->delegate !== null ? $this->delegate : $this;
        
        foreach ($service as $dependency) {
            // TOOD: Handle missing dependencies
                
            // get dependencies
            $dependencies[] = $handler->get($dependency);
        }
        
        return call_user_func_array($callable, $dependencies);
    }
    
    /**
     * {@inheritdoc}
     */
    public function has($serviceID) {
        return (isset($this->container[$serviceID]) || isset(static::$shared[$service]));
    }
}