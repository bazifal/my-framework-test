<?php
/**
 * Created by PhpStorm.
 * User: bazifal
 * Date: 14.11.2017
 * Time: 19:43
 */

namespace core;

/**
 * Class HttpException
 * @package core
 */
class HttpException extends \Exception
{
    /**
     * List of additional headers
     *
     * @var array
     */
    private $headers = [];

    /**
     * Body message
     *
     * @var string
     */
    private $body = '';


    private $status = array(
        400 => 'Bad Request',
        403 => 'Forbidden',
        404 => 'Not Found',
    );

    /**
     * @param int[optional]    $statusCode   If NULL will use 500 as default
     * @param string[optional] $statusPhrase If NULL will use the default status phrase
     * @param array[optional]  $headers      List of additional headers
     */
    public function __construct($statusCode = 500, $statusPhrase = null, array $headers = array())
    {
        if (null === $statusPhrase && isset($this->status[$statusCode])) {
            $statusPhrase = $this->status[$statusCode];
        }
        parent::__construct($statusPhrase, $statusCode);

        $header  = sprintf('HTTP/1.1 %d %s', $statusCode, $statusPhrase);

        $this->addHeader($header);
        $this->addHeaders($headers);
    }

    /**
     * Returns the list of additional headers
     *
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @param string $header
     *
     * @return self
     */
    public function addHeader($header)
    {
        $this->headers[] = $header;

        return $this;
    }

    /**
     * @param array $headers
     *
     * @return self
     */
    public function addHeaders(array $headers)
    {
        foreach ($headers as $key => $header) {
            if (!is_int($key)) {
                $header = $key.': '.$header;
            }

            $this->addHeader($header);
        }

        return $this;
    }

    /**
     * Return the body message.
     *
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Define a body message.
     *
     * @param string $body
     *
     * @return self
     */
    public function setBody($body)
    {
        $this->body = (string) $body;

        return $this;
    }
}