<?php

declare(strict_types=1);

namespace Dkweva01007\ZeroBundle;

use Dkweva01007\ZeroBundle\{
    Controller,
    Request,
    Responses\Response
};

/**
 * A class who role to link between the Controller,
 * the Controller's function of type "Action", the route (path) and the response
 */
class Action {

    /**
     * @var Closure|null  a Anonymous function who check the route (return a bool)
     */
    private ?\Closure $route = null;

    /**
     * @var string|null Controller convert in string
     */
    private ?string $controller = null;

    /**
     * @var string|null ControllerAction convert in string
     */
    private ?string $controllerAction = null;

    /**
     * @param array $parameters [route, controller, controllerAction]
     */
    public function __construct(array $parameters) {
        $this->init($parameters);
    }

    /**
     * Initialisation of the class
     *
     * @param array $parameters  [route, controller, controllerAction]
     */
    public function init(array $parameters): void {
        if (isset($parameters['route'])) {
            $this->setRoute($parameters['route']);
        }

        if (isset($parameters['controller'])) {
            $this->setController($parameters['controller']);
        }

        if (isset($parameters['controllerAction'])) {
            $this->setControllerAction($parameters['controllerAction']);
        }
    }

    /**
     * get Route
     * 
     * @return Closure|null Route
     */
    public function getRoute(): ?\Closure {
        return $this->route;
    }

    /**
     * set Route
     * 
     * @param  Closure|null $parameter
     * @return self
     */
    public function setRoute(?\Closure $parameter): self {
        if (is_callable($parameter) || ($parameter === null)) {
            $this->route = $parameter;
        }
        return $this;
    }

    /**
     * get Controller
     * 
     * @return string|Controller
     */
    public function getController() {
        return $this->controller;
    }
    
    /**
     * set Controller
     * 
     * @param  string|Controller $parameter Controller convert in string
     * @return self
     */
    public function setController(?string $parameter): self {
        if (is_string($parameter) || ($parameter instanceof Controller) || is_callable($parameter) || ($parameter === null)) {
            $this->controller = $parameter;
        }
        return $this;
    }

    /**
     * get ControllerAction
     * 
     * @return string|ControllerAction
     */
    public function getControllerAction() {
        return $this->controllerAction;
    }
    
    /**
     * set ControllerAction
     * 
     * @param  string|ControllerAction $parameter ControllerAction convert in string
     * @return self
     */
    public function setControllerAction(string $parameter): self {
        if (is_string($parameter) || is_callable($parameter) || ($parameter === null)) {
            $this->controllerAction = $parameter;
        }
        return $this;
    }

    /**
     * @param  Request $request
     * @return bool
     *
     * @throws BadFunctionCallException
     */
    public function handleRequest(Request $request): bool {
        if (is_callable($this->route)) {
            $anonymus_function = $this->route;
            $match = $anonymus_function($request);
            if (is_bool($match)) {
                return $match;
            } else {
                throw new BadFunctionCallException('Callable does not return a strict boolean');
            }
        }
        return false;
    }

}
