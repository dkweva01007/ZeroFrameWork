<?php

declare(strict_types=1);

namespace Dkweva01007\ZeroBundle;

use Dkweva01007\ZeroBundle\{
    Request,
    Responses\Response
};

/**
 * Class who are the Kernel of Zero Framework
 * 
 * It's the Intel, make the link between Requests, Responses and Controllers 
 * He have the primodial part to send the responds to the clients
 * 
 */
class Kernel {

    /**
     * @var self|null $_instance 
     */
    private static ?self $_instance = null;

    /**
     * @var Request|null $request 
     */
    protected ?Request $request = null;

    /**
     * @var Response|null $response
     */
    protected ?Response $response = null;

    /*
     * get the instance via initialization
     * 
     * @return self 
     */

    public static function getInstance(): self {

        if (is_null(self::$_instance)) {
            $className = __CLASS__;
            self::$_instance = new $className;
        }

        return self::$_instance;
    }

    /**
     * get the Request
     * 
     * @return Request|null 
     */
    public function getRequest(): ?Request {
        return $this->request;
    }

    /**
     * get the Response
     * 
     * @return Response|null 
     */
    public function getResponse(): ?Response {
        return $this->response;
    }

    /**
     * set the Response
     * 
     * @param Response $response 
     */
    public function setResponse(Response $response) {
        $this->response = $response;
    }

    /**
     * get the Action
     * 
     * @return Action|null
     */
    private function SearchActionsController(): ?Action {
        BundleRoute::getInstance();

        foreach (BundleRoute::getInstance()->getRouting() as $action) {
            if ($action->handleRequest($this->request)) {
                return $action;
            }
        }
        return null;
    }

    /**
     * The engine's key for "start your engine"
     * 
     * @return void|null Only if HTTp code[400,404,500]
     */
    public function start(): void {
        $controller = false;
        $scheme = 'http' . (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 's' : '');
        $uri = $scheme . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

        $this->request = new Request($uri);

        if ($this->request->isValid() == false) {
            $this->response = new Response('Invalid request', ['HTTP/1.0 400']);
            $this->response->send();
            return;
        }

        $controller = $this->SearchActionsController();

        if ($controller == null) {
            $this->response = new Response('Unable to manage the request', ['HTTP/1.0 404']);
            $this->response->send();
            return;
        }

        $BundleControllerString = $controller->getController();
        $BundleControllerAction = $controller->getControllerAction();

        $BundleControllerObjet = new $BundleControllerString;
        $BundleControllerObjet->$BundleControllerAction();
        $this->setResponse($BundleControllerObjet->getResponse());

        if (!($this->response instanceof Response)) {
            $this->response = new Response('Invalid response format', ['HTTP/1.0 500']);
        }

        $this->response->send();
    }

}
