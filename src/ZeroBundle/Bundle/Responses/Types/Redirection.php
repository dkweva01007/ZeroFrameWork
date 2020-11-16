<?php

declare(strict_types=1);

namespace ZeroBundle\Bundle\Responses\Types;

use ZeroBundle\Bundle\Responses\Response;

/**
 * Subclass Response for redirection
 */
class Redirection extends Response {

    /**
     * @param mixed $data 
     * @param array $headers  Array of headers to send first 
     */
    public function __construct($data = null, array $headers = []) {
        parent::__construct($data, $headers);
    }

    /**
     * Send the Response's JSON at the client
     * 
     * @return void
     */
    public function send(): void {
        foreach ($this->headers as $header) {
            header($header);
        }
        header('content-type: application/json');
        echo $this->data;
    }

}
