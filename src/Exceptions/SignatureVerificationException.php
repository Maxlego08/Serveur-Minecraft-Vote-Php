<?php

namespace ServeurMinecraftVote\Exceptions;

use Exception;

class SignatureVerificationException extends Exception
{

    protected $data;
    protected $header;

    /**
     * @param string $message
     * @param $data
     * @param $header
     */
    public function __construct(string $message, $data, $header)
    {
        parent::__construct($message);
        $this->data = $data;
        $this->header = $header;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param mixed $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * @return mixed
     */
    public function getHeader()
    {
        return $this->header;
    }

    /**
     * @param mixed $header
     */
    public function setHeader($header)
    {
        $this->header = $header;
    }


}
