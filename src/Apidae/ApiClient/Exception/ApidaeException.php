<?php

namespace Apidae\ApiClient\Exception;

use GuzzleHttp\Exception\GuzzleException;

class ApidaeException extends \Exception
{
    protected $request;
    protected $response;

    public function __construct(GuzzleException $e)
    {
        $this->request  = $e->getRequest();
        $this->response = $e->getResponse();
        $simpleMessage  = $e->getMessage();
        $code    = 0;

        $responseDescription = $this->response ? self::readBody($this->response) : 'none';
        $requestDescription = $this->request ? self::readBody($this->request) : 'none';

        if ($this->response) {
            $decodedJson = json_decode($responseDescription, true);
            if ($decodedJson && isset($decodedJson['errorType'])) {
                $simpleMessage = $decodedJson['errorType'].' '.$decodedJson['message'];
            }

            $code = $this->response->getStatusCode();
        }

        $message = sprintf("%s

Request: %s

Response: %s

", $simpleMessage, $requestDescription, $responseDescription);

        parent::__construct($message, $code, $e);
    }

    /**
     * Body may be a non-readable stream (e.g. a write-only 'sink' resource).
     */
    private static function readBody($message)
    {
        try {
            $body = $message->getBody();
            if (!$body->isReadable()) {
                return 'unreadable body';
            }
            if ($body->isSeekable()) {
                $body->rewind();
            }

            return $body->getContents();
        } catch (\RuntimeException $e) {
            return 'unreadable body';
        }
    }
}
