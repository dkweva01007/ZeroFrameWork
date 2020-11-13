<?php

declare(strict_types=1);

namespace Dkweva01007\ZeroBundle;

use Exception;

define('ROUTE_BUNDLE', 'route_bundle.json');
define('FILE_BUNDLES_CONFIG', realpath($_SERVER['DOCUMENT_ROOT']) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . ROUTE_BUNDLE);
define('FOLDER_BUNDLES_LOCALISATION', realpath($_SERVER['DOCUMENT_ROOT']) . DIRECTORY_SEPARATOR . 'src');

/**
 * a class who role to found all Function Actions, save hers route by Controller and Bundle
 */
class BundleRoute {

    /**
     * @var self|null $_instance 
     */
    private static ?self $_instance = null;

    /**
     * @var string[] First mapping route 
     */
    private array $routingSchema;

    /**
     * @var Action[] final mapping route used
     */
    private array $routing;

    private function __construct() {

        if (!file_exists(FILE_BUNDLES_CONFIG)) {
            throw new Exception('config' . DIRECTORY_SEPARATOR . ROUTE_BUNDLE . ' was not found');
        }
        $this->routingSchema = json_decode(file_get_contents(FILE_BUNDLES_CONFIG), true);
        self::checkJson();
        self::checkDuplicatePathBundle();
        $this->routing = self::initControllerByBundle();
    }

    /**
     * get the instance via initialization
     * 
     * @return self 
     */
    public static function getInstance(): self {

        if (is_null(self::$_instance)) {
            self::$_instance = new BundleRoute();
        }

        return self::$_instance;
    }

    /**
     *  get the routing
     *
     * @return array
     */
    public function getRouting(): array {
        return $this->routing;
    }

    /**
     *  Check if the routing if correct
     *
     * @throws Exception
     * 
     * @return void
     */
    private function checkJson(): void {
        foreach ($this->routingSchema as $bundle_config => $bundle_config_elements) {
            $str_error = "";
            if (sizeof($bundle_config_elements) != 2) {
                $str_error .= 'only 2 parameters expected by Bundle, actually ' . sizeof($bundle_config_elements)
                        . ' parameter(s) found for "' . $bundle_config . '", ';
            }
            if (!array_key_exists("path", $bundle_config_elements)) {
                $str_error .= '\'path\' paramater not found for "' . $bundle_config . '" (exemple format: \'/toto\')\n';
            }

            if (!array_key_exists("bundle", $bundle_config_elements)) {
                $str_error .= '\'bundle\' paramater not found for "' . $bundle_config . '" (exemple format: \'TestBundle\' or \'app' . DIRECTORY_SEPARATOR . 'TestBundle\'),';
            }

            if ($str_error !== "") {
                throw new Exception(nl2br($str_error));
            }
        }
    }

    /**
     *  Check if exist duplicate item
     *
     * @throws Exception
     * 
     * @return void
     */
    private function checkDuplicatePathBundle(string $bundle_name = null) {
        foreach ($this->routingSchema as $bundle_config => &$bundle_config_elements) {
            if ($bundle_name) {
                if (
                        $bundle_name !== $bundle_config &&
                        $bundle_config_elements['path'] === $this->_routingSchema[$bundle_name]['path']
                ) {
                    throw new Exception('path "' . $bundle_config_elements['path'] . '" already used in ' . ROUTE_BUNDLE);
                }
            } else {
                self::checkDuplicatePathBundle($bundle_config);
            }
            $bundle_config_elements['controllers'] = [];
        }
    }

    /**
     *  intialisation of the final routing used
     *
     * @return Action[]
     */
    private function initControllerByBundle(): Array {
        $actions = [];
        //foreach by bundle
        foreach ($this->routingSchema as &$bundle_config_elements) {
            $pathControllersInBundle = FOLDER_BUNDLES_LOCALISATION . DIRECTORY_SEPARATOR . $bundle_config_elements['bundle'] .
                    DIRECTORY_SEPARATOR . 'Controller';
            $bundle_config_elements['controllers'] = [];
            foreach (glob($pathControllersInBundle . DIRECTORY_SEPARATOR . '*Controller.php') as $file) {
                $class = $bundle_config_elements['bundle'] . "\\Controller\\" . basename($file, '.php');
                $bundle_config_elements['controllers'] = $class::getActionRoutes();
                foreach ($class::getActionRoutes() as $controllerAction => &$route) {
                    $actions[] = new Action([
                        'route' => function(Request $p) use ($route) {
                            return $p->getPath() === $route['route'];
                        },
                        'controller' => $class,
                        'controllerAction' => $controllerAction,
                    ]);
                }
            }
        }
        return $actions;
    }

}
