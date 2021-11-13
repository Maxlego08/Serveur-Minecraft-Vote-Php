<?php

namespace ServeurMinecraftVote;

/**
 * @property string $id
 * @property string $endpoint
 * @property string $description
 * @property Event[] $events
 * @property string $secretKey
 */
class Webhook
{

    /**
     * Identifiant unique pour le webhook
     *
     * @var string
     */
    public $id;

    /**
     * URL that the webhook will call during an event
     *
     * @var string
     */
    public $endpoint;

    /**
     * Description of the webhook
     *
     * @var string
     */
    public $description;

    /**
     * List of events that the webhook will listen to
     *
     * @var Event[]
     */
    public $events;

    /**
     * Secret key that will be used to send webhooks to the endpoint
     *
     * @var string
     */
    public $secretKey;

    /**
     * @param string $id
     * @param string $endpoint
     * @param string $description
     * @param Event[] $events
     * @param string $secretKey
     */
    public function __construct(string $id, string $endpoint, string $description, array $events, string $secretKey)
    {
        $this->id = $id;
        $this->endpoint = $endpoint;
        $this->description = $description;
        $this->events = $events;
        $this->secretKey = $secretKey;
    }

}
