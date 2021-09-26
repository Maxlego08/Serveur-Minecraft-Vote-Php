<?php

namespace ServeurMinecraftVote;

class Webhook
{

    /**
     * Identifiant unique pour le webhook
     *
     * @var string
     */
    private $id;

    /**
     * URL that the webhook will call during an event
     *
     * @var string
     */
    private $endpoint;

    /**
     * Description of the webhook
     *
     * @var string
     */
    private $description;

    /**
     * List of events that the webhook will listen to
     *
     * @var array
     */
    private $events;

    /**
     * Secret key that will be used to send webhooks to the endpoint
     *
     * @var string
     */
    private $secretKey;

    /**
     * @param string $id
     * @param string $endpoint
     * @param string $description
     * @param array $events
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

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getEndpoint(): string
    {
        return $this->endpoint;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return array
     */
    public function getEvents(): array
    {
        return $this->events;
    }

    /**
     * @return string
     */
    public function getSecretKey(): string
    {
        return $this->secretKey;
    }


}
