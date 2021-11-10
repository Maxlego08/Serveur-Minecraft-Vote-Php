<?php

namespace ServeurMinecraftVote\Exceptions;

use Exception;

class WebhookCreateException extends Exception
{

    public function __construct(string $message)
    {
        parent::__construct($message);
    }

}