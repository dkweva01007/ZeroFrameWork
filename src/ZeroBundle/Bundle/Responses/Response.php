<?php

declare(strict_types=1);

namespace ZeroBundle\Bundle\Responses;

/**
 * Class who manage the response send to the client
 * 
 * Can manage Data and Headers to send
 * 
 */
class Response {

    /**
     * Request's Header
     * 
     * @var string[] 
     */
    protected $headers = [];

    /**
     * Request's Data 
     * 
     * @var mixed|null
     */
    protected $data = null;

    /**
     * @param mixed $data 
     * @param string[] $headers  Array of headers to send first 
     */
    public function __construct($data = null, array $headers = []) {
        $this->data = $data;
        $this->headers = $headers;
    }

    /**
     * Send the Response at the client
     * 
     * @return void
     */
    public function send(): void {
        foreach ($this->headers as $header) {
            header($header);
        }
        echo $this->data ?? '';
    }

}
