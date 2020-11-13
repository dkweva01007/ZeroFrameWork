<?php

declare(strict_types=1);

namespace Dkweva01007\ZeroBundle;

use Dkweva01007\ZeroBundle\Responses\Response;

/**
 * Astract class for make controllers in Bundle 
 */
abstract class Controller {

    /**
     * @var Response
     */
    private Response $response;

    /**
     * get Response
     * 
     * return Response $p
     */
    public function getResponse(): Response {
        return $this->response;
    }

    /**
     * set Response
     * 
     * @param Response $p
     */
    public function setResponse(Response $p) {
        $this->response = $p;
    }

    /**
     * get all path by function Action in the Controller
     * 
     * @return array
     */
    public static function getActionRoutes(): array {
        //get path of childreen class
        $prefix_path = static::getPath((new \ReflectionClass(get_called_class()))->getdoccomment());
        //get the all Method of children class name
        $class_all_methods = get_class_methods(get_called_class());
        //var_dump($class_all_methods);
        //get all method content in name "Action" at the end
        $class_routes_methods = preg_grep("/Action$/", $class_all_methods);
        $routes = [];
        foreach ($class_routes_methods as $class_route_method) {
            $routes[$class_route_method] = static::getPath(
                            (new \ReflectionClass(get_called_class()))->getMethod($class_route_method)->getdoccomment(),
                            $class_route_method,
                            $prefix_path['route']
            );
        }
        return $routes;
    }

    /**
     * get route function
     * 
     * @param string $comment_path_string Doc's path comment in the target 
     * @param string $class_route_method Description
     * @param string $prefix_path the Controller path
     * 
     * @return array
     */
    private static function getPath(string $comment_path_string, string $class_route_method = null, string $prefix_path = "") {
        $pattern = "#(@Route)\((\"\/\w+\"),\s+(name)=(\"\w+\")\)#";
        preg_match_all($pattern, $comment_path_string, $matches, PREG_PATTERN_ORDER);
        $route['route'] = trim($matches[2][sizeof($matches[2]) - 1], '"') === "/" ?
                $prefix_path . "" : $prefix_path . trim($matches[2][sizeof($matches[2]) - 1], '"');
        if (isset($class_route_method)) {
            if (
                    array_key_exists(3, $matches) &&
                    array_key_exists(4, $matches)
            ) {
                $route['name'] = trim($matches[4][sizeof($matches[4]) - 1], '"');
            } else {
                $route['name'] = $class_route_method;
            }
            $route['route'] = explode('/', trim($route['route'], '/'));
        }
        return $route;
    }

}
