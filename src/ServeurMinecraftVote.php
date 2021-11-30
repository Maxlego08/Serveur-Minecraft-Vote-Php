<?php

namespace ServeurMinecraftVote;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use ServeurMinecraftVote\Exceptions\SignatureVerificationException;
use ServeurMinecraftVote\Exceptions\WebhookCreateException;

class ServeurMinecraftVote
{

    const DEFAULT_SECONDS_TOLERANCE = 1000 * 5;
    const API_BASE_URL = "https://serveur-minecraft-vote.fr/api/v1";

    /**
     * Secret key to interact with the creation and editing of webhooks
     *
     * @var string
     */
    private $secretKey;

    /**
     * @param string|null $secretKey
     */
    public function __construct(string $secretKey = null)
    {
        $this->secretKey = $secretKey;
    }

    /**
     * Returns the secret key that allows to interact with the API
     *
     * @return null|string the secret key
     */
    public function getSecretKey(): string
    {
        return $this->secretKey;
    }

    /**
     * Allows to check the header of a webhook
     * If the header is valid then you can process the request
     *
     * @param string $data the content sent by server minecraft vote
     * @param string $header the content of the header
     * @param string $secretKey the secret key of the webhook
     * @param int $secondTolerance the tolerance in milliseconds between now and the creation of the request
     * @return bool
     * @throws SignatureVerificationException if the verification fails
     */
    public function verifyHeader(string $data, string $header, string $secretKey, int $secondTolerance = self::DEFAULT_SECONDS_TOLERANCE): bool
    {
        $timestamp = $this->getTimestamp($header);
        $signature = $this->getSignatures($header);

        if ($timestamp === -1)
            throw new SignatureVerificationException(
                "Unable to extract the timestamp from the header.",
                $data,
                $header
            );

        if (empty($signature))
            throw new SignatureVerificationException(
                "Unable to extract the signature from the header.",
                $data,
                $header
            );

        $signedPayload = "{$timestamp}.{$data}";
        $expectedSignature = hash_hmac('sha256', $signedPayload, $secretKey);
        $sameSignature = hash_equals($signature, $expectedSignature);

        if (!$sameSignature) {
            throw new SignatureVerificationException(
                "Unable to verify the signature, the request is invalid.",
                $data,
                $header
            );
        }

        if ($secondTolerance > 0 && abs(time() - $timestamp) > $secondTolerance) {
            throw new SignatureVerificationException(
                "Time limit of the request exceeded",
                $data,
                $header
            );
        }

        return true;
    }

    /**
     * Returns the timestamp contained in the header
     *
     * @param $header
     * @return int|mixed|string
     */
    private function getTimestamp($header)
    {
        $informations = explode('.', $header);
        if (count($informations) === 2)
            return $informations[0];
        return -1;
    }

    /**
     * Returns the signature contained in the header
     *
     * @param $header
     * @return mixed|string|null
     */
    private function getSignatures($header)
    {
        $informations = explode('.', $header);
        if (count($informations) === 2)
            return $informations[1];
        return null;
    }

    /**
     * @throws GuzzleException
     * @throws WebhookCreateException
     */
    public function createWebhook(string $url, array $events, string $description = null): Webhook
    {

        $strExplode = explode('.', $this->secretKey);

        $base64UserId = $strExplode[1];
        $timestamp = time();

        $signedPayload = "{$timestamp}.{$base64UserId}";
        $signature = hash_hmac('sha256', $signedPayload, $this->secretKey);

        $client = new Client();
        $response = $client->post(self::API_BASE_URL . '/webhook/create', [
            'headers' => [
                'Accept' => 'application/json',
                'X-SMV-Signature' => $timestamp . '.' . $signature,
            ],
            'form_params' => [
                'userData' => $base64UserId,
                'url' => $url,
                'events' => $events,
                'description' => $description,
            ],
        ]);
        $json = json_decode((string)$response->getBody(), true);
        if ($json['status'] !== 'error') {
            $events = Event::fromJson($json['events']);
            $webhook = $json['webhook'];
            return new Webhook($webhook['id'], $webhook['url'], $webhook['description'], $events, $webhook['secret_key']);
        }

        throw new WebhookCreateException($json['message']);
    }

    /**
     * Get webhooks list
     *
     * @return Webhook[]
     * @throws GuzzleException
     * @throws WebhookCreateException
     */
    public function getWebhooks(): array
    {
        $strExplode = explode('.', $this->secretKey);

        $base64UserId = $strExplode[1];
        $timestamp = time();

        $signedPayload = "{$timestamp}.{$base64UserId}";
        $signature = hash_hmac('sha256', $signedPayload, $this->secretKey);

        $client = new Client();
        $response = $client->post(self::API_BASE_URL . '/webhook/create', [
            'headers' => [
                'Accept' => 'application/json',
                'X-SMV-Signature' => $timestamp . '.' . $signature,
            ],
            'form_params' => [
                'userData' => $base64UserId,
            ],
        ]);
        $json = json_decode((string)$response->getBody(), true);
        if ($json['status'] !== 'error') {
            $webhooks = [];
            foreach ($json['webhooks'] as $webhook) {
                array_push($webhooks, new Webhook($webhook['id'], $webhook['url'], $webhook['description'], [], $webhook['secret_key']));
            }
            return $webhooks;
        }

        throw new WebhookCreateException($json['message']);
    }

}
