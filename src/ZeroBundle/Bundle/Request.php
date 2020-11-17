<?php

declare(strict_types=1);

namespace ZeroBundle\Bundle;

class Request {
    // composants d'une URL 

    /**
     * @var string 
     */
    private string $scheme = '';

    /**
     * @var string 
     */
    private string $user = '';

    /**
     * @var string 
     */
    private string $pwd = '';

    /**
     * @var string 
     */
    private string $host = '';

    /**
     * @var string 
     */
    private string $port = '';

    /**
     * @var array 
     */
    private array $path = [];

    /**
     * @var string 
     */
    private string $request_method = '';

    /**
     * @var array 
     */
    private array $query = [];

    /**
     * @var string 
     */
    private string $fragment = '';

    /**
     * @var bool 
     */
    private bool $is_ajax = false;

    /**
     * @var bool 
     */
    private bool $is_valid = false;

    /**
     * @param string $url 
     */
    public function __construct(string $url) {
        $this->parse($url);
    }

    /**
     * @return string 
     */
    public function getScheme(): string {
        return $this->scheme;
    }

    /**
     * @return string 
     */
    public function getUser(): string {
        return $this->user;
    }

    /**
     * @return string 
     */
    public function getHost(): string {
        return $this->host;
    }

    /**
     * @return array 
     */
    public function getPath(): array {
        return $this->path;
    }

    /**
     * @return string 
     */
    public function getPort(): string {
        return $this->port;
    }

    /**
     * @return string 
     */
    public function getRequestMethod(): string {
        return $this->request_method;
    }

    /**
     * @return array 
     */
    public function getQuery(): array {
        return $this->query;
    }

    /**
     * @return string 
     */
    public function getFragment(): string {
        return $this->fragment;
    }

    /**
     * @return bool 
     */
    public function isAjax(): bool {
        return $this->is_ajax;
    }

    /**
     * @return bool 
     */
    public function isValid(): bool {
        return $this->is_valid;
    }

    /**
     * Url Parse whith RFC3986
     * 
     * @return void|null 
     */
    private function parse(string $url): void {

        $parts = parse_url($url);

        if ($parts === false) {
            return;
        }

        $this->linkParts($parts);

        $this->is_valid = true;
        
        $this->request_method = $_SERVER["REQUEST_METHOD"];

        $this->is_ajax = strtoupper(
                        $_SERVER['HTTP_X_REQUESTED_WITH'] ?? ''
                ) === 'XMLHTTPREQUEST';
    }

    /**
     * Url Parse whith RFC3986 - part 2
     * 
     * @return void 
     */
    protected function linkParts(array $url_parse): void {
        $this->scheme = $url_parse['scheme'] ?? '';
        $this->host = $url_parse['host'] ?? '';
        $this->port = $url_parse['port'] ?? '';

        $this->user = $url_parse['user'] ?? '';
        $this->pwd = $url_parse['pass'] ?? '';

        $this->path = explode('/', trim($url_parse['path'], '/')) ?? [];

        $this->query = (isset($url_parse['query']) && !empty($_POST['query'])) ?
                parse_str($url_parse['query']) :
                [];

        $this->fragment = $url_parse['fragment'] ?? '';
    }

}